<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Copies one page.
 */
final class DuplicatePage extends Data implements Operation
{
    /** Names this step in the operations list: always "duplicatePage". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  int  $page  1-based page to copy; negatives count from the end.
     * @param  int|Optional  $at  1-based position for the copy. Default: right after the original.
     */
    public function __construct(
        public readonly int $page,
        public readonly int|Optional $at = new Optional(),
    ) {
        $this->op = 'duplicatePage';
    }
}
