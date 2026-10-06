<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill;

use BogdanKharchenko\PdfMill\Exceptions\ApiException;
use BogdanKharchenko\PdfMill\Support\Encoder;
use BogdanKharchenko\PdfMill\Support\Fields;
use BogdanKharchenko\PdfMill\Support\JsonMap;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

/**
 * The pdfmill API client. Its endpoint methods are generated from the
 * API's OpenAPI spec (see Endpoints); this class only sends requests.
 *
 * In tests, PdfMill::fake() replaces it with PdfMillFake, a Client that
 * answers without the network.
 */
class Client
{
    use Endpoints;

    public function __construct(
        private readonly Factory $http,
        private readonly string $url,
        private readonly string $key,
        private readonly int $timeout = 120,
        private readonly int $connectTimeout = 10,
    ) {}

    /**
     * POSTs a request body: JSON, or multipart with the JSON in "options"
     * when the body refers to files.
     *
     * @param  array<string, mixed>  $body
     */
    private function post(string $path, array $body, string $accept = 'application/json'): Response
    {
        $encoder = new Encoder;
        $json = json_encode($encoder->encode(new JsonMap(Fields::compact($body))), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $request = $this->request()->accept($accept);
        $uploads = $encoder->uploads();

        if ($uploads === []) {
            return $this->checked($request->withBody($json, 'application/json')->post($path));
        }

        foreach ($uploads as ['name' => $name, 'upload' => $upload]) {
            $request = $request->attach($name, $upload->body(), $upload->filename);
        }

        return $this->checked($request->post($path, ['options' => $json]));
    }

    private function get(string $path, string $accept = '*/*'): Response
    {
        return $this->checked($this->request()->accept($accept)->get($path));
    }

    private function request(): PendingRequest
    {
        return $this->http
            ->baseUrl($this->url)
            ->withToken($this->key)
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout);
    }

    private function checked(Response $response): Response
    {
        if ($response->failed()) {
            throw ApiException::fromResponse($response);
        }

        return $response;
    }

    /**
     * A path parameter such as the key "outputs/abc.pdf": each segment encoded, slashes kept.
     */
    private static function pathParam(string $value): string
    {
        return implode('/', array_map(rawurlencode(...), explode('/', $value)));
    }
}
