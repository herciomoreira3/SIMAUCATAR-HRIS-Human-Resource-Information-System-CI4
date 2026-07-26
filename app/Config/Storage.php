<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Private object-storage configuration. Keep credentials in the deployment
 * secret store; this class intentionally supplies no credentials or public URL.
 *
 * To enable the S3-compatible adapter, set all of: storage.driver=s3,
 * storage.s3.endpoint, storage.s3.bucket, storage.s3.region,
 * storage.s3.accessKey, and storage.s3.secretKey. An incomplete setting
 * deliberately selects the legacy local adapter instead.
 */
class Storage extends BaseConfig
{
    public string $driver = 'local';
    public string $endpoint = '';
    public string $bucket = '';
    public string $region = 'us-east-1';
    public string $accessKey = '';
    public string $secretKey = '';
    public string $prefix = 'simaucatar';
    public bool $pathStyle = true;
    public int $timeoutSeconds = 15;

    public function __construct()
    {
        parent::__construct();

        $this->driver = (string) env('storage.driver', $this->driver);
        $this->endpoint = (string) env('storage.s3.endpoint', $this->endpoint);
        $this->bucket = (string) env('storage.s3.bucket', $this->bucket);
        $this->region = (string) env('storage.s3.region', $this->region);
        $this->accessKey = (string) env('storage.s3.accessKey', $this->accessKey);
        $this->secretKey = (string) env('storage.s3.secretKey', $this->secretKey);
        $this->prefix = (string) env('storage.s3.prefix', $this->prefix);
        $this->pathStyle = filter_var(env('storage.s3.pathStyle', $this->pathStyle), FILTER_VALIDATE_BOOL);
        $this->timeoutSeconds = max(1, (int) env('storage.s3.timeoutSeconds', $this->timeoutSeconds));
    }
}
