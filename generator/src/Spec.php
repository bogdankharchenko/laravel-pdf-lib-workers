<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Generator;

use RuntimeException;

/**
 * Read access to the OpenAPI document.
 */
final readonly class Spec
{
    /**
     * @param  array<string, mixed>  $document
     */
    public function __construct(public array $document) {}

    public static function load(string $path): self
    {
        $json = file_get_contents($path) ?: throw new RuntimeException("Cannot read {$path}");

        return new self(json_decode($json, true, flags: JSON_THROW_ON_ERROR));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function components(): array
    {
        return $this->document['components']['schemas'];
    }

    /**
     * @return array<string, mixed>
     */
    public function component(string $name): array
    {
        return $this->components()[$name] ?? throw new RuntimeException("No component schema {$name}");
    }

    /**
     * The component name a schema refers to, if it is a $ref.
     *
     * @param  array<mixed>|bool|null  $schema
     */
    public static function refName(array|bool|null $schema): ?string
    {
        return is_array($schema) && isset($schema['$ref']) ? substr($schema['$ref'], strlen('#/components/schemas/')) : null;
    }

    /**
     * Follows $refs to the schema they point at.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public function resolve(array $schema): array
    {
        while (($name = self::refName($schema)) !== null) {
            $schema = $this->component($name);
        }

        return $schema;
    }

    /**
     * @return array<string, array<string, array<string, mixed>>> path => method => operation
     */
    public function paths(): array
    {
        return $this->document['paths'];
    }

    public function version(): string
    {
        return $this->document['info']['version'];
    }
}
