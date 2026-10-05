<?php

declare(strict_types=1);

/*
 * Regenerates generated/ from openapi.json:  composer generate
 */

use BogdanKharchenko\PdfMill\Generator\Generator;
use BogdanKharchenko\PdfMill\Generator\Spec;

require __DIR__.'/../vendor/autoload.php';

$root = dirname(__DIR__);
$specPath = $root.'/openapi.json';
$files = (new Generator(Spec::load($specPath), (string) hash_file('sha256', $specPath)))->generate();

$target = $root.'/generated';
if (is_dir($target)) {
    $existing = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($existing as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }
}

foreach ($files as $path => $source) {
    $file = $target.'/'.$path;
    if (! is_dir(dirname($file))) {
        mkdir(dirname($file), 0755, true);
    }
    file_put_contents($file, $source);
}

fwrite(STDOUT, sprintf("Generated %d files in generated/\n", count($files)));
