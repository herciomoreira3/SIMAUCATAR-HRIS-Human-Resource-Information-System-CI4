<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $role_name = session()->get('role_name');
        if ($role_name == 'administrador') {
            return redirect()->to(base_url('administrador/dashboard'));
        } elseif ($role_name == 'funsionariu') {
            return redirect()->to(base_url('funsionariu/dashboard'));
        }

        $data = array_merge($this->data, [
            'title'         => 'Dashboard Page'
        ]);
        return view('pages/commons/dashboard', $data);
    }
}
