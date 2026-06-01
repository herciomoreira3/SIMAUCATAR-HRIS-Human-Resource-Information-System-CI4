<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropLegacyNotificationsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('notifications')) {
            $this->forge->dropTable('notifications', true);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('notifications')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
                'recipient_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'title' => ['type' => 'VARCHAR', 'constraint' => 255],
                'body' => ['type' => 'TEXT', 'null' => true],
                'url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'read_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('notifications');
        }
    }
}
