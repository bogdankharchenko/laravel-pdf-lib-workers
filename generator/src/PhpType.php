<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Generator;

/**
 * How a schema looks in PHP.
 */
final readonly class PhpType
{
    /**
     * @param  string  $native  Native type, e.g. "string", "float|array", or a FQCN. Never includes null.
     * @param  string  $doc  PHPDoc type with short class names, e.g. "list<string>", "array<string, Font>".
     * @param  string  $json  The JSON type it is read from: string, number, bool, null, array or mixed.
     * @param  list<string>  $uses  Fully qualified classes the type refers to.
     * @param  bool  $nullable  Whether the schema itself allows null.
     * @param  string|null  $listOf  FQCN of the data class, when this is a list of one.
     * @param  string|null  $listOrScalarOf  FQCN of the data class, for "list of it, or a plain value" unions.
     * @param  bool  $map  A JSON object with arbitrary keys (sent as {} when empty).
     * @param  bool  $ambiguous  A union JSON alone can't resolve; fine in requests, not readable from responses.
     */
    public function __construct(
        public string $native,
        public string $doc,
        public string $json,
        public array $uses = [],
        public bool $nullable = false,
        public ?string $listOf = null,
        public ?string $listOrScalarOf = null,
        public bool $map = false,
        public bool $ambiguous = false,
    ) {}

    public static function scalar(string $native, string $json, ?string $doc = null): self
    {
        return new self($native, $doc ?? $native, $json);
    }

    public function withNull(): self
    {
        return new self($this->native, $this->doc, $this->json, $this->uses, true, $this->listOf, $this->listOrScalarOf, $this->map, $this->ambiguous);
    }

    /**
     * The native type with null added where needed, e.g. "?string" or "int|float|null".
     */
    public function nativeOrNull(bool $null): string
    {
        if (! $null || $this->native === 'mixed') {
            return $this->native;
        }

        return str_contains($this->native, '|') ? $this->native.'|null' : '?'.$this->native;
    }

    public function docOrNull(bool $null): string
    {
        return $null && $this->native !== 'mixed' ? $this->doc.'|null' : $this->doc;
    }
}
