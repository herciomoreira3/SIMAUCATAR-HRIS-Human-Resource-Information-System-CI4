<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserManagement extends Migration
{
    public function up()
    {
        $this->forge->createDatabase('starterpanel', true);

        // Create menu categories table
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'menu_category'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ]

        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user_menu_category');

        // Create user menu table
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'menu_category'      => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true
            ],
            'title'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'url'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'icon'       => [
                'type'       => 'TEXT'
            ],
            'parent'       => [
                'type'       => 'TINYINT',
                'constraint' => '1',
            ],

        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user_menu');

        // Create user submenu table
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'menu'      => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true
            ],
            'title'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'url'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ]

        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user_submenu');

        // Create user role table
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role_name'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],

        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user_role');

        // Create users table              
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fullname'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'role' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
            ],
            'created_at' => [
                'type'           => 'datetime'
            ],
            'updated_at' => [
                'type'           => 'datetime'
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');

        // Create user access table
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'role_id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true
            ],
            'menu_category_id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true
            ],
            'menu_id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true
            ],
            'submenu_id'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true
            ]

        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user_access');
    }

    public function down()
    {
        $this->forge->dropTable('user_submenu');
        $this->forge->dropTable('user_menu');
        $this->forge->dropTable('user_menu_category');
        $this->forge->dropTable('user_role');
        $this->forge->dropTable('user_access');
        $this->forge->dropTable('users');
    }
}
