<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MergeNotificationsIntoAnunsiu extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('user_menu')) {
            $this->db->table('user_menu')
                ->where('url', 'administrador/avizu')
                ->update(['title' => 'Anunsiu']);

            $notificationMenu = $this->db->table('user_menu')
                ->where('url', 'notifikasaun')
                ->get()
                ->getRowArray();

            if ($notificationMenu && $this->db->tableExists('user_access')) {
                $this->db->table('user_access')
                    ->where('menu_id', $notificationMenu['id'])
                    ->delete();
            }

            $this->db->table('user_menu')
                ->where('url', 'notifikasaun')
                ->delete();
        }

        if ($this->db->tableExists('user_submenu')) {
            $notificationSubmenus = $this->db->table('user_submenu')
                ->select('id')
                ->where('url', 'notifikasaun')
                ->get()
                ->getResultArray();

            if ($notificationSubmenus && $this->db->tableExists('user_access')) {
                $this->db->table('user_access')
                    ->whereIn('submenu_id', array_column($notificationSubmenus, 'id'))
                    ->delete();
            }

            $this->db->table('user_submenu')
                ->where('url', 'notifikasaun')
                ->delete();
        }
    }

    public function down()
    {
        if ($this->db->tableExists('user_menu')) {
            $this->db->table('user_menu')
                ->where('url', 'administrador/avizu')
                ->update(['title' => 'Avizu']);
        }
    }
}
