<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LocalizeTetunLabels extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('user_menu_category')) {
            foreach ([
                'Common Page' => 'Pajina Jeral',
                'Settings' => 'Konfigurasaun',
                'DASHBOARD' => 'PAINEL',
                'MASTER DATA' => 'DADUS MESTRE',
                'HR MANAGEMENT' => 'JESTAUN FUNSIONÁRIU',
                'SELF SERVICE' => 'SERVISU RASIK',
            ] as $old => $new) {
                $this->db->table('user_menu_category')
                    ->where('menu_category', $old)
                    ->update(['menu_category' => $new]);
            }
        }

        if ($this->db->tableExists('user_menu')) {
            foreach ([
                'dashboard' => 'Painel',
                'users' => 'Utilizador',
                'menu-management' => 'Jestaun Menu',
                'administrador/dashboard' => 'Painel Admin',
                'funsionariu/dashboard' => 'Painel Funsionariu',
                'administrador/avizu' => 'Anunsiu',
                'administrador/audit' => 'Rejistu Auditoria',
                'administrador/maintenance' => 'Manutensaun',
            ] as $url => $title) {
                $this->db->table('user_menu')
                    ->where('url', $url)
                    ->update(['title' => $title]);
            }
        }
    }

    public function down()
    {
        // Keep localized labels.
    }
}
