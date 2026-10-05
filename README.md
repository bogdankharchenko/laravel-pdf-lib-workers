# Laravel client for pdf-lib-workers

Merge, fill, stamp, split and read PDFs from Laravel, using your own deployment of [pdf-lib-workers](https://github.com/bogdankharchenko/pdf-lib-workers) on Cloudflare Workers.

Every endpoint, operation, option and reply is generated from the API's OpenAPI spec as typed [laravel-data](https://spatie.be/docs/laravel-data) classes. Your IDE and PHPStan know every field, and the docblocks repeat the API's own documentation.

This version targets **pdf-lib-workers 0.2.2**. The JSON endpoints work with any deployment from 0.1.0; `createFile()`, `editFile()` and `mergeFile()` need 0.2.0 or later.

## Install

```bash
composer require bogdankharchenko/laravel-pdf-lib-workers
```

While the repository is private, first add it to your app's `composer.json`:

```json
"repositories": [{ "type": "vcs", "url": "https://github.com/bogdankharchenko/laravel-pdf-lib-workers" }]
```

Then add your deployment's address and API key to `.env`:

```dotenv
PDF_LIB_WORKERS_URL=https://pdf-lib-workers.your-subdomain.workers.dev
PDF_LIB_WORKERS_KEY=your-api-key
```

`PDF_LIB_WORKERS_TIMEOUT` (seconds, default 120) and `PDF_LIB_WORKERS_CONNECT_TIMEOUT` (default 10) are optional. To change the config file itself: `php artisan vendor:publish --tag=pdf-lib-workers-config`.

## Use

Call the `PdfLib` facade, or inject `BogdanKharchenko\PdfLibWorkers\Client`.

### Fill a form and show it

```php
use BogdanKharchenko\PdfLibWorkers\Facades\PdfLib;
use BogdanKharchenko\PdfLibWorkers\Operations\FillForm;

Route::get('/w9', fn () => PdfLib::editFile('templates/w9.pdf', [
    new FillForm(fields: ['name' => 'Ada Lovelace', 'agree' => true], flatten: true),
]));
```

`editFile()` returns the PDF itself as a `FileResponse`. Returned from a route, it opens in the browser. `->download()` makes the browser save it, and `->save('w9/ada.pdf', 's3')` puts it on a disk.

### Merge uploads with a stored PDF

```php
use BogdanKharchenko\PdfLibWorkers\MergeSource;

$pdf = PdfLib::merge([
    MergeSource::file($request->file('contract')),
    MergeSource::file($request->file('id_scan')), // images become pages
    'templates/terms.pdf',                        // an R2 key or a URL
]);

$pdf->url;       // signed download link, valid for an hour by default
$pdf->key;       // R2 key: use it as the source of the next request
$pdf->pageCount;
```

### Stamp, number and label

```php
use BogdanKharchenko\PdfLibWorkers\Enums\Position;
use BogdanKharchenko\PdfLibWorkers\Operations\PageNumbers;
use BogdanKharchenko\PdfLibWorkers\Operations\SetMetadata;
use BogdanKharchenko\PdfLibWorkers\Operations\Watermark;
use BogdanKharchenko\PdfLibWorkers\Source;

$pdf = PdfLib::edit($pdf->key, [
    new Watermark(image: Source::disk('branding/logo.png'), position: Position::BottomRight, scale: 0.15, opacity: 1),
    new Watermark(text: 'CONFIDENTIAL'),
    new PageNumbers(format: 'Page {page} of {total}'),
    new SetMetadata(
        title: 'Contract 42',
        author: 'Acme Inc.',
        copyright: '© 2026 Acme Inc. All rights reserved.',
        custom: ['MadeFor' => 'Client X', 'ContractId' => '42'],
    ),
]);
```

### Read a PDF

```php
use BogdanKharchenko\PdfLibWorkers\Data\InfoResponse;
use BogdanKharchenko\PdfLibWorkers\PdfSource;

$info = PdfLib::info('uploads/form.pdf'); // a LockedInfoResponse if it needs a password

if ($info instanceof InfoResponse) {
    foreach ($info->form->fields as $field) {
        echo "{$field->name} ({$field->type->value}): ".json_encode($field->value);
    }
}

$text = PdfLib::text(PdfSource::file($path, password: 'secret'))->pages[0]->text;
```

## Sources

Wherever the API takes a file, pass a string (an R2 key, or an http(s) URL) or a source object:

| Source | |
| --- | --- |
| `Source::key('templates/w9.pdf')` | A file in your R2 bucket |
| `Source::url($url, headers: ['authorization' => 'Bearer …'])` | The Worker downloads it |
| `Source::base64($data)` | The bytes, base64-encoded |
| `Source::file($path)` or `Source::file($request->file('pdf'))` | Sent with the request |
| `Source::contents($bytes, 'logo.png')` | Sent with the request |
| `Source::disk('logos/acme.png', 's3')` | Read from a Laravel disk, then sent |

`PdfSource` adds `password` and `preserveXFA`, `MergeSource` adds `pages`, `size` and `margin`, and `FontSource` adds `postscriptName`. Requests that carry files go out as multipart; a source object used twice in one request is uploaded once.

## Operations

`create()`, `edit()` and `merge()` take a list of operations, run in order. Each is a class in `BogdanKharchenko\PdfLibWorkers\Operations`, named after its `op`:

- **Pages:** `AddPage`, `RemovePages`, `SelectPages`, `DuplicatePage`, `RotatePages`, `ResizePages`, `CropPages`, `SetPageBoxes`, `ScalePages`, `TranslateContent`, `InsertPdf`
- **Drawing:** `DrawText`, `DrawImage`, `DrawRectangle`, `DrawEllipse`, `DrawLine`, `DrawSvgPath`, `DrawSvg`, `DrawPdfPage`, `Watermark`, `PageNumbers`
- **Forms:** `FillForm`, `FlattenForm`, `AddFormField`, `SetFieldProperties`, `RemoveFormFields`, `SetFieldScript`
- **Scripts:** `AddJavaScript`, `SetXFAJavaScript`, `DeleteXFA`
- **Document:** `SetLayerVisibility`, `SetViewerPreferences`, `SetMetadata`, `AttachFile`, `DetachFile`, `ConvertToPDFA`, `EmbedFacturX`, `Encrypt`

Arguments you leave out are not sent, so the API's defaults apply. An explicit `null` is sent as `null`. Choices are enums in `BogdanKharchenko\PdfLibWorkers\Enums`, e.g. `BuiltInFont::HelveticaBold` or `PaperSize::Letter`. The [API's README](https://github.com/bogdankharchenko/pdf-lib-workers#operations) describes each operation in full.

## Results

| Method | Returns |
| --- | --- |
| `create()`, `edit()`, `merge()` | `StoredPdf` (`key`, `url`, `expiresAt`, `size`, `pageCount`), or `InlinePdf` (`base64`, `size`, `pageCount`) with `output: new Output(store: false)` |
| `createFile()`, `editFile()`, `mergeFile()` | `FileResponse`: the PDF itself, plus `pageCount`, and `key` and `url` when it was also stored |
| `download($key)` | `FileResponse` for any stored result |
| `info()` | `InfoResponse`, or `LockedInfoResponse` for an encrypted PDF sent without its password |
| `text()`, `extract()`, `scripts()`, `split()`, `measureText()` | `TextResponse`, `ExtractResponse`, `ScriptsResponse`, `SplitResponse`, `MeasureResponse` |

`Output` controls where a PDF goes: `new Output(key: 'invoices/42.pdf', filename: 'Invoice 42.pdf', linkTtl: 86400)`. Results under the default `outputs/` keys are deleted after 7 days if you set up the API's recommended R2 rule.

## Errors

Every API error throws a subclass of `BogdanKharchenko\PdfLibWorkers\Exceptions\ApiException`. Each has the HTTP `$status`, the API's reply in `$error`, and a message that names the failing input, e.g. `operations[2] (removePages): Page 9 is out of range`.

| Exception | Status | When |
| --- | --- | --- |
| `InvalidRequestException` | 400 | Invalid options (`fieldErrors()` lists each field), a page out of range, an unknown form field |
| `UnauthorizedException` | 401 | Wrong or missing API key |
| `NotFoundException` | 404 | No file at an R2 key |
| `SourceTooLargeException` | 413 | A URL source is too large |
| `UnprocessablePdfException` | 422 | Not a PDF, a damaged PDF, or a wrong password |
| `SourceUnavailableException` | 502 | A URL source failed or couldn't be reached |
| `SourceTimeoutException` | 504 | A URL source timed out |
| `ServerException` | 500 and others | The API failed unexpectedly |

## Testing your app

Requests go through Laravel's HTTP client, so `Http::fake()` works:

```php
Http::fake([
    config('pdf-lib-workers.url').'/pdf/merge' => Http::response([
        'key' => 'outputs/test.pdf',
        'url' => 'https://example.test/outputs/test.pdf',
        'expiresAt' => '2026-01-01T00:00:00Z',
        'size' => 1024,
        'pageCount' => 3,
    ]),
]);
```

## Working on this package

Everything in `generated/` is produced from `openapi.json`, a copy of the API's spec, so don't edit it by hand. To update to a new API version:

```bash
composer spec -- ../pdf-lib-workers/openapi.json   # the file in the API repo, not a deployment's /openapi.json
composer generate
```

`composer test` runs the tests, which use replies captured from the real API (`tests/fixtures`) and fail if `generated/` is out of date. `composer analyse` runs PHPStan. To also run against a deployment:

```bash
PDF_LIB_WORKERS_LIVE_URL=https://… PDF_LIB_WORKERS_LIVE_KEY=… vendor/bin/phpunit --group live
```

## License

MIT
