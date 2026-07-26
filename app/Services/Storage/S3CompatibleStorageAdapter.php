<?php

namespace App\Services\Storage;

use InvalidArgumentException;
use RuntimeException;

/** Minimal private S3-compatible adapter using SigV4; it never generates public URLs. */
final class S3CompatibleStorageAdapter implements StorageAdapter
{
    public function __construct(
        private readonly string $endpoint,
        private readonly string $bucket,
        private readonly string $region,
        private readonly string $accessKey,
        private readonly string $secretKey,
        private readonly string $prefix,
        private readonly bool $pathStyle,
        private readonly int $timeoutSeconds,
    ) {
        if (!self::isComplete($endpoint, $bucket, $region, $accessKey, $secretKey, $prefix)) {
            throw new InvalidArgumentException('Incomplete or unsafe S3 storage configuration.');
        }
    }

    public static function isComplete(string $endpoint, string $bucket, string $region, string $accessKey, string $secretKey, string $prefix): bool
    {
        $url = parse_url($endpoint);
        return ($url['scheme'] ?? '') === 'https'
            && !isset($url['user'], $url['pass'], $url['query'], $url['fragment'])
            && (($url['path'] ?? '') === '' || ($url['path'] ?? '') === '/')
            && !empty($url['host'])
            && preg_match('/\A[a-z0-9][a-z0-9.-]{2,62}\z/i', $bucket) === 1
            && preg_match('/\A[a-z0-9-]{2,32}\z/i', $region) === 1
            && trim($accessKey) !== '' && trim($secretKey) !== ''
            && preg_match('/\A[A-Za-z0-9][A-Za-z0-9._-]{0,99}\z/', $prefix) === 1;
    }

    public function put(string $key, string $sourcePath, ?string $contentType = null): void
    {
        if (!is_file($sourcePath) || !is_readable($sourcePath)) {
            throw new RuntimeException('Upload source is unavailable.');
        }
        $contents = file_get_contents($sourcePath);
        if ($contents === false) {
            throw new RuntimeException('Unable to read upload source.');
        }
        $this->putContents($key, $contents, $contentType);
    }

    public function putContents(string $key, string $contents, ?string $contentType = null): void
    {
        StorageKey::assert($key);
        $headers = $contentType ? ['content-type' => $contentType] : [];
        $this->request('PUT', $key, '', $contents, $headers, [200, 201]);
    }

    public function get(string $key): ?string
    {
        StorageKey::assert($key);
        [$status, $body] = $this->request('GET', $key, '', '', [], [200, 404]);
        return $status === 404 ? null : $body;
    }

    public function delete(string $key): void
    {
        StorageKey::assert($key);
        $this->request('DELETE', $key, '', '', [], [204, 404]);
    }

    public function list(string $prefix): array
    {
        $prefix = StorageKey::collectionPrefix(rtrim($prefix, '/'));
        [, $xml] = $this->request('GET', '', 'list-type=2&prefix=' . rawurlencode($this->objectKey($prefix)), '', [], [200]);
        $previous = libxml_use_internal_errors(true);
        $parsed = simplexml_load_string($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        if ($parsed === false) {
            throw new RuntimeException('Object storage returned an invalid listing.');
        }

        $objects = [];
        foreach ($parsed->Contents ?? [] as $item) {
            $fullKey = (string) $item->Key;
            $prefixAt = $this->prefix . '/';
            if (!str_starts_with($fullKey, $prefixAt)) {
                continue;
            }
            $key = substr($fullKey, strlen($prefixAt));
            try {
                StorageKey::assert($key);
                $objects[] = new StoredObject($key, (int) $item->Size, strtotime((string) $item->LastModified) ?: null);
            } catch (\InvalidArgumentException) {
                // Skip objects outside this application's constrained key space.
            }
        }
        return $objects;
    }

    /** @return array{0:int,1:string} */
    private function request(string $method, string $key, string $query, string $body, array $headers, array $accepted): array
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('The cURL extension is required for object storage.');
        }
        $objectKey = $key === '' ? '' : $this->objectKey($key);
        $uri = $this->canonicalUri($objectKey);
        $url = $this->baseUrl() . $uri . ($query === '' ? '' : '?' . $query);
        $payloadHash = hash('sha256', $body);
        $timestamp = gmdate('Ymd\\THis\\Z');
        $date = substr($timestamp, 0, 8);
        $signed = array_change_key_case($headers, CASE_LOWER);
        $host = (string) parse_url($this->baseUrl(), PHP_URL_HOST);
        $port = parse_url($this->baseUrl(), PHP_URL_PORT);
        $signed['host'] = $port === null ? $host : $host . ':' . $port;
        $signed['x-amz-content-sha256'] = $payloadHash;
        $signed['x-amz-date'] = $timestamp;
        ksort($signed);
        $canonicalHeaders = '';
        foreach ($signed as $name => $value) {
            $canonicalHeaders .= $name . ':' . trim((string) $value) . "\n";
        }
        $signedNames = implode(';', array_keys($signed));
        $canonicalRequest = implode("\n", [$method, $uri, $query, $canonicalHeaders, $signedNames, $payloadHash]);
        $scope = $date . '/' . $this->region . '/s3/aws4_request';
        $stringToSign = "AWS4-HMAC-SHA256\n{$timestamp}\n{$scope}\n" . hash('sha256', $canonicalRequest);
        $signature = hash_hmac('sha256', $stringToSign, $this->signingKey($date), false);
        $signed['authorization'] = 'AWS4-HMAC-SHA256 Credential=' . $this->accessKey . '/' . $scope
            . ', SignedHeaders=' . $signedNames . ', Signature=' . $signature;
        $curlHeaders = [];
        foreach ($signed as $name => $value) {
            $curlHeaders[] = $name . ': ' . $value;
        }
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => $this->timeoutSeconds,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_FAILONERROR => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
        ]);
        if ($method === 'PUT') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }
        $response = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        if ($response === false || !in_array($status, $accepted, true)) {
            throw new RuntimeException($response === false ? 'Object storage request failed: ' . $error : 'Object storage request was rejected.');
        }
        return [$status, (string) $response];
    }

    private function objectKey(string $key): string
    {
        return $this->prefix . '/' . ltrim($key, '/');
    }

    private function baseUrl(): string
    {
        $endpoint = rtrim($this->endpoint, '/');
        return $this->pathStyle ? $endpoint . '/' . $this->bucket : preg_replace('/^(https:\/\/)/', '$1' . $this->bucket . '.', $endpoint, 1);
    }

    private function canonicalUri(string $objectKey): string
    {
        return '/' . implode('/', array_map('rawurlencode', array_filter(explode('/', $objectKey), static fn($part) => $part !== '')));
    }

    private function signingKey(string $date): string
    {
        $key = hash_hmac('sha256', $date, 'AWS4' . $this->secretKey, true);
        $key = hash_hmac('sha256', $this->region, $key, true);
        $key = hash_hmac('sha256', 's3', $key, true);
        return hash_hmac('sha256', 'aws4_request', $key, true);
    }
}
