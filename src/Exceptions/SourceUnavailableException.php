<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Exceptions;

/**
 * HTTP 502: a URL source returned an error or could not be reached.
 */
final class SourceUnavailableException extends ApiException {}
