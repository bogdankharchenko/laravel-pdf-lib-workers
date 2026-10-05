<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Data\Output;
use BogdanKharchenko\PdfMill\Data\StoredPdf;
use BogdanKharchenko\PdfMill\Exceptions\ApiException;
use BogdanKharchenko\PdfMill\Support\Fields;
use Closure;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Traits\Conditionable;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * A PDF being made by create(), edit() or merge(). Add operations with its
 * methods, one per operation (see AddsOperations), then send it:
 *
 *   PdfMill::edit('templates/w9.pdf')->fillForm(['name' => 'Ada'], flatten: true)->store();
 *
 * store() keeps the PDF in R2 and returns its key and link; file() returns the
 * PDF itself; download() makes the browser save it. Returned from a route, it
 * shows the PDF.
 */
final class PendingPdf implements Responsable
{
    use AddsOperations;
    use Conditionable;

    /** @var list<Operation> */
    private array $operations = [];

    private ?string $filename = null;

    private ?int $linkTtl = null;

    private ?bool $useObjectStreams = null;

    /**
     * @param  Closure(array<string, mixed>, string): Response  $send  Posts a body with an Accept header.
     * @param  array<string, mixed>  $fields  The request's other fields, e.g. its source.
     */
    public function __construct(
        private readonly Closure $send,
        private readonly array $fields,
    ) {}

    /**
     * Adds operations made elsewhere, e.g. a list built up in a loop.
     */
    public function apply(Operation ...$operations): static
    {
        array_push($this->operations, ...array_values($operations));

        return $this;
    }

    /**
     * The name offered when the PDF is opened or saved. Default: "document.pdf".
     */
    public function filename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * How long store()'s download link works, in seconds (max 604800 = 7 days).
     */
    public function linkTtl(int $seconds): static
    {
        $this->linkTtl = $seconds;

        return $this;
    }

    /**
     * Writes a classic cross-reference table, for old tools; the file is larger.
     */
    public function withoutObjectStreams(): static
    {
        $this->useObjectStreams = false;

        return $this;
    }

    /**
     * Saves the PDF in R2 and returns its key and a signed download link.
     *
     * @param  string|null  $key  Default: "outputs/<uuid>.pdf", which the recommended R2 rule deletes after 7 days.
     *
     * @throws ApiException
     */
    public function store(?string $key = null): StoredPdf
    {
        return StoredPdf::from(($this->send)($this->body(['key' => $key]), 'application/json')->json());
    }

    /**
     * The PDF itself. It isn't kept in R2 unless you name a key to store it under.
     *
     * @param  string|null  $storeAs  Also save it in R2 under this key; the response then has its key and link.
     *
     * @throws ApiException
     */
    public function file(?string $storeAs = null): FileResponse
    {
        $output = $storeAs === null ? ['store' => false] : ['key' => $storeAs];

        return FileResponse::fromResponse(($this->send)($this->body($output), 'application/pdf'));
    }

    /**
     * A response that makes the browser save the PDF.
     *
     * @throws ApiException
     */
    public function download(?string $filename = null): HttpResponse
    {
        return $this->file()->download($filename);
    }

    /**
     * Shows the PDF in the browser, when returned from a route or controller.
     *
     * @throws ApiException
     */
    public function toResponse($request): HttpResponse
    {
        return $this->file()->toResponse($request);
    }

    /**
     * @param  array<string, mixed>  $output
     * @return array<string, mixed>
     */
    private function body(array $output): array
    {
        $output = Fields::compact([
            ...$output,
            'filename' => $this->filename,
            'linkTtl' => $this->linkTtl,
            'useObjectStreams' => $this->useObjectStreams,
        ]);

        return [
            ...$this->fields,
            'operations' => $this->operations === [] ? null : $this->operations,
            'output' => $output === [] ? null : new Output(...$output),
        ];
    }
}
