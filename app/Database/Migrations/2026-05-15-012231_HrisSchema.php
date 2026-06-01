<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HrisSchema extends Migration
{
    public function up()
    {
        // 2.1. Tabela papel (Roles)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_papel' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('papel');

        // 2.2. Tabela utilizador (Users)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_utilizador' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'xave_secreta' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'papel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'estadu_kontu' => [
                'type'       => 'ENUM',
                'constraint' => ['Ativu', 'Inativu'],
                'default'    => 'Ativu',
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
        $this->forge->addForeignKey('papel_id', 'papel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('utilizador');

        // 2.3. Tabela departamentu
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_departamentu' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('departamentu');

        // Tabela pozisaun
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_pozisaun' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'salariu_baziku' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pozisaun');

        // Tabela kategoria
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_kategoria' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('kategoria');

        // 2.4. Tabela funsionariu
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'utilizador_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'unique'     => true,
            ],
            'nid' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'naran_kompletu' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'seksu' => [
                'type'       => 'ENUM',
                'constraint' => ['Mane', 'Feto'],
            ],
            'fatin_moris' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'data_moris' => [
                'type' => 'DATE',
            ],
            'hela_fatin' => [
                'type' => 'TEXT',
            ],
            'nu_telefone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'estadu_sivil' => [
                'type'       => 'ENUM',
                'constraint' => ['Solteiru', 'Kaben Nain', 'Divorsiadu'],
            ],
            'departamentu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'pozisaun_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kategoria_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'data_hahu_servisu' => [
                'type' => 'DATE',
            ],
            'foto_perfil' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
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
        $this->forge->addForeignKey('utilizador_id', 'utilizador', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('departamentu_id', 'departamentu', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pozisaun_id', 'pozisaun', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kategoria_id', 'kategoria', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('funsionariu');

        // 2.5. Tabela prezensa
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'funsionariu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'data_prezensa' => [
                'type' => 'DATE',
            ],
            'oras_tama' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'oras_sai' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'estadu_prezensa' => [
                'type'       => 'ENUM',
                'constraint' => ['Prezente', 'Tardi', 'Falta', 'Lisensa', 'Incomplete', 'Holiday', 'Weekend'],
            ],
            'foto_tama' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'kordenada' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
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
        $this->forge->addForeignKey('funsionariu_id', 'funsionariu', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('prezensa');

        // 2.6. Tabela lisensa
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'funsionariu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tipu_lisensa' => [
                'type'       => 'ENUM',
                'constraint' => ['Moras', 'Anual', 'Maternidade', 'Lutu', 'Seluk'],
            ],
            'data_hahu' => [
                'type' => 'DATE',
            ],
            'data_remata' => [
                'type' => 'DATE',
            ],
            'razaun' => [
                'type' => 'TEXT',
            ],
            'dokumentu_suporta' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'estadu_lisensa' => [
                'type'       => 'ENUM',
                'constraint' => ['Pendente', 'Aprovadu', 'Rezeitadu'],
                'default'    => 'Pendente',
            ],
            'komentariu_admin' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('funsionariu_id', 'funsionariu', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lisensa');

        // 2.7. Tabela salariu
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'funsionariu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'fulan' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'tinan' => [
                'type'       => 'YEAR',
            ],
            'salariu_baziku' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'total_subsidiu' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'total_deskontu' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'salariu_liquidu' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'estadu_pagamentu' => [
                'type'       => 'ENUM',
                'constraint' => ['Seidauk Selu', 'Selu Ona'],
                'default'    => 'Seidauk Selu',
            ],
            'data_pagamentu' => [
                'type' => 'DATE',
                'null' => true,
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
        $this->forge->addForeignKey('funsionariu_id', 'funsionariu', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('salariu');

        // Tabela salariu_detallu
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'salariu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'naran_komponente' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'valor' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'tipu' => [
                'type'       => 'ENUM',
                'constraint' => ['Subsidiu', 'Deskontu'],
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('salariu_id', 'salariu', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('salariu_detallu');

        // 2.8. Tabela avizu
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'titulu' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'konteudu' => [
                'type' => 'TEXT',
            ],
            'data_publikasaun' => [
                'type' => 'DATE',
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
        $this->forge->createTable('avizu');

        // Tabela sansaun
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'funsionariu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tipu_sansaun' => [
                'type'       => 'ENUM',
                'constraint' => ['Avisu Lisan', 'Avisu Eskritu 1', 'Avisu Eskritu 2', 'Suspensaun'],
            ],
            'motivu' => [
                'type' => 'TEXT',
            ],
            'data_sansaun' => [
                'type' => 'DATE',
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
        $this->forge->addForeignKey('funsionariu_id', 'funsionariu', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sansaun');
    }

    public function down()
    {
        $this->forge->dropTable('sansaun');
        $this->forge->dropTable('avizu');
        $this->forge->dropTable('salariu_detallu');
        $this->forge->dropTable('salariu');
        $this->forge->dropTable('lisensa');
        $this->forge->dropTable('prezensa');
        $this->forge->dropTable('funsionariu');
        $this->forge->dropTable('kategoria');
        $this->forge->dropTable('pozisaun');
        $this->forge->dropTable('departamentu');
        $this->forge->dropTable('utilizador');
        $this->forge->dropTable('papel');
    }
}
