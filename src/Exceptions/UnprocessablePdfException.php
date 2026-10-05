<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Exceptions;

/**
 * HTTP 422: not a PDF, a damaged PDF, a wrong or missing password, or an operation the PDF cannot support.
 */
final class UnprocessablePdfException extends ApiException {}
