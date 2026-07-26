<?php

namespace App\Services;

use App\Repositories\NavigationRepository;
use CodeIgniter\Cache\CacheInterface;

final class NavigationService
{
    private const TTL = 300;
    private const VERSION_KEY = 'simaucatar:navigation:v4:version';

    public function __construct(private NavigationRepository $repository, private CacheInterface $cache)
    {
    }

    public function forRole(int $roleId): array
    {
        $version = (int) ($this->cache->get(self::VERSION_KEY) ?: 1);
        $key = "simaucatar:navigation:v4:role:{$roleId}:version:{$version}";
        $tree = $this->cache->get($key);
        if (is_array($tree)) {
            return $tree;
        }

        $rows = $this->repository->forRole($roleId);
        $tree = self::buildTree($rows['categories'], $rows['menus'], $rows['submenus']);
        $this->cache->save($key, $tree, self::TTL);
        return $tree;
    }

    public function invalidate(): void
    {
        $version = (int) ($this->cache->get(self::VERSION_KEY) ?: 1);
        $this->cache->save(self::VERSION_KEY, $version + 1, self::TTL);
    }

    public static function buildTree(array $categories, array $menus, array $submenus): array
    {
        $tree = [];
        $menuById = [];
        foreach ($categories as $category) {
            $id = (int) $category['id'];
            $tree[$id] = ['id' => $id, 'menu_category' => $category['menu_category'], 'menus' => []];
        }
        foreach ($menus as $menu) {
            $categoryId = (int) $menu['menu_category'];
            $menuId = (int) $menu['id'];
            if (!isset($tree[$categoryId])) {
                continue;
            }
            $menu['id'] = $menuId;
            $menu['menu_id'] = $menuId;
            $menu['submenus'] = [];
            $tree[$categoryId]['menus'][$menuId] = $menu;
            $menuById[$menuId] = $categoryId;
        }
        foreach ($submenus as $submenu) {
            $menuId = (int) $submenu['menu'];
            if (!isset($menuById[$menuId])) {
                continue;
            }
            $tree[$menuById[$menuId]]['menus'][$menuId]['submenus'][] = $submenu;
        }
        foreach ($tree as &$category) {
            $category['menus'] = array_values($category['menus']);
        }
        unset($category);
        return array_values(array_filter($tree, static fn (array $category): bool => $category['menus'] !== []));
    }
}
