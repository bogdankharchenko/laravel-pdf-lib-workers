<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Exceptions;

/**
 * HTTP 400: bad JSON or fields (see fieldErrors()), a page out of range, an unknown form field, or a font missing characters.
 */
final class InvalidRequestException extends ApiException {}
