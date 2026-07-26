<?php

use App\Services\Storage\LocalStorageAdapter;
use App\Services\Storage\StorageAdapter;
use App\Services\Storage\StorageKey;
use App\Services\Storage\StorageManager;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Storage;

/**
 * @internal
 */
final class StorageManagerTest extends CIUnitTestCase
{
    public function testStorageKeysRejectTraversalAndUnknownCollections(): void
    {
        $this->assertSame('documentu/file-1.pdf', StorageKey::make('documentu', 'file-1.pdf'));

        foreach ([
            static fn() => StorageKey::make('documentu', '../secrets.pdf'),
            static fn() => StorageKey::make('documentu', 'nested/file.pdf'),
            static fn() => StorageKey::assert('documentu/../../secrets.pdf'),
            static fn() => StorageKey::make('other', 'file.pdf'),
        ] as $unsafeKey) {
            try {
                $unsafeKey();
                $this->fail('Unsafe key was accepted.');
            } catch (\InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function testIncompleteS3ConfigurationFallsBackToLocal(): void
    {
        $config = new Storage();
        $config->driver = 's3';
        $config->endpoint = 'https://objects.example.test';
        $config->bucket = 'private-uploads';
        $config->accessKey = '';
        $config->secretKey = '';
        $manager = StorageManager::fromConfig($config, $this->legacyAdapter());

        $this->assertFalse($manager->isObjectStorageActive());
    }

    public function testCompleteS3ConfigurationSelectsPrivateObjectAdapter(): void
    {
        $config = new Storage();
        $config->driver = 's3';
        $config->endpoint = 'https://objects.example.test';
        $config->bucket = 'private-uploads';
        $config->region = 'us-east-1';
        $config->accessKey = 'testing-access-key';
        $config->secretKey = 'testing-secret-key';
        $config->prefix = 'simaucatar';
        $manager = StorageManager::fromConfig($config, $this->legacyAdapter());

        $this->assertTrue($manager->isObjectStorageActive());
    }

    public function testLegacyLocalLayoutCanReadStoredBackup(): void
    {
        $adapter = $this->legacyAdapter();
        $config = new Storage();
        $config->driver = 'local';
        $manager = StorageManager::fromConfig($config, $adapter);

        $manager->putContents('backups', 'backup_20260727_010203.sql', 'SELECT 1;', 'application/sql');

        $this->assertSame('SELECT 1;', $manager->read('backups', 'backup_20260727_010203.sql'));
        $this->assertSame(['backups/backup_20260727_010203.sql'], array_map(static fn($object) => $object->key, $manager->list('backups')));
    }

    public function testLegacyFileIsResolvedWhenPrimaryStorageDoesNotHaveIt(): void
    {
        $legacy = $this->legacyAdapter();
        $legacy->putContents('backups/backup_20260727_010203.sql', 'legacy backup');
        $missingPrimary = new class implements StorageAdapter {
            public function put(string $key, string $sourcePath, ?string $contentType = null): void {}
            public function putContents(string $key, string $contents, ?string $contentType = null): void {}
            public function get(string $key): ?string { return null; }
            public function delete(string $key): void {}
            public function list(string $prefix): array { return []; }
        };
        $manager = new StorageManager($missingPrimary, $legacy);

        $this->assertSame('legacy backup', $manager->read('backups', 'backup_20260727_010203.sql'));
    }

    private function legacyAdapter(): LocalStorageAdapter
    {
        $root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'simaucatar-storage-' . bin2hex(random_bytes(8));
        return new LocalStorageAdapter($root . DIRECTORY_SEPARATOR . 'uploads', $root . DIRECTORY_SEPARATOR . 'backups');
    }
}
