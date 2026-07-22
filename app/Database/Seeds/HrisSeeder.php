<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class HrisSeeder extends Seeder
{
    public function run()
    {
        // 1. Roles (papel)
        if ($this->db->table('papel')->countAllResults() === 0) {
            $this->db->table('papel')->insertBatch([
                ['naran_papel' => 'administrador'],
                ['naran_papel' => 'funsionariu'],
            ]);
        }

        // 2. Initial Admin User (utilizador)
        if ($this->db->table('utilizador')->countAllResults() === 0) {
            $this->db->table('utilizador')->insert([
                'naran_utilizador' => 'admin',
                'xave_secreta'     => password_hash('admin123', PASSWORD_DEFAULT),
                'papel_id'         => 1,
                'estadu_kontu'     => 'Ativu',
                'created_at'       => date('Y-m-d H:i:s'),
            ]);
        }

        // 3. Initial Master Data
        if ($this->db->table('departamentu')->countAllResults() === 0) {
            $this->db->table('departamentu')->insertBatch([
                ['naran_departamentu' => 'Rekursu Umanu (HR)'],
                ['naran_departamentu' => 'Finansas'],
                ['naran_departamentu' => 'TI & Operasaun'],
            ]);
        }

        if ($this->db->table('grau')->countAllResults() === 0) {
            $this->db->table('grau')->insertBatch([
                ['naran_grau' => 'Grau I', 'salariu_baziku' => 500.00, 'created_at' => date('Y-m-d H:i:s')],
                ['naran_grau' => 'Grau II', 'salariu_baziku' => 800.00, 'created_at' => date('Y-m-d H:i:s')],
                ['naran_grau' => 'Grau III', 'salariu_baziku' => 1500.00, 'created_at' => date('Y-m-d H:i:s')],
            ]);
        }

        if ($this->db->table('pozisaun')->countAllResults() === 0) {
            $this->db->table('pozisaun')->insertBatch([
                ['naran_pozisaun' => 'Diretór'],
                ['naran_pozisaun' => 'Xefe Seksaun'],
                ['naran_pozisaun' => 'Staff'],
            ]);
        }

        if ($this->db->table('kategoria')->countAllResults() === 0) {
            $this->db->table('kategoria')->insertBatch([
                ['naran_kategoria' => 'Kategoria A'],
                ['naran_kategoria' => 'Kategoria B'],
                ['naran_kategoria' => 'Kategoria C'],
            ]);
        }

        // --- INTEGRATION WITH EXISTING MENU SYSTEM ---
        // We will create menu categories and menus for the HRIS system

        // Menu Categories
        if ($this->db->table('user_menu_category')->countAllResults() <= 2) {
            $this->db->table('user_menu_category')->insertBatch([
                ['menu_category' => 'DASHBOARD'],
                ['menu_category' => 'MASTER DATA'],
                ['menu_category' => 'HR MANAGEMENT'],
                ['menu_category' => 'SELF SERVICE'],
            ]);
        }

        // Get IDs of newly inserted categories (assuming they start after existing ones)
        // Existing ones are probably 1 (Common Page) and 2 (Settings)
        $catDashboard = 3;
        $catMaster = 4;
        $catHR = 5;
        $catSelf = 6;

        // User Roles for existing system (Mapping)
        // Let's add 'administrador' (2) and 'funsionariu' (3) to user_role
        if ($this->db->table('user_role')->whereIn('role_name', ['administrador', 'funsionariu'])->countAllResults() === 0) {
            $this->db->table('user_role')->insertBatch([
                ['id' => 2, 'role_name' => 'administrador'],
                ['id' => 3, 'role_name' => 'funsionariu'],
            ]);
        }

        // Menus
        if ($this->db->table('user_menu')->whereIn('title', ['Admin Dashboard', 'Diresaun', 'Dashboard'])->countAllResults() === 0) {
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
                    'title'         => 'Diresaun',
                    'url'           => 'administrador/diresaun',
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
                    'title'         => 'Anunsiu',
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
        }

        // Set Access (Manual IDs based on previous insert)
        // Assuming admin menu starts from ID 4
        // Admin Access (Role 2)
        if ($this->db->table('user_access')->where('role_id', 2)->countAllResults() === 0) {
            $adminMenus = range(3, 6); // Categories
            foreach ($adminMenus as $catId) {
                $this->db->table('user_access')->insert(['role_id' => 2, 'menu_category_id' => $catId, 'menu_id' => 0, 'submenu_id' => 0]);
            }
            $adminMenuIds = range(4, 13); // Menu IDs
            foreach ($adminMenuIds as $menuId) {
                $this->db->table('user_access')->insert(['role_id' => 2, 'menu_category_id' => 0, 'menu_id' => $menuId, 'submenu_id' => 0]);
            }
        }

        // Funsionariu Access (Role 3)
        if ($this->db->table('user_access')->where('role_id', 3)->countAllResults() === 0) {
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
}
