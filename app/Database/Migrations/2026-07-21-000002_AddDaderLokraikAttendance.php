<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDaderLokraikAttendance extends Migration
{
    public function up()
    {
        // 1. Update attendance_settings to add Dader & Lokraik time columns
        $this->forge->addColumn('attendance_settings', [
            'tama_hahu_dader' => [
                'type' => 'TIME',
                'default' => '08:00:00',
            ],
            'tama_remata_dader' => [
                'type' => 'TIME',
                'default' => '09:00:00',
            ],
            'sai_hahu_dader' => [
                'type' => 'TIME',
                'default' => '12:00:00',
            ],
            'sai_remata_dader' => [
                'type' => 'TIME',
                'default' => '13:00:00',
            ],
            'tama_hahu_lokraik' => [
                'type' => 'TIME',
                'default' => '14:00:00',
            ],
            'tama_remata_lokraik' => [
                'type' => 'TIME',
                'default' => '15:00:00',
            ],
            'sai_hahu_lokraik' => [
                'type' => 'TIME',
                'default' => '17:00:00',
            ],
            'sai_remata_lokraik' => [
                'type' => 'TIME',
                'default' => '18:00:00',
            ],
            'tama_manual_dader' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'sai_manual_dader' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'tama_manual_lokraik' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'sai_manual_lokraik' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);

        // 2. Update prezensa table
        $this->forge->addColumn('prezensa', [
            'oras_tama_dader' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'oras_sai_dader' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'oras_tama_lokraik' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'oras_sai_lokraik' => [
                'type' => 'TIME',
                'null' => true,
            ],
        ]);

        // 3. Remove "Tardi" from estadu_prezensa enum, keep only valid statuses
        $this->forge->modifyColumn('prezensa', [
            'estadu_prezensa' => [
                'type' => 'ENUM',
                'constraint' => ['Prezente', 'Falta', 'Lisensa', 'Loron Sorin', 'Incomplete', 'Holiday', 'Weekend'],
                'default' => 'Incomplete',
                'null' => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('attendance_settings', [
            'tama_hahu_dader', 'tama_remata_dader', 'sai_hahu_dader', 'sai_remata_dader',
            'tama_hahu_lokraik', 'tama_remata_lokraik', 'sai_hahu_lokraik', 'sai_remata_lokraik',
            'tama_manual_dader', 'sai_manual_dader', 'tama_manual_lokraik', 'sai_manual_lokraik',
        ]);
        
        $this->forge->dropColumn('prezensa', [
            'oras_tama_dader', 'oras_sai_dader', 'oras_tama_lokraik', 'oras_sai_lokraik',
        ]);

        // Revert enum to original with "Tardi"
        $this->forge->modifyColumn('prezensa', [
            'estadu_prezensa' => [
                'type' => 'ENUM',
                'constraint' => ['Prezente', 'Tardi', 'Falta', 'Lisensa', 'Incomplete', 'Holiday', 'Weekend'],
                'default' => 'Incomplete',
                'null' => false,
            ],
        ]);
    }
}
