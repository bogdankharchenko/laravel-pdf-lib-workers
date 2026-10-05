<?php

declare(strict_types=1);

/*
 * Copies the API's spec into openapi.json, then run `composer generate`:
 *
 *   composer spec -- ../pdfmill/openapi.json
 *
 * Take it from the pdfmill repo, not from a deployment's /openapi.json:
 * a deployment may run older or newer code, and its address doesn't belong here.
 * The file is copied byte for byte, so the hash in generated/ headers matches the repo's.
 */

function fail(string $message): never
{
    fwrite(STDERR, $message."\n");
    exit(1);
}

$source = $argv[1] ?? fail('Usage: composer spec -- <path or URL of the openapi.json in the pdfmill repo>');
$json = @file_get_contents($source);

if ($json === false) {
    fail("Cannot read {$source}");
}

$spec = json_decode($json, true);

if (! is_array($spec) || ! str_starts_with((string) ($spec['openapi'] ?? ''), '3.1.') || ($spec['info']['title'] ?? null) !== 'pdfmill') {
    fail("{$source} is not the pdfmill OpenAPI 3.1 spec");
}

foreach ($spec['servers'] ?? [] as $server) {
    if (! str_contains((string) ($server['url'] ?? ''), '{')) {
        fail("{$source} comes from a deployment ({$server['url']}). Use the openapi.json in the pdfmill repo.");
    }
}

$target = dirname(__DIR__).'/openapi.json';
$previous = is_file($target) ? json_decode((string) file_get_contents($target), true)['info']['version'] ?? '?' : 'none';
file_put_contents($target, $json);

fwrite(STDOUT, "openapi.json: pdfmill {$previous} → {$spec['info']['version']}. Now run: composer generate\n");
