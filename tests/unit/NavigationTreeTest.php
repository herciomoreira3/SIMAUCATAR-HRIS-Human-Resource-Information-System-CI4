<?php

use App\Services\NavigationService;
use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Test\CIUnitTestCase;

final class NavigationTreeTest extends CIUnitTestCase
{
    public function testItKeepsOnlyRowsReachableFromAnAuthorizedCategoryAndMenu(): void
    {
        $tree = NavigationService::buildTree(
            [['id' => 1, 'menu_category' => 'DASHBOARD']],
            [
                ['id' => 10, 'menu_category' => 1, 'title' => 'Parent', 'url' => 'parent', 'icon' => 'menu', 'parent' => 1],
                ['id' => 11, 'menu_category' => 99, 'title' => 'Orphan', 'url' => 'orphan', 'icon' => 'x', 'parent' => 0],
            ],
            [
                ['id' => 20, 'menu' => 10, 'title' => 'Child', 'url' => 'child'],
                ['id' => 21, 'menu' => 99, 'title' => 'Orphan child', 'url' => 'nope'],
            ]
        );

        $this->assertCount(1, $tree);
        $this->assertSame(10, $tree[0]['menus'][0]['menu_id']);
        $this->assertSame('Child', $tree[0]['menus'][0]['submenus'][0]['title']);
    }

    public function testInvalidationBumpsTheSharedNavigationVersion(): void
    {
        $cache = new InMemoryNavigationCache(['simaucatar:navigation:v3:version' => 4]);
        $reflection = new ReflectionClass(NavigationService::class);
        $service = $reflection->newInstanceWithoutConstructor();
        $cacheProperty = $reflection->getProperty('cache');
        $cacheProperty->setValue($service, $cache);

        $service->invalidate();

        $this->assertSame(5, $cache->values['simaucatar:navigation:v3:version']);
        $this->assertSame(300, $cache->ttls['simaucatar:navigation:v3:version']);
    }
}

final class InMemoryNavigationCache implements CacheInterface
{
    public array $values;
    public array $ttls = [];

    public function __construct(array $values = []) { $this->values = $values; }
    public function initialize() {}
    public function get(string $key) { return $this->values[$key] ?? null; }
    public function save(string $key, $value, int $ttl = 60) { $this->values[$key] = $value; $this->ttls[$key] = $ttl; return true; }
    public function delete(string $key) { unset($this->values[$key]); return true; }
    public function increment(string $key, int $offset = 1) { return $this->values[$key] = ((int) ($this->values[$key] ?? 0)) + $offset; }
    public function decrement(string $key, int $offset = 1) { return $this->values[$key] = ((int) ($this->values[$key] ?? 0)) - $offset; }
    public function clean() { $this->values = []; return true; }
    public function getCacheInfo() { return null; }
    public function getMetaData(string $key) { return null; }
    public function isSupported(): bool { return true; }
}
