<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn') == TRUE) {
            return redirect()->to(base_url('dashboard'));
        }

        if (!$this->request->is('post')) {
            return view('pages/commons/login');
        }

        if (!$this->validate([
            'inputEmail'    => 'required|min_length[3]|max_length[100]',
            'inputPassword' => 'required|max_length[255]',
        ])) {
            session()->setFlashdata('notif_error', '<b>Naran utilizador/email no senha presiza kompletu.</b>');
            return redirect()->to(base_url())->withInput();
        }

        $inputLogin     = trim((string) $this->request->getPost('inputEmail'));
        $inputPassword  = (string) $this->request->getPost('inputPassword');
        $user           = $this->ApplicationModel->getUser(username: $inputLogin);

        if ($user && !empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
            $this->logAudit('login_locked', 'user', $user['userID']);
            session()->setFlashdata('notif_error', '<b>Akun taka temporariamente. Favor koko fali depois.</b>');
            return redirect()->to(base_url())->withInput();
        }

        if (!$user || !password_verify($inputPassword, $user['password'])) {
            if ($user && ($user['_auth_table'] ?? 'users') === 'users' && $this->db->fieldExists('failed_login_count', 'users')) {
                $failedCount = (int) ($user['failed_login_count'] ?? 0) + 1;
                $update = ['failed_login_count' => $failedCount];
                if ($failedCount >= 5 && $this->db->fieldExists('locked_until', 'users')) {
                    $update['locked_until'] = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                }
                $this->db->table('users')->where('id', $user['userID'])->update($update);
            }

            $this->logAudit('login_failed', 'user_login', $inputLogin);
            session()->setFlashdata('notif_error', '<b>Naran utilizador/email ka senha sala!</b>');
            return redirect()->to(base_url())->withInput();
        }

        $accountStatus = strtolower((string) ($user['status'] ?? $user['estadu_kontu'] ?? 'active'));
        if (in_array($accountStatus, ['inactive', 'inativu', 'disabled', 'blocked'], true)) {
            session()->setFlashdata('notif_error', '<b>Akun ne\'e seidauk ativu. Favor kontaktu admin.</b>');
            return redirect()->to(base_url());
        }

        session()->regenerate(true);

        $sessionData = [
            'userID'          => $user['userID'],
            'username'        => $user['username'],
            'role'            => $user['role_id'],
            'role_name'       => $user['role'],
            'isLoggedIn'      => true
        ];

        if ($user['role'] === 'funsionariu') {
            $funsionariu = $this->ApplicationModel->getFunsionariuByUserId($user['userID']);
            if ($funsionariu) {
                $sessionData['funsionariu_id'] = $funsionariu['id'];
                $sessionData['naran_kompletu'] = $funsionariu['naran_kompletu'];
            }
        }

        session()->set($sessionData);

        if (($user['_auth_table'] ?? 'users') === 'users' && $this->db->fieldExists('last_login_at', 'users')) {
            $this->db->table('users')->where('id', $user['userID'])->update([
                'failed_login_count' => 0,
                'locked_until'       => null,
                'last_login_at'      => date('Y-m-d H:i:s'),
                'last_login_ip'      => $this->request->getIPAddress(),
            ]);
        }

        $this->logAudit('login_success', 'user', $user['userID']);

        if ($user['role'] === 'administrador') {
            return redirect()->to(base_url('administrador/dashboard'));
        }

        if ($user['role'] === 'funsionariu') {
            return redirect()->to(base_url('funsionariu/dashboard'));
        }

        return redirect()->to(base_url('dashboard'));
    }
    public function logout()
    {
        $this->logAudit('logout', 'user', session()->get('userID'));
        $this->session->destroy();
        return redirect()->to(base_url('/'));
    }

    public function forbiddenPage()
    {
        $data = array_merge($this->data, [
            'title'         => 'Forbidden Page'
        ]);
        return view('pages/commons/forbidden', $data);
    }

    public function register()
    {
        return view('pages/commons/register');
    }

    public function registration()
    {
        if (!$this->validate([
            'inputEmail'     => ['label' => 'Email', 'rules' => 'is_unique[users.username]'],
            'inputPassword'  => ['label' => 'Senha', 'rules' => 'required'],
            'inputPassword2' => ['label' => 'Konfirmasaun Senha', 'rules' => 'matches[inputPassword]'],
        ])) {
            $data = array_merge($this->data, [
                'title'         => 'Pajina Rejistu',
            ]);

            session()->setFlashdata('notif_error', $this->validation->getError('inputPassword2') . ' ' . $this->validation->getError('inputEmail'));
            return view('pages/commons/register', $data);
        } else {
            $inputFullname = htmlspecialchars($this->request->getVar('inputFullname', FILTER_UNSAFE_RAW));
            $inputEmail    = htmlspecialchars($this->request->getVar('inputEmail', FILTER_UNSAFE_RAW));
            $inputPassword = htmlspecialchars($this->request->getVar('inputPassword', FILTER_UNSAFE_RAW));
            $dataUser      = [
                'inputFullname' => $inputFullname,
                'inputUsername' => $inputEmail,
                'inputPassword' => $inputPassword,
                'inputRole'     => 1
            ];
            $this->ApplicationModel->createUser($dataUser);
            session()->setFlashdata('notif_success', '<b>Rejistu susesu!</b> Favor tama ho ita-nia akun.');
            return view('pages/commons/login');
        }
    }
}
