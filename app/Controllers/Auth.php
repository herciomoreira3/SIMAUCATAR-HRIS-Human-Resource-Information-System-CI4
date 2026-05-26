<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn') == TRUE) {
            return redirect()->to(base_url('dashboard'));
        }

        if (!$this->validate(['inputEmail'  => 'required'])) {
            return view('pages/commons/login');
        } else {
            $inputEmail     = htmlspecialchars($this->request->getVar('inputEmail', FILTER_UNSAFE_RAW));
            $inputPassword  = htmlspecialchars($this->request->getVar('inputPassword', FILTER_UNSAFE_RAW));
            $user           = $this->ApplicationModel->getUser(username: $inputEmail);
            if ($user) {
                $password        = $user['password'];
                $verify = password_verify($inputPassword, $password);
                if ($verify) {
                    $sessionData = [
                        'userID'          => $user['userID'],
                        'username'        => $user['username'],
                        'role'            => $user['role_id'], // Role ID for menu access
                        'role_name'       => $user['role'],    // Role name (e.g. administrador)
                        'isLoggedIn'      => TRUE
                    ];

                    // If it's an employee, get their funsionariu_id
                    if ($user['role'] == 'funsionariu') {
                        $funsionariu = $this->ApplicationModel->getFunsionariuByUserId($user['userID']);
                        if ($funsionariu) {
                            $sessionData['funsionariu_id'] = $funsionariu['id'];
                            $sessionData['naran_kompletu'] = $funsionariu['naran_kompletu'];
                        }
                    }

                    session()->set($sessionData);
                    
                    // Redirect based on role
                    if ($user['role'] == 'administrador') {
                        return redirect()->to(base_url('administrador/dashboard'));
                    } elseif ($user['role'] == 'funsionariu') {
                        return redirect()->to(base_url('funsionariu/dashboard'));
                    }
                    
                    return redirect()->to(base_url('dashboard'));
                } else {
                    session()->setFlashdata('notif_error', '<b>Your ID or Password is Wrong !</b> ');
                    return redirect()->to(base_url());
                }
            } else {
                session()->setFlashdata('notif_error', '<b>Your ID or Password is Wrong!</b> ');
                return redirect()->to(base_url());
            }
        }
    }
    public function logout()
    {
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
            'inputPassword'  => ['label' => 'Password', 'rules' => 'required'],
            'inputPassword2' => ['label' => 'Password Confirmation', 'rules' => 'matches[inputPassword]'],
        ])) {
            $data = array_merge($this->data, [
                'title'         => 'Register Page',
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
            session()->setFlashdata('notif_success', '<b>Registration Successfully!</b> Please login with your account.');
            return view('pages/commons/login');
        }
    }
}
