<?php

namespace App\Repositories;

use CodeIgniter\Database\BaseConnection;

final class NavigationRepository
{
    public function __construct(private BaseConnection $db)
    {
    }

    public function forRole(int $roleId): array
    {
        return [
            // Legacy data can grant a menu/submenu without a matching category
            // row in user_access. Categories are presentation groups, not an
            // authorization boundary; the menu/submenu queries below remain
            // role-filtered. Loading this small reference table once prevents
            // valid menu grants from being dropped from the sidebar.
            'categories' => $this->db->table('user_menu_category')
                ->select('id, menu_category')
                ->orderBy('id', 'ASC')->get()->getResultArray(),
            // Explicit table names avoid alias parsing differences between the
            // local driver and TiDB/MySQLi while preserving the same batched
            // role-scoped reads.
            'menus' => $this->db->table('user_menu')
                ->select('user_menu.id, user_menu.menu_category, user_menu.title, user_menu.url, user_menu.icon, user_menu.parent')
                ->join('user_access', 'user_access.menu_id = user_menu.id')
                ->where('user_access.role_id', $roleId)->orderBy('user_access.id', 'ASC')->get()->getResultArray(),
            'submenus' => $this->db->table('user_submenu')
                ->select('user_submenu.id, user_submenu.menu, user_submenu.title, user_submenu.url')
                ->join('user_access', 'user_access.submenu_id = user_submenu.id')
                ->where('user_access.role_id', $roleId)->orderBy('user_access.id', 'ASC')->get()->getResultArray(),
        ];
    }
}
