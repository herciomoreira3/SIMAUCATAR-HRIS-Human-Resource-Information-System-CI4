<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AttendanceSettings extends Migration
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
            'tama_hahu' => [
                'type' => 'TIME',
                'default' => '08:00:00',
            ],
            'tama_remata' => [
                'type' => 'TIME',
                'default' => '09:00:00',
            ],
            'sai_hahu' => [
                'type' => 'TIME',
                'default' => '17:00:00',
            ],
            'sai_remata' => [
                'type' => 'TIME',
                'default' => '18:00:00',
            ],
            'toleransia_minutu' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 15,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('attendance_settings');

        // Insert default settings
        $this->db->table('attendance_settings')->insert([
            'tama_hahu' => '08:00:00',
            'tama_remata' => '09:00:00',
            'sai_hahu' => '17:00:00',
            'sai_remata' => '18:00:00',
            'toleransia_minutu' => 15,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('attendance_settings');
    }
}
