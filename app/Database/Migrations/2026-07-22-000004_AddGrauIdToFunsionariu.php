<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGrauIdToFunsionariu extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('funsionariu')) {
            return;
        }

        // 1. Add 'grau_id' to 'funsionariu' table if it doesn't exist
        if (!$this->db->fieldExists('grau_id', 'funsionariu')) {
            $this->forge->addColumn('funsionariu', [
                'grau_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'pozisaun_id'
                ]
            ]);

            // 2. Migrate existing 'grau_id' data from 'pozisaun' to 'funsionariu'
            if ($this->db->tableExists('pozisaun') && $this->db->fieldExists('grau_id', 'pozisaun')) {
                $this->db->query("
                    UPDATE funsionariu f
                    JOIN pozisaun p ON f.pozisaun_id = p.id
                    SET f.grau_id = p.grau_id
                    WHERE p.grau_id IS NOT NULL
                ");
            }

            // 3. Add Foreign Key to 'funsionariu' pointing to 'grau'
            try {
                $this->db->query("ALTER TABLE funsionariu ADD CONSTRAINT fk_funsionariu_grau FOREIGN KEY (grau_id) REFERENCES grau(id) ON DELETE SET NULL ON UPDATE CASCADE");
            } catch (\Exception $e) {
                // Ignore if constraint addition fails (e.g. SQLite)
            }
        }

        // 4. Remove 'grau_id' from 'pozisaun' table
        if ($this->db->tableExists('pozisaun') && $this->db->fieldExists('grau_id', 'pozisaun')) {
            // Drop constraint first if possible
            try {
                $this->db->query("ALTER TABLE pozisaun DROP FOREIGN KEY pozisaun_grau_id_foreign");
            } catch (\Exception $e) {
                // Ignore
            }
            try {
                $this->db->query("ALTER TABLE pozisaun DROP FOREIGN KEY fk_pozisaun_grau");
            } catch (\Exception $e) {
                // Ignore
            }

            $this->forge->dropColumn('pozisaun', 'grau_id');
        }
    }

    public function down()
    {
        // Prevent data loss
    }
}
