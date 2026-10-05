# Laravel client for pdfmill

Merge, fill, stamp, split and read PDFs from Laravel, using your own deployment of [pdfmill](https://github.com/bogdankharchenko/pdfmill) on Cloudflare Workers.

Every endpoint, operation, option and reply is generated from the API's OpenAPI spec as typed [laravel-data](https://spatie.be/docs/laravel-data) classes. Your IDE and PHPStan know every field, and the docblocks repeat the API's own documentation.

This version targets **pdfmill 0.3.0**. Getting the PDF itself (`file()`, `download()`, or returning it from a route) needs 0.2.0 or later; everything else works from 0.1.0.

## Install

It isn't on Packagist yet, so first add the repository to your app's `composer.json`:

```json
"repositories": [{ "type": "vcs", "url": "https://github.com/bogdankharchenko/laravel-pdfmill" }]
```

Then require it:

```bash
composer require bogdankharchenko/laravel-pdfmill
```

Add your deployment's address and API key to `.env`:

```dotenv
PDFMILL_URL=https://pdfmill.your-subdomain.workers.dev
PDFMILL_KEY=your-api-key
```

`PDFMILL_TIMEOUT` (seconds, default 120) and `PDFMILL_CONNECT_TIMEOUT` (default 10) are optional. To change the config file itself: `php artisan vendor:publish --tag=pdfmill-config`.

## Use

Call the `PdfMill` facade, or inject `BogdanKharchenko\PdfMill\Client`.

### Build a PDF step by step

```php
use BogdanKharchenko\PdfMill\Enums\Position;
use BogdanKharchenko\PdfMill\Facades\PdfMill;
use BogdanKharchenko\PdfMill\Source;

$pdf = PdfMill::edit('templates/contract.pdf')
    ->fillForm(['client' => 'Acme Inc.', 'agree' => true], flatten: true)
    ->watermark(image: Source::disk('branding/logo.png'), position: Position::BottomRight, scale: 0.15, opacity: 1)
    ->pageNumbers(format: 'Page {page} of {total}')
    ->setMetadata(title: 'Contract 42', copyright: '© 2026 Acme Inc.', custom: ['ContractId' => '42'])
    ->store();

$pdf->url; // signed download link, valid for an hour by default
$pdf->key; // R2 key: the source of your next request
```

`create()`, `edit()` and `merge()` return a `PendingPdf`. Each [operation](#operations) is a method on it, and nothing is sent until you end the chain:

| End with | You get |
| --- | --- |
| `->store()`, or `->store('contracts/42.pdf')` | `StoredPdf`: the PDF saved in R2, with `key`, `url`, `expiresAt`, `size` and `pageCount` |
| `->file()` | `FileResponse`: the PDF itself. `->file(storeAs: 'contracts/42.pdf')` also saves it in R2 |
| `->download('contract.pdf')` | A response that makes the browser save it |
| Returning it from a route or controller | The PDF, shown in the browser |

Before that, `->filename('Contract 42.pdf')` names the file, `->linkTtl(86400)` sets how long `store()`'s link works, and `->withoutObjectStreams()` writes a file that old tools can read.

### Show a filled form in the browser

```php
Route::get('/w9', fn () => PdfMill::edit('templates/w9.pdf')
    ->fillForm(['name' => 'Ada Lovelace', 'agree' => true], flatten: true));
```

A `FileResponse` from `->file()` can also be saved to a disk: `->file()->save('w9/ada.pdf', 's3')`.

### Merge uploads with a stored PDF

```php
use BogdanKharchenko\PdfMill\MergeSource;

$pdf = PdfMill::merge([
    MergeSource::file($request->file('contract')),
    MergeSource::file($request->file('id_scan')), // images become pages
    'templates/terms.pdf',                        // an R2 key or a URL
])->store();
```

### Add steps only sometimes

```php
use BogdanKharchenko\PdfMill\Enums\BuiltInFont;
use BogdanKharchenko\PdfMill\PendingPdf;

return PdfMill::create()
    ->drawText("Quote #{$quote->id}", 72, 760, size: 24, font: BuiltInFont::HelveticaBold)
    ->when($quote->isDraft(), fn (PendingPdf $pdf) => $pdf->watermark(text: 'DRAFT'))
    ->download("quote-{$quote->id}.pdf");
```

### Read a PDF

```php
use BogdanKharchenko\PdfMill\Data\InfoResponse;
use BogdanKharchenko\PdfMill\PdfSource;

$info = PdfMill::info('uploads/form.pdf'); // a LockedInfoResponse if it needs a password

if ($info instanceof InfoResponse) {
    foreach ($info->form->fields as $field) {
        echo "{$field->name} ({$field->type->value}): ".json_encode($field->value);
    }
}

$text = PdfMill::text(PdfSource::file($path, password: 'secret'))->pages[0]->text;
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

Each operation is a `PendingPdf` method named after its `op`, run in the order you call them:

- **Pages:** `addPage`, `removePages`, `selectPages`, `duplicatePage`, `rotatePages`, `resizePages`, `cropPages`, `setPageBoxes`, `scalePages`, `translateContent`, `insertPdf`
- **Drawing:** `drawText`, `drawImage`, `drawRectangle`, `drawEllipse`, `drawLine`, `drawSvgPath`, `drawSvg`, `drawPdfPage`, `watermark`, `pageNumbers`
- **Forms:** `fillForm`, `flattenForm`, `addFormField`, `setFieldProperties`, `removeFormFields`, `setFieldScript`
- **Scripts:** `addJavaScript`, `setXFAJavaScript`, `deleteXFA`
- **Document:** `setLayerVisibility`, `setViewerPreferences`, `setMetadata`, `attachFile`, `detachFile`, `convertToPDFA`, `embedFacturX`, `encrypt`

Arguments you leave out are not sent, so the API's defaults apply. An explicit `null` is sent as `null`. Choices are enums in `BogdanKharchenko\PdfMill\Enums`, e.g. `BuiltInFont::HelveticaBold` or `PaperSize::Letter`. The [API's README](https://github.com/bogdankharchenko/pdfmill#operations) describes each operation in full.

Each operation is also a class in `BogdanKharchenko\PdfMill\Operations`, with the same arguments. Build a list of them elsewhere and add it with `->apply(...$operations)`.

## Results

| Method | Returns |
| --- | --- |
| `create()`, `edit()`, `merge()` | A `PendingPdf`; see [Build a PDF step by step](#build-a-pdf-step-by-step) |
| `download($key)` | `FileResponse` for any stored result |
| `info()` | `InfoResponse`, or `LockedInfoResponse` for an encrypted PDF sent without its password |
| `text()`, `extract()`, `scripts()`, `split()`, `measureText()` | `TextResponse`, `ExtractResponse`, `ScriptsResponse`, `SplitResponse`, `MeasureResponse` |

PDFs stored under the default `outputs/` keys are deleted after 7 days if you set up the API's recommended R2 rule.

## Errors

Every API error throws a subclass of `BogdanKharchenko\PdfMill\Exceptions\ApiException`. Each has the HTTP `$status`, the API's reply in `$error`, and a message that names the failing input, e.g. `operations[2] (removePages): Page 9 is out of range`.

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
    config('pdfmill.url').'/pdf/merge' => Http::response([
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
composer spec -- ../pdfmill/openapi.json   # the file in the API repo, not a deployment's /openapi.json
composer generate
```

`composer test` runs the tests, which use replies captured from the real API (`tests/fixtures`) and fail if `generated/` is out of date. `composer analyse` runs PHPStan. To also run against a deployment:

```bash
PDFMILL_LIVE_URL=https://… PDFMILL_LIVE_KEY=… vendor/bin/phpunit --group live
```

## License

MIT
