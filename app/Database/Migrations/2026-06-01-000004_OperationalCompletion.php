<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OperationalCompletion extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('holidays')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'holiday_date' => ['type' => 'DATE'],
                'title' => ['type' => 'VARCHAR', 'constraint' => 150],
                'description' => ['type' => 'TEXT', 'null' => true],
                'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('holiday_date');
            $this->forge->createTable('holidays');
        }

        if (! $this->db->tableExists('document_categories')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'name' => ['type' => 'VARCHAR', 'constraint' => 100],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('name');
            $this->forge->createTable('document_categories');

            foreach (['Kontratu', 'Certidaun', 'Surat', 'Identidade'] as $name) {
                $this->db->table('document_categories')->insert([
                    'name' => $name,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $this->ensureAdminMenu('Rejistu Auditoria', 'administrador/audit', 'list');
        $this->ensureAdminMenu('Manutensaun', 'administrador/maintenance', 'database');
        $this->ensureAdminMenu('Feriadu', 'administrador/feriadu', 'calendar');
        $this->ensureAdminMenu('Balansu Lisensa', 'administrador/lisensa/balansu', 'pie-chart');
    }

    private function ensureAdminMenu(string $title, string $url, string $icon): void
    {
        if (! $this->db->tableExists('user_menu') || ! $this->db->tableExists('user_access')) {
            return;
        }

        $category = $this->db->table('user_menu_category')
            ->where('menu_category', 'HR MANAGEMENT')
            ->get()->getRowArray();
        $role = $this->db->table('user_role')
            ->where('role_name', 'administrador')
            ->get()->getRowArray();

        if (! $category || ! $role) {
            return;
        }

        $menu = $this->db->table('user_menu')->where('url', $url)->get()->getRowArray();
        if (! $menu) {
            $this->db->table('user_menu')->insert([
                'menu_category' => $category['id'],
                'title' => $title,
                'url' => $url,
                'icon' => $icon,
                'parent' => 0,
            ]);
            $menuId = $this->db->insertID();
        } else {
            $menuId = $menu['id'];
        }

        $exists = $this->db->table('user_access')
            ->where('role_id', $role['id'])
            ->where('menu_id', $menuId)
            ->where('menu_category_id', 0)
            ->where('submenu_id', 0)
            ->countAllResults();

        if ($exists === 0) {
            $this->db->table('user_access')->insert([
                'role_id' => $role['id'],
                'menu_category_id' => 0,
                'menu_id' => $menuId,
                'submenu_id' => 0,
            ]);
        }
    }

    public function down()
    {
        // Keep operational tables and menus to preserve user data.
    }
}
