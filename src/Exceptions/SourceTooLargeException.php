<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Exceptions;

/**
 * HTTP 413: a URL source is larger than the API allows.
 */
final class SourceTooLargeException extends ApiException {}
