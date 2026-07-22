<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddManualAttendanceSettings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('attendance_settings', [
            'tama_manual' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
            'sai_manual' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('attendance_settings', ['tama_manual', 'sai_manual']);
    }
}
