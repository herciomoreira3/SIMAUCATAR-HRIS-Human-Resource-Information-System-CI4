<?php

namespace App\Services\Storage;

interface StorageAdapter
{
    public function put(string $key, string $sourcePath, ?string $contentType = null): void;

    public function putContents(string $key, string $contents, ?string $contentType = null): void;

    public function get(string $key): ?string;

    public function delete(string $key): void;

    /** @return list<StoredObject> */
    public function list(string $prefix): array;
}
