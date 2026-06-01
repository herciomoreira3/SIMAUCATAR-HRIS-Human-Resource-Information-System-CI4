<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDocumentMenus extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('user_menu') || ! $this->db->tableExists('user_access')) {
            return;
        }

        $hrCategory = $this->db->table('user_menu_category')
            ->where('menu_category', 'HR MANAGEMENT')
            ->get()
            ->getRowArray();

        $selfCategory = $this->db->table('user_menu_category')
            ->where('menu_category', 'SELF SERVICE')
            ->get()
            ->getRowArray();

        $adminRole = $this->db->table('user_role')
            ->where('role_name', 'administrador')
            ->get()
            ->getRowArray();

        $employeeRole = $this->db->table('user_role')
            ->where('role_name', 'funsionariu')
            ->get()
            ->getRowArray();

        if ($hrCategory && $adminRole) {
            $this->ensureMenuWithAccess(
                (int) $hrCategory['id'],
                (int) $adminRole['id'],
                'Dokumentu',
                'administrador/documentu',
                'folder'
            );
        }

        if ($selfCategory && $employeeRole) {
            $this->ensureMenuWithAccess(
                (int) $selfCategory['id'],
                (int) $employeeRole['id'],
                'Dokumentu',
                'funsionariu/dokumentu',
                'folder'
            );
        }
    }

    private function ensureMenuWithAccess(int $categoryId, int $roleId, string $title, string $url, string $icon): void
    {
        $menu = $this->db->table('user_menu')->where('url', $url)->get()->getRowArray();

        if (! $menu) {
            $this->db->table('user_menu')->insert([
                'menu_category' => $categoryId,
                'title' => $title,
                'url' => $url,
                'icon' => $icon,
                'parent' => 0,
            ]);
            $menuId = $this->db->insertID();
        } else {
            $menuId = $menu['id'];
        }

        $categoryAccess = $this->db->table('user_access')
            ->where('role_id', $roleId)
            ->where('menu_category_id', $categoryId)
            ->where('menu_id', 0)
            ->where('submenu_id', 0)
            ->countAllResults();

        if ($categoryAccess === 0) {
            $this->db->table('user_access')->insert([
                'role_id' => $roleId,
                'menu_category_id' => $categoryId,
                'menu_id' => 0,
                'submenu_id' => 0,
            ]);
        }

        $menuAccess = $this->db->table('user_access')
            ->where('role_id', $roleId)
            ->where('menu_category_id', 0)
            ->where('menu_id', $menuId)
            ->where('submenu_id', 0)
            ->countAllResults();

        if ($menuAccess === 0) {
            $this->db->table('user_access')->insert([
                'role_id' => $roleId,
                'menu_category_id' => 0,
                'menu_id' => $menuId,
                'submenu_id' => 0,
            ]);
        }
    }

    public function down()
    {
        // Menus are kept to avoid hiding existing document data.
    }
}
