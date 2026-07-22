<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifySchemaForGrauAndSubsidiu extends Migration
{
    public function up()
    {
        // 1. Create 'grau' table if it doesn't exist
        if (!$this->db->tableExists('grau')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'naran_grau' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                ],
                'salariu_baziku' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
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
            $this->forge->createTable('grau');
        }

        // 2. Modify 'pozisaun' table
        if ($this->db->tableExists('pozisaun')) {
            // Check if 'grau_id' already exists
            if (!$this->db->fieldExists('grau_id', 'pozisaun')) {
                // Add 'grau_id' column
                $this->forge->addColumn('pozisaun', [
                    'grau_id' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'null'       => true,
                        'after'      => 'naran_pozisaun'
                    ]
                ]);

                // Migrate existing 'salariu_baziku' data to 'grau'
                if ($this->db->fieldExists('salariu_baziku', 'pozisaun')) {
                    $pozisauns = $this->db->table('pozisaun')->get()->getResultArray();
                    foreach ($pozisauns as $p) {
                        $salariu = $p['salariu_baziku'];
                        
                        // Check if a Grau with this salary already exists
                        $existingGrau = $this->db->table('grau')->where('salariu_baziku', $salariu)->get()->getRowArray();
                        if ($existingGrau) {
                            $grauId = $existingGrau['id'];
                        } else {
                            // Create a new Grau
                            $this->db->table('grau')->insert([
                                'naran_grau' => 'Grau ' . number_format($salariu, 0),
                                'salariu_baziku' => $salariu,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                            $grauId = $this->db->insertID();
                        }

                        // Update pozisaun with the new grau_id
                        $this->db->table('pozisaun')->where('id', $p['id'])->update(['grau_id' => $grauId]);
                    }

                    // Drop 'salariu_baziku' column
                    $this->forge->dropColumn('pozisaun', 'salariu_baziku');
                }

                // Add Foreign Key (Manual SQL since SQLite/MySQL handled differently by CI4 forge during addForeignKey on existing table)
                // Note: On MySQL, we can add constraint. Let's do it safely.
                try {
                    $this->db->query("ALTER TABLE pozisaun ADD CONSTRAINT fk_pozisaun_grau FOREIGN KEY (grau_id) REFERENCES grau(id) ON DELETE SET NULL ON UPDATE CASCADE");
                } catch (\Exception $e) {
                    // Ignore if foreign key addition fails (e.g. SQLite or already exists)
                }
            }
        }

        // 3. Modify 'subsidiu' table
        if ($this->db->tableExists('subsidiu')) {
            $fieldsToAdd = [];
            if (!$this->db->fieldExists('pozisaun_id', 'subsidiu')) {
                $fieldsToAdd['pozisaun_id'] = [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id'
                ];
            }
            if (!$this->db->fieldExists('tipu_valor', 'subsidiu')) {
                $fieldsToAdd['tipu_valor'] = [
                    'type'       => 'ENUM',
                    'constraint' => ['Fiksu', 'Persentajen'],
                    'default'    => 'Fiksu',
                    'after'      => 'valor_padrao'
                ];
            }

            if (!empty($fieldsToAdd)) {
                $this->forge->addColumn('subsidiu', $fieldsToAdd);

                if (isset($fieldsToAdd['pozisaun_id'])) {
                    try {
                        $this->db->query("ALTER TABLE subsidiu ADD CONSTRAINT fk_subsidiu_pozisaun FOREIGN KEY (pozisaun_id) REFERENCES pozisaun(id) ON DELETE SET NULL ON UPDATE CASCADE");
                    } catch (\Exception $e) {
                        // Ignore
                    }
                }
            }
        }
    }

    public function down()
    {
        // Keep modifications to prevent data loss
    }
}
