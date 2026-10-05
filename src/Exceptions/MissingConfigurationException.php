<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Exceptions;

use LogicException;

final class MissingConfigurationException extends LogicException
{
    public static function for(string $setting, string $env): self
    {
        return new self("pdf-lib-workers.{$setting} is not set. Add {$env} to your .env file.");
    }
}
