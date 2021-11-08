<?php

namespace Jotform;

use Jotform\Exceptions\InvalidKeyException;
use Jotform\Exceptions\JotformException;
use Jotform\Exceptions\ServiceUnavailableException;

class JotformClient
{
    /** @var int */
    public const API_VERSION = 1;

    public const OUTPUT_JSON = 'json';
    public const OUTPUT_XML = 'xml';

    /** @var string */
    private $apiKey;

    /** @var bool */
    private $europeOnly = false;

    /** @var string */
    private $outputType;

    /** @var bool */
    private $debug;

    /** @var JotformResponse */
    public $response;

    public function __construct(string $apiKey, string $outputType = self::OUTPUT_JSON, bool $debug = false)
    {
        $this->apiKey = $apiKey;
        $this->outputType = $outputType;
        $this->debug = $debug;
    }

    public function europeOnly(bool $value): void
    {
        $this->europeOnly = $value;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setOutputType(string $outputType): void
    {
        $this->outputType = $outputType;
    }

    public function getOutputType(): string
    {
        return $this->outputType;
    }

    public function setDebugMode(string $debugMode): void
    {
        $this->debugMode = $debugMode;
    }

    public function getDebugMode(): bool
    {
        return $this->debugMode;
    }

    public function get(string $path, array $params = []): ?array
    {
        return $this->request('get', $path, $params);
    }

    public function post(string $path, array $params = []): ?array
    {
        return $this->request('post', $path, $params);
    }

    public function put(string $path, array $params = []): ?array
    {
        return $this->request('put', $path, $params);
    }

    public function delete(string $path, array $params = []): ?array
    {
        return $this->request('delete', $path, $params);
    }

    public function postJson(string $path, string $params = ''): ?array
    {
        return $this->requestJson('post', $path, $params);
    }

    public function putJson(string $path, string $params = ''): ?array
    {
        return $this->requestJson('put', $path, $params);
    }

    public function deleteJson(string $path, string $params = ''): ?array
    {
        return $this->requestJson('delete', $path, $params);
    }

    protected function request(string $method, string $path, array $params = []): ?array
    {
        return $this->prepareAndSendRequest($method, $path, $params);
    }

    protected function requestJson(string $method, string $path, string $params = ''): ?array
    {
        return $this->prepareAndSendRequest($method, $path, $params);
    }

    /**
     * @param string        $method  Request Method
     * @param string        $path    Request Path/URL
     * @param array|string  $params  Data Array or JSON string
     * @return              array|null
     */
    protected function prepareAndSendRequest(string $method, string $path, $params = []): ?array
    {
        $method = strtoupper($method);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->normalizeRequestUrl($path, $method, $params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JOTFORM_PHP_WRAPPER');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->prepareRequestHeaders($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $statusCode = $info['http_code'];
        curl_close($ch);

        if ($this->debug) {
            // [TODO]
            // var_dump([
            //     'parameters' => $params,
            //     'info' => $info,
            // ]);
        }

        if ($response == false) {
            throw new JotFormException(curl_error($ch), 400);
        }

        $response = $this->outputType === self::OUTPUT_JSON
            ? json_decode($response, true)
            : utf8_decode($response);

        if ($statusCode !== 200) {
            switch ($statusCode) {
                case 400:
                case 403:
                case 404:
                    throw new JotFormException($response["message"] ?? 'Not Found', $statusCode);

                    break;
                case 401:
                    throw new InvalidKeyException();

                    break;
                case 503:
                    throw new ServiceUnavailableException();

                    break;
                default:
                    throw new JotFormException($response["info"] ?? 'Unexpected Error', $statusCode);
            }
        }

        if ($this->outputType === self::OUTPUT_JSON) {
            $response = $response['content'] ?? $response;
        }

        $this->response = new JotformResponse($response, $statusCode, $response["message"] ?? null);
        return $response;
    }

    /**
     * @param  array|string  Data Array or JSON string
     * @return array
     */
    private function prepareRequestHeaders($params): array
    {
        $headers = [
            "APIKEY: {$this->apiKey}",
        ];

        if (is_string($params)) {
            $headers[] = "Content-Type: application/json";
        }

        return $headers;
    }

    /**
     * @param string        $path
     * @param string        $method
     * @param array|string  $params
     * @return              string
     */
    private function normalizeRequestUrl(string $path, string $method, $params): string
    {
        $server = $this->europeOnly ? 'eu-api' : 'api';
        $segments = [
            "https://{$server}.jotform.com",
            'v' . self::API_VERSION,
            $path . ($this->outputType === self::OUTPUT_JSON ? '' : '.xml'),
        ];

        $url = implode('/', $segments);

        return $method === 'GET' && is_array($params) && !empty($params)
            ? "{$url}?" . http_build_query($params)
            : $url;
    }
}
