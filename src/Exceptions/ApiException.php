<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Exceptions;

use BogdanKharchenko\PdfLibWorkers\Data\ErrorResponse;
use BogdanKharchenko\PdfLibWorkers\Data\FieldError;
use Illuminate\Http\Client\Response;
use RuntimeException;

/**
 * The API answered with an error. Catch this to handle every API error, or a
 * subclass for one kind. The message names the failing input, e.g.
 * "operations[2] (removePages): Page 9 is out of range".
 */
class ApiException extends RuntimeException
{
    final public function __construct(
        public readonly int $status,
        public readonly ErrorResponse $error,
    ) {
        parent::__construct($error->error, $status);
    }

    public static function fromResponse(Response $response): self
    {
        $data = $response->json();
        $error = is_array($data) && is_string($data['error'] ?? null)
            ? ErrorResponse::from($data)
            : new ErrorResponse(error: "HTTP {$response->status()}: ".self::excerpt($response->body()));

        $class = match ($response->status()) {
            400 => InvalidRequestException::class,
            401 => UnauthorizedException::class,
            404 => NotFoundException::class,
            413 => SourceTooLargeException::class,
            422 => UnprocessablePdfException::class,
            502 => SourceUnavailableException::class,
            504 => SourceTimeoutException::class,
            default => ServerException::class,
        };

        return new $class($response->status(), $error);
    }

    /**
     * The start of a body that isn't an API error, such as a proxy's HTML page, on one line.
     */
    private static function excerpt(string $body): string
    {
        return mb_strimwidth(trim((string) preg_replace('/\s+/', ' ', $body)), 0, 200, '…');
    }

    /**
     * The invalid fields of a 400 response, e.g. path "operations.0.pages".
     *
     * @return list<FieldError>
     */
    public function fieldErrors(): array
    {
        return is_array($this->error->details) ? $this->error->details : [];
    }
}
