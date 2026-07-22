<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGrauMenu extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('user_menu') || ! $this->db->tableExists('user_access')) {
            return;
        }

        $masterCategory = $this->db->table('user_menu_category')
            ->where('menu_category', 'MASTER DATA')
            ->get()
            ->getRowArray();

        $adminRole = $this->db->table('user_role')
            ->where('role_name', 'administrador')
            ->get()
            ->getRowArray();

        if ($masterCategory && $adminRole) {
            $this->ensureMenuWithAccess(
                (int) $masterCategory['id'],
                (int) $adminRole['id'],
                'Grau',
                'administrador/grau',
                'award'
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
    }
}
