<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeAdminLogin extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('users') || ! $this->db->tableExists('user_role')) {
            return;
        }

        $adminRole = $this->db->table('user_role')
            ->whereIn('role_name', ['administrador', 'admin', 'superadmin'])
            ->orderBy('id', 'ASC')
            ->get()
            ->getRowArray();

        if (! $adminRole) {
            return;
        }

        $fields = $this->db->getFieldNames('users');
        $now = date('Y-m-d H:i:s');
        $password = password_hash('admin123', PASSWORD_DEFAULT);

        $admin = $this->db->table('users')
            ->groupStart()
                ->where('username', 'admin')
                ->orWhere('username', 'admin@gmail.com');

        if (in_array('email', $fields, true)) {
            $admin->orWhere('email', 'admin@gmail.com');
        }

        $admin = $admin->groupEnd()
            ->where('role', $adminRole['id'])
            ->get()
            ->getRowArray();

        $data = [
            'fullname' => 'Administrator',
            'username' => 'admin',
            'password' => $password,
            'role' => $adminRole['id'],
            'updated_at' => $now,
        ];

        if (in_array('email', $fields, true)) {
            $data['email'] = 'admin@gmail.com';
        }

        if (in_array('status', $fields, true)) {
            $data['status'] = 'active';
        }

        if ($admin) {
            $this->db->table('users')->where('id', $admin['id'])->update($data);
            return;
        }

        $data['created_at'] = $now;
        $this->db->table('users')->insert($data);
    }

    public function down()
    {
        // Data normalization is intentionally kept.
    }
}
