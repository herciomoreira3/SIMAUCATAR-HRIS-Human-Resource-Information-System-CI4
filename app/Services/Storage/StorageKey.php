<?php

namespace App\Services\Storage;

use InvalidArgumentException;

/** Validates every filesystem/object key before an adapter can use it. */
final class StorageKey
{
    /** @var list<string> */
    private const COLLECTIONS = ['perfil', 'lisensa', 'documentu', 'backups'];

    public static function make(string $collection, string $name): string
    {
        if (!in_array($collection, self::COLLECTIONS, true)) {
            throw new InvalidArgumentException('Unknown storage collection.');
        }

        self::assertName($name);

        return $collection . '/' . $name;
    }

    public static function assert(string $key): void
    {
        if (str_contains($key, '\\') || str_starts_with($key, '/') || str_contains($key, "\0")) {
            throw new InvalidArgumentException('Unsafe storage key.');
        }

        $parts = explode('/', $key);
        if (count($parts) !== 2 || !in_array($parts[0], self::COLLECTIONS, true)) {
            throw new InvalidArgumentException('Invalid storage key.');
        }

        self::assertName($parts[1]);
    }

    public static function collectionPrefix(string $collection): string
    {
        if (!in_array($collection, self::COLLECTIONS, true)) {
            throw new InvalidArgumentException('Unknown storage collection.');
        }

        return $collection . '/';
    }

    private static function assertName(string $name): void
    {
        if ($name === '' || strlen($name) > 180 || $name === '.' || $name === '..'
            || !preg_match('/\A[A-Za-z0-9][A-Za-z0-9._-]*\z/', $name)) {
            throw new InvalidArgumentException('Unsafe storage filename.');
        }
    }
}
