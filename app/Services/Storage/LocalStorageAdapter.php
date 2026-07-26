<?php

namespace App\Services\Storage;

use RuntimeException;

/** Legacy local layout adapter; it preserves the existing public/uploads paths. */
final class LocalStorageAdapter implements StorageAdapter
{
    public function __construct(
        private readonly string $uploadsRoot,
        private readonly string $backupsRoot,
    ) {
    }

    public function put(string $key, string $sourcePath, ?string $contentType = null): void
    {
        if (!is_file($sourcePath) || !is_readable($sourcePath)) {
            throw new RuntimeException('Upload source is unavailable.');
        }

        $target = $this->pathFor($key, true);
        if (!copy($sourcePath, $target)) {
            throw new RuntimeException('Unable to persist uploaded file.');
        }
    }

    public function putContents(string $key, string $contents, ?string $contentType = null): void
    {
        $target = $this->pathFor($key, true);
        if (file_put_contents($target, $contents, LOCK_EX) === false) {
            throw new RuntimeException('Unable to persist storage content.');
        }
    }

    public function get(string $key): ?string
    {
        $path = $this->pathFor($key);
        if (!is_file($path)) {
            return null;
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException('Unable to read stored file.');
        }

        return $contents;
    }

    public function delete(string $key): void
    {
        $path = $this->pathFor($key);
        if (is_file($path) && !unlink($path)) {
            throw new RuntimeException('Unable to delete stored file.');
        }
    }

    public function list(string $prefix): array
    {
        $collection = rtrim($prefix, '/');
        $prefix = StorageKey::collectionPrefix($collection);
        $root = $collection === 'backups' ? $this->backupsRoot : $this->uploadsRoot . DIRECTORY_SEPARATOR . $collection;
        if (!is_dir($root)) {
            return [];
        }

        $objects = [];
        foreach (scandir($root) ?: [] as $name) {
            if ($name === '.' || $name === '..' || !is_file($root . DIRECTORY_SEPARATOR . $name)) {
                continue;
            }
            try {
                $key = $prefix . $name;
                StorageKey::assert($key);
                $path = $root . DIRECTORY_SEPARATOR . $name;
                $objects[] = new StoredObject($key, (int) filesize($path), (int) filemtime($path));
            } catch (\InvalidArgumentException) {
                // Ignore legacy names that cannot safely be addressed.
            }
        }

        return $objects;
    }

    private function pathFor(string $key, bool $createParent = false): string
    {
        StorageKey::assert($key);
        [$collection, $name] = explode('/', $key, 2);
        $directory = $collection === 'backups'
            ? $this->backupsRoot
            : $this->uploadsRoot . DIRECTORY_SEPARATOR . $collection;

        if ($createParent && !is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Unable to create storage directory.');
        }

        return $directory . DIRECTORY_SEPARATOR . $name;
    }
}
