<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Exceptions;

/**
 * HTTP 500 or another status: the API is misconfigured or failed unexpectedly.
 */
class ServerException extends ApiException {}
