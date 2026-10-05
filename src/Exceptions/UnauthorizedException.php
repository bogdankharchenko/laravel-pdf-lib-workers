<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Exceptions;

/**
 * HTTP 401: the API key is missing or wrong.
 */
final class UnauthorizedException extends ApiException {}
