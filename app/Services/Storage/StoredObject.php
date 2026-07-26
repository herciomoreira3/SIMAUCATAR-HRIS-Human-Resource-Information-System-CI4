<?php

namespace App\Services\Storage;

final class StoredObject
{
    public function __construct(
        public readonly string $key,
        public readonly int $size,
        public readonly ?int $modifiedAt = null,
    ) {
    }
}
