<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSesaunToLisensa extends Migration
{
    public function up()
    {
        // Add 'sesaun' column to lisensa table
        $this->forge->addColumn('lisensa', [
            'sesaun' => [
                'type'       => 'ENUM',
                'constraint' => ['Loron Tomak', 'Dader', 'Lokraik'],
                'default'    => 'Loron Tomak',
                'after'      => 'tipu_lisensa',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('lisensa', 'sesaun');
    }
}
