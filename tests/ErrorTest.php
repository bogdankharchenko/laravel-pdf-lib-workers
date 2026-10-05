<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Tests;

use BogdanKharchenko\PdfLibWorkers\Data\FieldError;
use BogdanKharchenko\PdfLibWorkers\Exceptions\ApiException;
use BogdanKharchenko\PdfLibWorkers\Exceptions\InvalidRequestException;
use BogdanKharchenko\PdfLibWorkers\Exceptions\NotFoundException;
use BogdanKharchenko\PdfLibWorkers\Exceptions\ServerException;
use BogdanKharchenko\PdfLibWorkers\Exceptions\SourceTimeoutException;
use BogdanKharchenko\PdfLibWorkers\Exceptions\SourceTooLargeException;
use BogdanKharchenko\PdfLibWorkers\Exceptions\SourceUnavailableException;
use BogdanKharchenko\PdfLibWorkers\Exceptions\UnauthorizedException;
use BogdanKharchenko\PdfLibWorkers\Exceptions\UnprocessablePdfException;
use BogdanKharchenko\PdfLibWorkers\Facades\PdfLib;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;

final class ErrorTest extends TestCase
{
    /**
     * @return iterable<string, array{string, class-string<ApiException>, int, string}>
     */
    public static function capturedErrors(): iterable
    {
        yield 'bad operation' => ['error-400', InvalidRequestException::class, 400, 'operations[0] (removePages): Page 9 is out of range (document has 2 pages)'];
        yield 'wrong key' => ['error-401', UnauthorizedException::class, 401, 'Missing or invalid API key'];
        yield 'missing file' => ['error-404', NotFoundException::class, 404, 'No file at key "missing/file.pdf"'];
        yield 'not a pdf' => ['error-422', UnprocessablePdfException::class, 422, 'Not a PDF (starts with "not a pdf")'];
    }

    /**
     * @param  class-string<ApiException>  $class
     */
    #[DataProvider('capturedErrors')]
    public function test_throws_the_exception_for_a_real_error(string $fixture, string $class, int $status, string $message): void
    {
        $this->respondWith($fixture);

        $error = $this->catch(fn () => PdfLib::info('in.pdf'));

        $this->assertInstanceOf($class, $error);
        $this->assertSame($status, $error->status);
        $this->assertSame($status, $error->getCode());
        $this->assertSame($message, $error->getMessage());
        $this->assertSame($message, $error->error->error);
    }

    /**
     * @return iterable<int, array{int, class-string<ApiException>}>
     */
    public static function statuses(): iterable
    {
        yield [413, SourceTooLargeException::class];
        yield [500, ServerException::class];
        yield [502, SourceUnavailableException::class];
        yield [503, ServerException::class];
        yield [504, SourceTimeoutException::class];
    }

    /**
     * @param  class-string<ApiException>  $class
     */
    #[DataProvider('statuses')]
    public function test_throws_an_exception_per_status(int $status, string $class): void
    {
        Http::fake(['*' => Http::response(['error' => 'sources[0]: failed'], $status)]);

        $error = $this->catch(fn () => PdfLib::info('https://files.test/in.pdf'));

        $this->assertInstanceOf($class, $error);
        $this->assertSame('sources[0]: failed', $error->getMessage());
    }

    public function test_lists_the_invalid_fields(): void
    {
        $this->respondWith('error-400-validation');

        $error = $this->catch(fn () => PdfLib::info('in.pdf'));

        $this->assertInstanceOf(InvalidRequestException::class, $error);
        $this->assertSame('Invalid request', $error->error->error);
        $this->assertStringStartsWith("Invalid request: operations.0.op: Invalid discriminator value. Expected 'addPage' | ", $error->getMessage());

        $fields = $error->fieldErrors();
        $this->assertNotEmpty($fields);
        $this->assertContainsOnlyInstancesOf(FieldError::class, $fields);
        $this->assertSame('operations.0.op', $fields[0]->path);
        $this->assertStringContainsString("Expected 'addPage'", $fields[0]->message);
    }

    public function test_names_every_invalid_field_in_the_message(): void
    {
        Http::fake(['*' => Http::response(['error' => 'Invalid request', 'details' => [
            ['path' => 'operations.0.x', 'message' => 'Expected number, received string'],
            ['path' => '', 'message' => 'Unrecognized key: "colour"'],
        ]], 400)]);

        $error = $this->catch(fn () => PdfLib::info('in.pdf'));

        $this->assertSame('Invalid request: operations.0.x: Expected number, received string; Unrecognized key: "colour"', $error->getMessage());
    }

    public function test_keeps_details_given_as_text(): void
    {
        Http::fake(['*' => Http::response(['error' => 'Invalid request', 'details' => 'Body is not JSON'], 400)]);

        $error = $this->catch(fn () => PdfLib::info('in.pdf'));

        $this->assertSame('Body is not JSON', $error->error->details);
        $this->assertSame([], $error->fieldErrors());
    }

    public function test_describes_an_error_that_is_not_json(): void
    {
        Http::fake(['*' => Http::response("<html>\n  <title>502 Bad Gateway</title>\n</html>\n", 502)]);

        $error = $this->catch(fn () => PdfLib::info('in.pdf'));

        $this->assertInstanceOf(SourceUnavailableException::class, $error);
        $this->assertSame('HTTP 502: <html> <title>502 Bad Gateway</title> </html>', $error->getMessage());
        $this->assertNull($error->error->details);
    }

    public function test_shortens_a_long_error_that_is_not_json(): void
    {
        Http::fake(['*' => Http::response(str_repeat('x', 500), 500)]);

        $error = $this->catch(fn () => PdfLib::info('in.pdf'));

        $this->assertSame('HTTP 500: '.str_repeat('x', 199).'…', $error->getMessage());
    }

    private function catch(callable $call): ApiException
    {
        try {
            $call();
        } catch (ApiException $error) {
            return $error;
        }

        $this->fail('Expected an ApiException');
    }
}
