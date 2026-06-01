<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HardeningFoundation extends Migration
{
    public function up()
    {
        $this->ensureUserSecurityColumns();
        $this->ensureHrisFoundationTables();
        $this->ensurePayrollColumns();
        $this->ensureOperationalTables();
        $this->ensureIndexes();
        $this->repairEmployeeRoles();
    }

    public function down()
    {
        // This migration intentionally keeps data-preserving additions in place.
    }

    private function ensureUserSecurityColumns(): void
    {
        if ($this->db->tableExists('users')) {
            if (! $this->db->fieldExists('email', 'users')) {
                $this->forge->addColumn('users', [
                    'email' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255,
                        'null' => true,
                        'after' => 'username',
                    ],
                ]);
            }

            if (! $this->db->fieldExists('status', 'users')) {
                $this->forge->addColumn('users', [
                    'status' => [
                        'type' => 'VARCHAR',
                        'constraint' => 20,
                        'default' => 'active',
                        'after' => 'role',
                    ],
                ]);
            }

            foreach ([
                'failed_login_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'locked_until' => ['type' => 'DATETIME', 'null' => true],
                'last_login_at' => ['type' => 'DATETIME', 'null' => true],
                'last_login_ip' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
                'password_changed_at' => ['type' => 'DATETIME', 'null' => true],
            ] as $field => $definition) {
                if (! $this->db->fieldExists($field, 'users')) {
                    $this->forge->addColumn('users', [$field => $definition]);
                }
            }

            $this->db->query("UPDATE users SET status = 'active' WHERE status IS NULL OR status = ''");
        }

        if ($this->db->tableExists('funsionariu') && ! $this->db->fieldExists('status', 'funsionariu')) {
            $this->forge->addColumn('funsionariu', [
                'status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'default' => 'active',
                    'after' => 'foto_perfil',
                ],
            ]);
        }
    }

    private function ensureHrisFoundationTables(): void
    {
        if (! $this->db->tableExists('attendance_settings')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'tama_hahu' => ['type' => 'TIME', 'default' => '08:00:00'],
                'tama_remata' => ['type' => 'TIME', 'default' => '09:00:00'],
                'sai_hahu' => ['type' => 'TIME', 'default' => '17:00:00'],
                'sai_remata' => ['type' => 'TIME', 'default' => '18:00:00'],
                'toleransia_minutu' => ['type' => 'INT', 'constraint' => 11, 'default' => 15],
                'sabadu' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'domingu' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('attendance_settings');
        }

        foreach ([
            'sabadu' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'domingu' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ] as $field => $definition) {
            if (! $this->db->fieldExists($field, 'attendance_settings')) {
                $this->forge->addColumn('attendance_settings', [$field => $definition]);
            }
        }

        if (! $this->db->table('attendance_settings')->get()->getRowArray()) {
            $this->db->table('attendance_settings')->insert([
                'tama_hahu' => '08:00:00',
                'tama_remata' => '09:00:00',
                'sai_hahu' => '17:00:00',
                'sai_remata' => '18:00:00',
                'toleransia_minutu' => 15,
                'sabadu' => 0,
                'domingu' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        if ($this->db->tableExists('prezensa')) {
            $this->db->query("ALTER TABLE prezensa MODIFY COLUMN estadu_prezensa ENUM('Prezente','Tardi','Falta','Lisensa','Incomplete','Holiday','Weekend') NOT NULL");
        }

        if (! $this->db->tableExists('subsidiu')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'naran_subsidiu' => ['type' => 'VARCHAR', 'constraint' => 100],
                'valor_padrao' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
                'deskrisaun' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('subsidiu');
        }

        if (! $this->db->tableExists('tipu_sansaun')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'naran_tipu' => ['type' => 'VARCHAR', 'constraint' => 100],
                'kategoria' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Jeral'],
                'valor_dedusaun' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('tipu_sansaun');
        } else {
            if (! $this->db->fieldExists('is_active', 'tipu_sansaun')) {
                $this->forge->addColumn('tipu_sansaun', [
                    'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                ]);
            }
            $this->db->query("ALTER TABLE tipu_sansaun MODIFY COLUMN kategoria VARCHAR(50) DEFAULT 'Jeral'");
        }

        if ($this->db->tableExists('sansaun')) {
            foreach ([
                'tipu_sansaun_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'estadu_sansaun' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Ativu'],
                'valor_total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
                'valor_pagadu' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
                'pozisaun_anterior_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'retira_reason' => ['type' => 'TEXT', 'null' => true],
                'retira_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'retira_at' => ['type' => 'DATETIME', 'null' => true],
            ] as $field => $definition) {
                if (! $this->db->fieldExists($field, 'sansaun')) {
                    $this->forge->addColumn('sansaun', [$field => $definition]);
                }
            }
        }

        if ($this->db->tableExists('avizu')) {
            foreach ([
                'data_remata' => ['type' => 'DATETIME', 'null' => true],
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Published'],
                'published_at' => ['type' => 'DATETIME', 'null' => true],
                'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            ] as $field => $definition) {
                if (! $this->db->fieldExists($field, 'avizu')) {
                    $this->forge->addColumn('avizu', [$field => $definition]);
                }
            }
        }
    }

    private function ensurePayrollColumns(): void
    {
        if ($this->db->tableExists('salariu_detallu')) {
            $columns = $this->db->getFieldNames('salariu_detallu');
            if (! in_array('valor', $columns, true)) {
                foreach ($columns as $column) {
                    if (str_starts_with($column, 'val')) {
                        $safeColumn = str_replace('`', '``', $column);
                        $this->db->query("ALTER TABLE salariu_detallu CHANGE `$safeColumn` `valor` DECIMAL(10,2) DEFAULT NULL");
                        break;
                    }
                }
            }
        }
    }

    private function ensureOperationalTables(): void
    {
        if (! $this->db->tableExists('audit_logs')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
                'actor_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'actor_role' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'action' => ['type' => 'VARCHAR', 'constraint' => 100],
                'entity_type' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'entity_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'old_values' => ['type' => 'TEXT', 'null' => true],
                'new_values' => ['type' => 'TEXT', 'null' => true],
                'ip_address' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
                'user_agent' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('audit_logs');
        }

        if (! $this->db->tableExists('payroll_periods')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'fulan' => ['type' => 'INT', 'constraint' => 2],
                'tinan' => ['type' => 'INT', 'constraint' => 4],
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Draft'],
                'processed_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'processed_at' => ['type' => 'DATETIME', 'null' => true],
                'locked_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'locked_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('payroll_periods');
        }

        if ($this->db->tableExists('salariu') && ! $this->db->fieldExists('payroll_period_id', 'salariu')) {
            $this->forge->addColumn('salariu', [
                'payroll_period_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'id'],
                'processed_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'processed_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
        }

        if (! $this->db->tableExists('employee_documents')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
                'funsionariu_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'category' => ['type' => 'VARCHAR', 'constraint' => 100],
                'original_name' => ['type' => 'VARCHAR', 'constraint' => 255],
                'stored_name' => ['type' => 'VARCHAR', 'constraint' => 255],
                'mime_type' => ['type' => 'VARCHAR', 'constraint' => 150],
                'size_bytes' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'visibility' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'admin_only'],
                'uploaded_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'deleted_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('employee_documents');
        }

        if (! $this->db->tableExists('leave_balances')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'funsionariu_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'leave_type' => ['type' => 'VARCHAR', 'constraint' => 100],
                'year' => ['type' => 'INT', 'constraint' => 4],
                'entitlement_days' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
                'used_days' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
                'pending_days' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
                'remaining_days' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('leave_balances');
        }
    }

    private function ensureIndexes(): void
    {
        if ($this->db->tableExists('users') && ! $this->indexExists('users', 'idx_users_username_unique')) {
            $duplicates = $this->db->query('SELECT username FROM users GROUP BY username HAVING COUNT(*) > 1 LIMIT 1')->getRowArray();
            if (! $duplicates) {
                $this->db->query('CREATE UNIQUE INDEX idx_users_username_unique ON users(username)');
            }
        }

        if ($this->db->tableExists('salariu') && ! $this->indexExists('salariu', 'idx_salary_period_unique')) {
            $duplicates = $this->db->query('SELECT funsionariu_id, fulan, tinan FROM salariu GROUP BY funsionariu_id, fulan, tinan HAVING COUNT(*) > 1 LIMIT 1')->getRowArray();
            if (! $duplicates) {
                $this->db->query('CREATE UNIQUE INDEX idx_salary_period_unique ON salariu(funsionariu_id, fulan, tinan)');
            }
        }

        foreach ([
            ['prezensa', 'idx_prezensa_employee_date', 'funsionariu_id, data_prezensa'],
            ['lisensa', 'idx_lisensa_employee_dates', 'funsionariu_id, data_hahu, data_remata'],
            ['sansaun', 'idx_sansaun_employee_status', 'funsionariu_id, estadu_sansaun'],
        ] as [$table, $index, $columns]) {
            if ($this->db->tableExists($table) && ! $this->indexExists($table, $index)) {
                $this->db->query("CREATE INDEX $index ON $table($columns)");
            }
        }
    }

    private function repairEmployeeRoles(): void
    {
        if (! $this->db->tableExists('users') || ! $this->db->tableExists('funsionariu') || ! $this->db->tableExists('user_role')) {
            return;
        }

        $role = $this->db->table('user_role')->where('role_name', 'funsionariu')->get()->getRowArray();
        if (! $role) {
            return;
        }

        $this->db->query("
            UPDATE users u
            INNER JOIN funsionariu f ON f.utilizador_id = u.id
            SET u.role = ?
            WHERE u.username NOT IN ('admin', 'admin@gmail.com')
        ", [$role['id']]);
    }

    private function indexExists(string $table, string $index): bool
    {
        $rows = $this->db->query("SHOW INDEX FROM `$table` WHERE Key_name = ?", [$index])->getResultArray();
        return ! empty($rows);
    }
}
