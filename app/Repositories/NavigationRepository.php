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
            'menus' => $this->db->table('user_menu m')
                ->select('m.id, m.menu_category, m.title, m.url, m.icon, m.parent')
                ->join('user_access a', 'a.menu_id = m.id')
                ->where('a.role_id', $roleId)->orderBy('a.id', 'ASC')->get()->getResultArray(),
            'submenus' => $this->db->table('user_submenu sm')
                ->select('sm.id, sm.menu, sm.title, sm.url')
                ->join('user_access a', 'a.submenu_id = sm.id')
                ->where('a.role_id', $roleId)->orderBy('a.id', 'ASC')->get()->getResultArray(),
        ];
    }
}
