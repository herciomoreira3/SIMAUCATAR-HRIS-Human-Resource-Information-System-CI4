<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTipuLisensaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_tipu' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tipu_lisensa');

        // Seed default leave types
        $this->db->table('tipu_lisensa')->insertBatch([
            ['naran_tipu' => 'Moras',       'created_at' => date('Y-m-d H:i:s')],
            ['naran_tipu' => 'Anuál',       'created_at' => date('Y-m-d H:i:s')],
            ['naran_tipu' => 'Maternidade', 'created_at' => date('Y-m-d H:i:s')],
            ['naran_tipu' => 'Lutu',        'created_at' => date('Y-m-d H:i:s')],
            ['naran_tipu' => 'Seluk',       'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tipu_lisensa');
    }
}
