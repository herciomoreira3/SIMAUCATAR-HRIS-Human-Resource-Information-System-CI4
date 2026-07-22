<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLoronSorinStatus extends Migration
{
    public function up()
    {
        // Modify the estadu_prezensa column to add 'Loron Sorin'
        $this->forge->modifyColumn('prezensa', [
            'estadu_prezensa' => [
                'type'       => 'ENUM',
                'constraint' => ['Prezente', 'Tardi', 'Falta', 'Lisensa', 'Incomplete', 'Holiday', 'Weekend', 'Loron Sorin'],
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        // Revert back to original enum values
        $this->forge->modifyColumn('prezensa', [
            'estadu_prezensa' => [
                'type'       => 'ENUM',
                'constraint' => ['Prezente', 'Tardi', 'Falta', 'Lisensa', 'Incomplete', 'Holiday', 'Weekend'],
                'null'       => false,
            ],
        ]);
    }
}
