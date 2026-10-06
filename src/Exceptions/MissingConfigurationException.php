<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Exceptions;

use LogicException;

class MissingConfigurationException extends LogicException
{
    public static function for(string $setting, string $env): self
    {
        return new self("pdfmill.{$setting} is not set. Add {$env} to your .env file.");
    }
}
