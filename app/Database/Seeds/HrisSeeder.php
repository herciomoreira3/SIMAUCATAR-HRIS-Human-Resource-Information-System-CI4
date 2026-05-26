<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class HrisSeeder extends Seeder
{
    public function run()
    {
        // 1. Roles (papel)
        $this->db->table('papel')->insertBatch([
            ['naran_papel' => 'administrador'],
            ['naran_papel' => 'funsionariu'],
        ]);

        // 2. Initial Admin User (utilizador)
        $this->db->table('utilizador')->insert([
            'naran_utilizador' => 'admin',
            'xave_secreta'     => password_hash('admin123', PASSWORD_DEFAULT),
            'papel_id'         => 1,
            'estadu_kontu'     => 'Ativu',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        // 3. Initial Master Data
        $this->db->table('departamentu')->insertBatch([
            ['naran_departamentu' => 'Rekursu Umanu (HR)'],
            ['naran_departamentu' => 'Finansas'],
            ['naran_departamentu' => 'TI & Operasaun'],
        ]);

        $this->db->table('pozisaun')->insertBatch([
            ['naran_pozisaun' => 'Diretór', 'salariu_baziku' => 1500.00],
            ['naran_pozisaun' => 'Xefe Seksaun', 'salariu_baziku' => 800.00],
            ['naran_pozisaun' => 'Staff', 'salariu_baziku' => 500.00],
        ]);

        $this->db->table('kategoria')->insertBatch([
            ['naran_kategoria' => 'Kategoria A'],
            ['naran_kategoria' => 'Kategoria B'],
            ['naran_kategoria' => 'Kategoria C'],
        ]);

        // --- INTEGRATION WITH EXISTING MENU SYSTEM ---
        // We will create menu categories and menus for the HRIS system

        // Menu Categories
        $this->db->table('user_menu_category')->insertBatch([
            ['menu_category' => 'DASHBOARD'],
            ['menu_category' => 'MASTER DATA'],
            ['menu_category' => 'HR MANAGEMENT'],
            ['menu_category' => 'SELF SERVICE'],
        ]);

        // Get IDs of newly inserted categories (assuming they start after existing ones)
        // Existing ones are probably 1 (Common Page) and 2 (Settings)
        $catDashboard = 3;
        $catMaster = 4;
        $catHR = 5;
        $catSelf = 6;

        // User Roles for existing system (Mapping)
        // Let's add 'administrador' (2) and 'funsionariu' (3) to user_role
        $this->db->table('user_role')->insertBatch([
            ['id' => 2, 'role_name' => 'administrador'],
            ['id' => 3, 'role_name' => 'funsionariu'],
        ]);

        // Menus
        $this->db->table('user_menu')->insertBatch([
            // Admin Menus
            [
                'menu_category' => $catDashboard,
                'title'         => 'Admin Dashboard',
                'url'           => 'administrador/dashboard',
                'icon'          => 'grid',
                'parent'        => 0
            ],
            [
                'menu_category' => $catMaster,
                'title'         => 'Departamentu',
                'url'           => 'administrador/departamentu',
                'icon'          => 'layers',
                'parent'        => 0
            ],
            [
                'menu_category' => $catMaster,
                'title'         => 'Pozisaun',
                'url'           => 'administrador/pozisaun',
                'icon'          => 'briefcase',
                'parent'        => 0
            ],
            [
                'menu_category' => $catMaster,
                'title'         => 'Kategoria',
                'url'           => 'administrador/kategoria',
                'icon'          => 'tag',
                'parent'        => 0
            ],
            [
                'menu_category' => $catHR,
                'title'         => 'Funsionáriu',
                'url'           => 'administrador/funsionariu',
                'icon'          => 'users',
                'parent'        => 0
            ],
            [
                'menu_category' => $catHR,
                'title'         => 'Prezensa',
                'url'           => 'administrador/prezensa',
                'icon'          => 'calendar',
                'parent'        => 0
            ],
            [
                'menu_category' => $catHR,
                'title'         => 'Lisensa',
                'url'           => 'administrador/lisensa',
                'icon'          => 'file-text',
                'parent'        => 0
            ],
            [
                'menu_category' => $catHR,
                'title'         => 'Saláriu',
                'url'           => 'administrador/salariu',
                'icon'          => 'dollar-sign',
                'parent'        => 0
            ],
            [
                'menu_category' => $catHR,
                'title'         => 'Avizu',
                'url'           => 'administrador/avizu',
                'icon'          => 'bell',
                'parent'        => 0
            ],
            [
                'menu_category' => $catHR,
                'title'         => 'Sansaun',
                'url'           => 'administrador/sansaun',
                'icon'          => 'alert-triangle',
                'parent'        => 0
            ],
            // Employee Menus
            [
                'menu_category' => $catDashboard,
                'title'         => 'Dashboard',
                'url'           => 'funsionariu/dashboard',
                'icon'          => 'home',
                'parent'        => 0
            ],
            [
                'menu_category' => $catSelf,
                'title'         => 'Prezensa',
                'url'           => 'funsionariu/prezensa',
                'icon'          => 'clock',
                'parent'        => 0
            ],
            [
                'menu_category' => $catSelf,
                'title'         => 'Lisensa',
                'url'           => 'funsionariu/lisensa',
                'icon'          => 'send',
                'parent'        => 0
            ],
            [
                'menu_category' => $catSelf,
                'title'         => 'Saláriu',
                'url'           => 'funsionariu/salariu',
                'icon'          => 'file',
                'parent'        => 0
            ],
            [
                'menu_category' => $catSelf,
                'title'         => 'Perfil',
                'url'           => 'funsionariu/perfil',
                'icon'          => 'user',
                'parent'        => 0
            ],
        ]);

        // Set Access (Manual IDs based on previous insert)
        // Assuming admin menu starts from ID 4
        // Admin Access (Role 2)
        $adminMenus = range(3, 6); // Categories
        foreach ($adminMenus as $catId) {
            $this->db->table('user_access')->insert(['role_id' => 2, 'menu_category_id' => $catId, 'menu_id' => 0, 'submenu_id' => 0]);
        }
        $adminMenuIds = range(4, 13); // Menu IDs
        foreach ($adminMenuIds as $menuId) {
            $this->db->table('user_access')->insert(['role_id' => 2, 'menu_category_id' => 0, 'menu_id' => $menuId, 'submenu_id' => 0]);
        }

        // Funsionariu Access (Role 3)
        $funsionariuMenus = [3, 6]; // Categories
        foreach ($funsionariuMenus as $catId) {
            $this->db->table('user_access')->insert(['role_id' => 3, 'menu_category_id' => $catId, 'menu_id' => 0, 'submenu_id' => 0]);
        }
        $funsionariuMenuIds = range(14, 18); // Menu IDs
        foreach ($funsionariuMenuIds as $menuId) {
            $this->db->table('user_access')->insert(['role_id' => 3, 'menu_category_id' => 0, 'menu_id' => $menuId, 'submenu_id' => 0]);
        }
    }
}
