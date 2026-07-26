<?php

namespace App\Services\Storage;

use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Storage;

/** Selects private object storage only when its complete configuration is present. */
final class StorageManager
{
    public function __construct(
        private readonly StorageAdapter $primary,
        private readonly LocalStorageAdapter $legacy,
    ) {
    }

    public static function fromConfig(?Storage $config = null, ?LocalStorageAdapter $legacy = null): self
    {
        $config ??= config('Storage');
        $legacy ??= new LocalStorageAdapter(FCPATH . 'uploads', WRITEPATH . 'backups');
        $primary = $legacy;
        if (strtolower($config->driver) === 's3' && S3CompatibleStorageAdapter::isComplete(
            $config->endpoint, $config->bucket, $config->region, $config->accessKey, $config->secretKey, $config->prefix,
        )) {
            $primary = new S3CompatibleStorageAdapter(
                $config->endpoint, $config->bucket, $config->region, $config->accessKey, $config->secretKey,
                $config->prefix, $config->pathStyle, $config->timeoutSeconds,
            );
        }
        return new self($primary, $legacy);
    }

    public function putUpload(string $collection, string $name, UploadedFile $file): string
    {
        $key = StorageKey::make($collection, $name);
        $this->primary->put($key, $file->getTempName(), $file->getClientMimeType());
        // Existing views use /uploads directly. Keep that legacy read path alive
        // while the object copy becomes the durable source for later controller reads.
        if ($this->primary !== $this->legacy) {
            $this->legacy->put($key, $file->getTempName(), $file->getClientMimeType());
        }
        return $key;
    }

    public function putContents(string $collection, string $name, string $contents, ?string $contentType = null): string
    {
        $key = StorageKey::make($collection, $name);
        $this->primary->putContents($key, $contents, $contentType);
        return $key;
    }

    public function read(string $collection, string $name): ?string
    {
        $key = StorageKey::make($collection, $name);
        $contents = $this->primary->get($key);
        return $contents ?? ($this->primary === $this->legacy ? null : $this->legacy->get($key));
    }

    public function delete(string $collection, string $name): void
    {
        $key = StorageKey::make($collection, $name);
        $this->primary->delete($key);
        if ($this->primary !== $this->legacy) {
            $this->legacy->delete($key);
        }
    }

    /** @return list<StoredObject> */
    public function list(string $collection): array
    {
        $prefix = StorageKey::collectionPrefix($collection);
        $objects = [];
        foreach ($this->primary === $this->legacy ? [$this->legacy] : [$this->legacy, $this->primary] as $adapter) {
            foreach ($adapter->list($prefix) as $object) {
                $objects[$object->key] = $object;
            }
        }
        return array_values($objects);
    }

    public function isObjectStorageActive(): bool
    {
        return $this->primary !== $this->legacy;
    }
}
