<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Administrador extends BaseController
{
    public function dashboard()
    {
        // Chart 1: Attendance Trends (Last 15 days)
        $labels = [];
        $prezente = [];
        $falta = [];
        for ($i = 14; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('d M', strtotime($date));
            $prezente[] = $this->db->table('prezensa')->where('data_prezensa', $date)->whereIn('estadu_prezensa', ['Prezente'])->countAllResults();
            $falta[] = $this->db->table('prezensa')->where('data_prezensa', $date)->where('estadu_prezensa', 'Falta')->countAllResults();
        }

        // Chart 2: Department Composition
        $dept_comp = $this->db->table('funsionariu')
            ->select('departamentu.naran_departamentu, COUNT(funsionariu.id) as total')
            ->join('departamentu', 'funsionariu.departamentu_id = departamentu.id')
            ->groupBy('funsionariu.departamentu_id')
            ->get()->getResultArray();

        $data = array_merge($this->data, [
            'title' => 'Dashboard Administrador',
            'total_funsionariu' => count($this->ApplicationModel->getFunsionariu()),
            'total_prezensa_ohin' => count($this->ApplicationModel->getPrezensa(data: date('Y-m-d'))),
            'pendente_lisensa' => count($this->ApplicationModel->getLisensa(estadu: 'Pendente')),
            'avizu_ikus' => $this->ApplicationModel->getAvizu(),
            'sansaun_ikus' => $this->ApplicationModel->getSansaun(),
            'chart_labels' => json_encode($labels),
            'chart_prezente' => json_encode($prezente),
            'chart_falta' => json_encode($falta),
            'dept_comp' => json_encode($dept_comp),
        ]);
        return view('pages/administrador/dashboard', $data);
    }


    // Jestaun Dadus Báziku
    public function departamentu()
    {
        $data = array_merge($this->data, [
            'title' => 'Jestaun Departamentu',
            'departamentu' => $this->ApplicationModel->getDepartamentu(),
        ]);
        return view('pages/administrador/departamentu', $data);
    }

    public function createDepartamentu()
    {
        $naran = $this->request->getVar('naran_departamentu');
        $this->ApplicationModel->saveData('departamentu', ['naran_departamentu' => $naran]);
        session()->setFlashdata('success', 'Departamentu foun aumenta ona!');
        return redirect()->back();
    }

    public function updateDepartamentu($id)
    {
        $naran = $this->request->getVar('naran_departamentu');
        $this->ApplicationModel->updateData('departamentu', ['naran_departamentu' => $naran], ['id' => $id]);
        session()->setFlashdata('success', 'Departamentu atualiza ona!');
        return redirect()->back();
    }

    public function deleteDepartamentu($id)
    {
        $check = $this->db->table('funsionariu')->where('departamentu_id', $id)->countAllResults();
        if ($check > 0) {
            session()->setFlashdata('error', 'La bele hamos departamentu ne\'e tanba iha funsionáriu ne\'ebé utiliza hela!');
            return redirect()->back();
        }

        $this->ApplicationModel->deleteData('departamentu', ['id' => $id]);
        session()->setFlashdata('success', 'Departamentu hamos ona!');
        return redirect()->back();
    }

    public function pozisaun()
    {
        $data = array_merge($this->data, [
            'title' => 'Jestaun Pozisaun',
            'pozisaun' => $this->ApplicationModel->getPozisaun(),
        ]);
        return view('pages/administrador/pozisaun', $data);
    }

    public function createPozisaun()
    {
        $naran = $this->request->getVar('naran_pozisaun');
        $salariu = $this->request->getVar('salariu_baziku');
        $this->ApplicationModel->saveData('pozisaun', [
            'naran_pozisaun' => $naran,
            'salariu_baziku' => $salariu
        ]);
        session()->setFlashdata('success', 'Pozisaun foun aumenta ona!');
        return redirect()->back();
    }

    public function updatePozisaun($id)
    {
        $naran = $this->request->getVar('naran_pozisaun');
        $salariu = $this->request->getVar('salariu_baziku');
        $this->ApplicationModel->updateData('pozisaun', [
            'naran_pozisaun' => $naran,
            'salariu_baziku' => $salariu
        ], ['id' => $id]);
        session()->setFlashdata('success', 'Pozisaun atualiza ona!');
        return redirect()->back();
    }

    public function deletePozisaun($id)
    {
        $check = $this->db->table('funsionariu')->where('pozisaun_id', $id)->countAllResults();
        if ($check > 0) {
            session()->setFlashdata('error', 'La bele hamos pozisaun ne\'e tanba iha funsionáriu ne\'ebé utiliza hela!');
            return redirect()->back();
        }

        $this->ApplicationModel->deleteData('pozisaun', ['id' => $id]);
        session()->setFlashdata('success', 'Pozisaun hamos ona!');
        return redirect()->back();
    }

    public function kategoria()
    {
        $data = array_merge($this->data, [
            'title' => 'Jestaun Kategoria',
            'kategoria' => $this->ApplicationModel->getKategoria(),
        ]);
        return view('pages/administrador/kategoria', $data);
    }

    public function createKategoria()
    {
        $naran = $this->request->getVar('naran_kategoria');
        $this->ApplicationModel->saveData('kategoria', ['naran_kategoria' => $naran]);
        session()->setFlashdata('success', 'Kategoria foun aumenta ona!');
        return redirect()->back();
    }

    public function updateKategoria($id)
    {
        $naran = $this->request->getVar('naran_kategoria');
        $this->ApplicationModel->updateData('kategoria', ['naran_kategoria' => $naran], ['id' => $id]);
        session()->setFlashdata('success', 'Kategoria atualiza ona!');
        return redirect()->back();
    }

    public function deleteKategoria($id)
    {
        $check = $this->db->table('funsionariu')->where('kategoria_id', $id)->countAllResults();
        if ($check > 0) {
            session()->setFlashdata('error', 'La bele hamos kategoria ne\'e tanba iha funsionáriu ne\'ebé utiliza hela!');
            return redirect()->back();
        }

        $this->ApplicationModel->deleteData('kategoria', ['id' => $id]);
        session()->setFlashdata('success', 'Kategoria hamos ona!');
        return redirect()->back();
    }

    // Jestaun Funsionáriu
    public function funsionariu()
    {
        $data = array_merge($this->data, [
            'title' => 'Jestaun Funsionáriu',
            'funsionariu' => $this->ApplicationModel->getFunsionariu(),
            'departamentu' => $this->ApplicationModel->getDepartamentu(),
            'pozisaun' => $this->ApplicationModel->getPozisaun(),
            'kategoria' => $this->ApplicationModel->getKategoria(),
            'papel' => $this->ApplicationModel->getUserRole(),
        ]);
        return view('pages/administrador/funsionariu', $data);
    }

    public function saveFunsionariu()
    {
        // 1. Insert to users first
        $userData = [
            'fullname'         => $this->request->getVar('naran_kompletu'),
            'username'         => $this->request->getVar('username'),
            'password'         => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'role'             => $this->request->getVar('papel_id'),
            'created_at'       => date('Y-m-d H:i:s'),
        ];
        $this->db->table('users')->insert($userData);
        $userId = $this->db->insertID();

        // 2. Insert to funsionariu
        $funsionariuData = [
            'utilizador_id'     => $userId,
            'nid'               => $this->request->getVar('nid'),
            'naran_kompletu'    => $this->request->getVar('naran_kompletu'),
            'seksu'             => $this->request->getVar('seksu'),
            'fatin_moris'       => $this->request->getVar('fatin_moris'),
            'data_moris'        => $this->request->getVar('data_moris'),
            'hela_fatin'        => $this->request->getVar('hela_fatin'),
            'nu_telefone'       => $this->request->getVar('nu_telefone'),
            'estadu_sivil'      => $this->request->getVar('estadu_sivil'),
            'departamentu_id'   => $this->request->getVar('departamentu_id'),
            'pozisaun_id'       => $this->request->getVar('pozisaun_id'),
            'kategoria_id'      => $this->request->getVar('kategoria_id'),
            'data_hahu_servisu' => $this->request->getVar('data_hahu_servisu'),
            'created_at'        => date('Y-m-d H:i:s'),
        ];

        // Handle File Upload
        $file = $this->request->getFile('foto_perfil');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/perfil', $newName);
            $funsionariuData['foto_perfil'] = $newName;
        }

        $this->ApplicationModel->saveData('funsionariu', $funsionariuData);
        session()->setFlashdata('success', 'Funsionáriu foun aumenta ona!');
        return redirect()->to(base_url('administrador/funsionariu'));
    }

    public function updateFunsionariu($id)
    {
        $funsionariuData = [
            'nid'               => $this->request->getVar('nid'),
            'naran_kompletu'    => $this->request->getVar('naran_kompletu'),
            'seksu'             => $this->request->getVar('seksu'),
            'fatin_moris'       => $this->request->getVar('fatin_moris'),
            'data_moris'        => $this->request->getVar('data_moris'),
            'hela_fatin'        => $this->request->getVar('hela_fatin'),
            'nu_telefone'       => $this->request->getVar('nu_telefone'),
            'estadu_sivil'      => $this->request->getVar('estadu_sivil'),
            'departamentu_id'   => $this->request->getVar('departamentu_id'),
            'pozisaun_id'       => $this->request->getVar('pozisaun_id'),
            'kategoria_id'      => $this->request->getVar('kategoria_id'),
            'data_hahu_servisu' => $this->request->getVar('data_hahu_servisu'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        // Handle File Upload
        $file = $this->request->getFile('foto_perfil');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/perfil', $newName);
            $funsionariuData['foto_perfil'] = $newName;
        }

        $this->ApplicationModel->updateData('funsionariu', $funsionariuData, ['id' => $id]);

        // Update user account
        $funsionariu = $this->ApplicationModel->getFunsionariu($id);
        if ($funsionariu) {
            $userData = [
                'fullname'   => $this->request->getVar('naran_kompletu'),
                'username'   => $this->request->getVar('username'),
                'role'       => $this->request->getVar('papel_id'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $password = $this->request->getVar('password');
            if (!empty($password)) {
                $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $this->ApplicationModel->updateData('users', $userData, ['id' => $funsionariu['utilizador_id']]);
        }

        session()->setFlashdata('success', 'Dadus funsionáriu atualiza ona!');
        return redirect()->to(base_url('administrador/funsionariu'));
    }

    public function deleteFunsionariu($id)
    {
        $funsionariu = $this->ApplicationModel->getFunsionariu($id);
        if ($funsionariu) {
            // Delete from funsionariu table
            $this->ApplicationModel->deleteData('funsionariu', ['id' => $id]);
            // Delete from users table
            if (!empty($funsionariu['utilizador_id'])) {
                $this->ApplicationModel->deleteData('users', ['id' => $funsionariu['utilizador_id']]);
            }
            session()->setFlashdata('success', 'Funsionáriu no akun utilizador hamos ona!');
        } else {
            session()->setFlashdata('error', 'Dadus la konese!');
        }
        return redirect()->back();
    }

    // Jestaun Prezensa
    public function prezensa()
    {
        $data = array_merge($this->data, [
            'title' => 'Jestaun Prezensa',
            'prezensa' => $this->ApplicationModel->getPrezensa(),
            'settings' => $this->ApplicationModel->getAttendanceSettings(),
        ]);
        return view('pages/administrador/prezensa', $data);
    }

    public function updateAttendanceSettings()
    {
        $data = [
            'tama_hahu'         => $this->request->getVar('tama_hahu'),
            'tama_remata'       => $this->request->getVar('tama_remata'),
            'sai_hahu'          => $this->request->getVar('sai_hahu'),
            'sai_remata'        => $this->request->getVar('sai_remata'),
            'toleransia_minutu' => $this->request->getVar('toleransia_minutu'),
            'sabadu'            => $this->request->getVar('sabadu') ? 1 : 0,
            'domingu'           => $this->request->getVar('domingu') ? 1 : 0,
            'updated_at'        => date('Y-m-d H:i:s'),
        ];
        $this->ApplicationModel->updateData('attendance_settings', $data, ['id' => 1]);


        // Automatically create an announcement
        $this->ApplicationModel->saveData('avizu', [
            'titulu' => 'Konfigurasaun Absénsia Foun',
            'konteudu' => 'Absénsia TAMA loke husi ' . $data['tama_hahu'] . ' to\'o ' . $data['tama_remata'] . '. Absénsia SAI loke husi ' . $data['sai_hahu'] . ' to\'o ' . $data['sai_remata'] . '. Favor absénte iha tempu ne\'ebé konese!',
            'data_publikasaun' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Konfigurasaun prezensa atualiza ona!');
        return redirect()->back();
    }

    // Jestaun Lisensa
    public function lisensa()
    {
        $data = array_merge($this->data, [
            'title' => 'Jestaun Lisensa',
            'lisensa' => $this->ApplicationModel->getLisensa(),
        ]);
        return view('pages/administrador/lisensa', $data);
    }

    public function aprovaLisensa($id)
    {
        $estadu = $this->request->getPost('estadu_lisensa');
        $komentariu = $this->request->getPost('komentariu_admin');

        $this->ApplicationModel->updateData('lisensa', [
            'estadu_lisensa'   => $estadu,
            'komentariu_admin' => $komentariu,
            'updated_at'       => date('Y-m-d H:i:s')
        ], ['id' => $id]);
        
        if ($estadu == 'Aprovadu') {
            $lisensa = $this->ApplicationModel->getLisensa($id);
            // Logic to insert into prezensa for each day
            $start = new \DateTime($lisensa['data_hahu']);
            $end = new \DateTime($lisensa['data_remata']);
            $interval = new \DateInterval('P1D');
            $period = new \DatePeriod($start, $interval, $end->modify('+1 day'));

            foreach ($period as $date) {
                $checkDate = $date->format('Y-m-d');
                $existing = $this->db->table('prezensa')
                    ->where('funsionariu_id', $lisensa['funsionariu_id'])
                    ->where('data_prezensa', $checkDate)
                    ->get()->getRowArray();
                
                if ($existing) {
                    $this->ApplicationModel->updateData('prezensa', [
                        'estadu_prezensa' => 'Lisensa',
                        'updated_at'      => date('Y-m-d H:i:s')
                    ], ['id' => $existing['id']]);
                } else {
                    $this->ApplicationModel->saveData('prezensa', [
                        'funsionariu_id'  => $lisensa['funsionariu_id'],
                        'data_prezensa'   => $checkDate,
                        'estadu_prezensa' => 'Lisensa',
                        'created_at'      => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
        
        session()->setFlashdata('success', 'Estadu lisensa atualiza ona!');
        return redirect()->back();
    }

    // Jestaun Saláriu
    public function salariu()
    {
        $data = array_merge($this->data, [
            'title' => 'Jestaun Saláriu',
            'salariu' => $this->ApplicationModel->getSalariu(),
            'funsionariu' => $this->ApplicationModel->getFunsionariu(),
            'subsidiu' => $this->ApplicationModel->getSubsidiu(),
        ]);
        return view('pages/administrador/salariu', $data);
    }

    public function createSubsidiu()
    {
        $data = [
            'naran_subsidiu' => $this->request->getVar('naran_subsidiu'),
            'valor_padrao'   => $this->request->getVar('valor_padrao'),
            'deskrisaun'     => $this->request->getVar('deskrisaun'),
            'created_at'     => date('Y-m-d H:i:s'),
        ];
        $this->ApplicationModel->saveData('subsidiu', $data);
        session()->setFlashdata('success', 'Subsídiu foun aumenta ona!');
        return redirect()->back();
    }

    public function updateSubsidiu($id)
    {
        $data = [
            'naran_subsidiu' => $this->request->getVar('naran_subsidiu'),
            'valor_padrao'   => $this->request->getVar('valor_padrao'),
            'deskrisaun'     => $this->request->getVar('deskrisaun'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
        $this->ApplicationModel->updateData('subsidiu', $data, ['id' => $id]);
        session()->setFlashdata('success', 'Subsídiu atualiza ona!');
        return redirect()->back();
    }

    public function deleteSubsidiu($id)
    {
        $this->ApplicationModel->deleteData('subsidiu', ['id' => $id]);
        session()->setFlashdata('success', 'Subsídiu hamos ona!');
        return redirect()->back();
    }

    public function getPaymentStatus()
    {
        $fulan = $this->request->getVar('fulan');
        $tinan = $this->request->getVar('tinan');
        $data = $this->ApplicationModel->getFunsionariuPaymentStatus($fulan, $tinan);
        return $this->response->setJSON($data);
    }

    public function prosesaSalariu()
    {
        $funsionariu_id = $this->request->getPost('funsionariu_id');
        $fulan = $this->request->getPost('fulan');
        $tinan = $this->request->getPost('tinan');
        
        // Check if salary for this month/year already exists
        $check = $this->db->table('salariu')
            ->where('funsionariu_id', $funsionariu_id)
            ->where('fulan', $fulan)
            ->where('tinan', $tinan)
            ->get()->getRowArray();
            
        if ($check) {
            session()->setFlashdata('error', "Saláriu ba funsionáriu ne'e iha fulan/tinan ne'e prosesa ona!");
            return redirect()->back();
        }

        $data = [
            'funsionariu_id'   => $funsionariu_id,
            'fulan'            => $fulan,
            'tinan'            => $tinan,
            'salariu_baziku'   => $this->request->getPost('salariu_baziku'),
            'total_subsidiu'   => $this->request->getPost('total_subsidiu'),
            'total_deskontu'   => $this->request->getPost('total_deskontu'),
            'salariu_liquidu'  => $this->request->getPost('salariu_liquidu'),
            'estadu_pagamentu' => 'Selu Ona',
            'data_pagamentu'   => date('Y-m-d'),
            'created_at'       => date('Y-m-d H:i:s'),
        ];

        $this->db->transBegin();
        
        $this->ApplicationModel->saveData('salariu', $data);
        $salariu_id = $this->db->insertID();

        // Save details (Subsidies)
        $subsidiu_ids = $this->request->getPost('subsidiu_ids');
        if (!empty($subsidiu_ids)) {
            foreach ($subsidiu_ids as $sub_id) {
                $sub_data = $this->ApplicationModel->getSubsidiu($sub_id);
                $this->ApplicationModel->saveData('salariu_detallu', [
                    'salariu_id'       => $salariu_id,
                    'naran_komponente' => $sub_data['naran_subsidiu'],
                    'valór'            => $sub_data['valor_padrao'],
                    'tipu'             => 'Subsidiu'
                ]);
            }
        }
        
        // Save details (Discount if any)
        if ($data['total_deskontu'] > 0) {
             $this->ApplicationModel->saveData('salariu_detallu', [
                'salariu_id'       => $salariu_id,
                'naran_komponente' => 'Deskontu Jeral',
                'valór'            => $data['total_deskontu'],
                'tipu'             => 'Deskontu'
            ]);
        }

        // Save details (Sanction Deduction)
        $sansaun_dedusaun = $this->request->getPost('sansaun_dedusaun');
        if ($sansaun_dedusaun > 0) {
             $this->ApplicationModel->saveData('salariu_detallu', [
                'salariu_id'       => $salariu_id,
                'naran_komponente' => 'Potongan Sansaun',
                'valór'            => $sansaun_dedusaun,
                'tipu'             => 'Deskontu'
            ]);

            // Update Sanction records to reflect payment
            $active_sansauns = $this->db->table('sansaun')
                ->select('sansaun.*')
                ->join('tipu_sansaun', 'sansaun.tipu_sansaun_id = tipu_sansaun.id')
                ->where('funsionariu_id', $funsionariu_id)
                ->where('estadu_sansaun', 'Ativu')
                ->where('tipu_sansaun.kategoria', 'Korta Saláriu')
                ->where('valor_pagadu < valor_total')
                ->orderBy('data_sansaun', 'ASC')
                ->get()->getResultArray();
             
             $amount_to_pay = $sansaun_dedusaun;
             foreach ($active_sansauns as $as) {
                 if ($amount_to_pay <= 0) break;
                 $remaining = $as['valor_total'] - $as['valor_pagadu'];
                 $pay_now = min($amount_to_pay, $remaining);
                 
                 $new_pagadu = $as['valor_pagadu'] + $pay_now;
                 $up_data = [
                     'valor_pagadu' => $new_pagadu,
                     'updated_at'   => date('Y-m-d H:i:s')
                 ];
                 if ($new_pagadu >= $as['valor_total']) {
                     $up_data['estadu_sansaun'] = 'Konkluidu';
                 }
                 $this->db->table('sansaun')->where('id', $as['id'])->update($up_data);
                 $amount_to_pay -= $pay_now;
             }
        }

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Erro wainhira prosesa saláriu!');
        } else {
            $this->db->transCommit();
            session()->setFlashdata('success', 'Pagamentu saláriu prosesa ho susesu!');
        }

        return redirect()->back();
    }

    // Avizu & Sansaun
    public function avizu()
    {
        $data = array_merge($this->data, [
            'title' => 'Jestaun Avizu',
            'avizu' => $this->ApplicationModel->getAvizu(),
        ]);
        return view('pages/administrador/avizu', $data);
    }

    public function createAvizu()
    {
        $data = [
            'titulu' => $this->request->getVar('titulu'),
            'konteudu' => $this->request->getVar('konteudu'),
            'data_publikasaun' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->ApplicationModel->saveData('avizu', $data);
        session()->setFlashdata('success', 'Avizu foun publika ona!');
        return redirect()->back();
    }

    public function deleteAvizu($id)
    {
        $this->ApplicationModel->deleteData('avizu', ['id' => $id]);
        session()->setFlashdata('success', 'Avizu hamos ona!');
        return redirect()->back();
    }

    public function setExpiration($id)
    {
        $data_remata = $this->request->getPost('data_remata');
        $time_remata = $this->request->getPost('time_remata');
        
        $final_datetime = null;
        if (!empty($data_remata) && !empty($time_remata)) {
            $final_datetime = $data_remata . ' ' . $time_remata . ':00';
        }

        $this->ApplicationModel->updateData('avizu', ['data_remata' => $final_datetime], ['id' => $id]);
        session()->setFlashdata('success', 'Tempu penghapusan otomatis atualiza ona!');
        return redirect()->back();
    }

    public function sansaun()
    {
        $data = array_merge($this->data, [
            'title' => 'Jestaun Sansaun',
            'sansaun' => $this->ApplicationModel->getSansaun(),
            'funsionariu' => $this->ApplicationModel->getFunsionariu(),
            'tipu_sansaun' => $this->ApplicationModel->getTipuSansaun(),
            'pozisaun' => $this->ApplicationModel->getPozisaun(),
        ]);
        return view('pages/administrador/sansaun', $data);
    }

    public function createTipuSansaun()
    {
        $data = [
            'naran_tipu' => $this->request->getVar('naran_tipu'),
            'kategoria'  => $this->request->getVar('kategoria'),
            'valor_dedusaun' => $this->request->getVar('valor_dedusaun') ?? 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->ApplicationModel->saveData('tipu_sansaun', $data);
        session()->setFlashdata('success', 'Tipu sansaun foun aumenta ona!');
        return redirect()->back();
    }

    public function deleteTipuSansaun($id)
    {
        $this->ApplicationModel->deleteData('tipu_sansaun', ['id' => $id]);
        session()->setFlashdata('success', 'Tipu sansaun hamos ona!');
        return redirect()->back();
    }

    public function createSansaun()
    {
        $funsionariu_id = $this->request->getVar('funsionariu_id');
        $tipu_id = $this->request->getVar('tipu_sansaun_id');
        $tipu = $this->ApplicationModel->getTipuSansaun($tipu_id);

        $data = [
            'funsionariu_id' => $funsionariu_id,
            'tipu_sansaun_id' => $tipu_id,
            'motivu' => $this->request->getVar('motivu'),
            'data_sansaun' => $this->request->getVar('data_sansaun'),
            'valor_total' => $tipu['valor_dedusaun'] ?? 0,
            'estadu_sansaun' => 'Ativu',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Handle Hatun Pozisaun (Demotion)
        if ($tipu['kategoria'] == 'Hatun Pozisaun') {
            $funsionariu = $this->ApplicationModel->getFunsionariu($funsionariu_id);
            $new_pozisaun_id = $this->request->getVar('new_pozisaun_id');
            if ($new_pozisaun_id) {
                $new_pozisaun = $this->ApplicationModel->getPozisaun($new_pozisaun_id);
                // Store current position before changing it
                $data['pozisaun_anterior_id'] = $funsionariu['pozisaun_id'];
                
                $this->ApplicationModel->updateData('funsionariu', ['pozisaun_id' => $new_pozisaun_id], ['id' => $funsionariu_id]);
                $data['motivu'] .= " (Hatun pozisaun husi " . $funsionariu['naran_pozisaun'] . " ba " . $new_pozisaun['naran_pozisaun'] . ")";
                
                // Publish to announcement
                $this->ApplicationModel->saveData('avizu', [
                    'titulu' => 'Anúnsiu Sansaun: Hatun Pozisaun',
                    'konteudu' => "Funsionáriu " . $funsionariu['naran_kompletu'] . " (" . $funsionariu['nid'] . ") hetan sansaun Hatun Pozisaun husi " . $funsionariu['naran_pozisaun'] . " ba " . $new_pozisaun['naran_pozisaun'] . " tanba: " . $this->request->getVar('motivu'),
                    'data_publikasaun' => date('Y-m-d'),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $this->ApplicationModel->saveData('sansaun', $data);
        session()->setFlashdata('success', 'Sansaun foun fo ona ba funsionáriu!');
        return redirect()->back();
    }

    public function getSansaunDetail($id)
    {
        $data = $this->ApplicationModel->getSansaun($id);
        return $this->response->setJSON($data);
    }

    public function retiraSansaun($id)
    {
        $sansaun = $this->ApplicationModel->getSansaun($id);
        if (!$sansaun) {
            session()->setFlashdata('error', 'Dadus sansaun la konese!');
            return redirect()->back();
        }

        $this->db->transBegin();

        // Update Sansaun Status
        $this->ApplicationModel->updateData('sansaun', [
            'estadu_sansaun' => 'Retira',
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);

        // If it was a demotion, revert the position
        if ($sansaun['kategoria'] == 'Hatun Pozisaun' && !empty($sansaun['pozisaun_anterior_id'])) {
            $this->ApplicationModel->updateData('funsionariu', [
                'pozisaun_id' => $sansaun['pozisaun_anterior_id']
            ], ['id' => $sansaun['funsionariu_id']]);
            
            $pozisaun_foun = $this->ApplicationModel->getPozisaun($sansaun['pozisaun_anterior_id']);
            $funsionariu = $this->ApplicationModel->getFunsionariu($sansaun['funsionariu_id']);

            // Create announcement for position recovery
            $this->ApplicationModel->saveData('avizu', [
                'titulu' => 'Anúnsiu: Kansela Sansaun Hatun Pozisaun',
                'konteudu' => "Sansaun Hatun Pozisaun ba funsionáriu " . $sansaun['naran_kompletu'] . " (" . $sansaun['nid'] . ") retira ona. Pozisaun funsionáriu fila fali ba " . $pozisaun_foun['naran_pozisaun'] . ".",
                'data_publikasaun' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Generic withdrawal announcement
            $this->ApplicationModel->saveData('avizu', [
                'titulu' => 'Anúnsiu: Sansaun Retira',
                'konteudu' => "Sansaun (" . $sansaun['naran_tipu'] . ") ne'ebé fo ba funsionáriu " . $sansaun['naran_kompletu'] . " (" . $sansaun['nid'] . ") retira ona husi administrasaun.",
                'data_publikasaun' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Erro wainhira retira sansaun!');
        } else {
            $this->db->transCommit();
            session()->setFlashdata('success', 'Sansaun retira ona no pozisaun fila fali (se iha)!');
        }
        
        return redirect()->back();
    }

    public function jeraSansaunAbsensia()
    {
        $fulan = $this->request->getPost('fulan');
        $tinan = $this->request->getPost('tinan');

        if (!$fulan || !$tinan) {
            session()->setFlashdata('error', 'Favor hili fulan no tinan!');
            return redirect()->back();
        }

        $funsionariu = $this->ApplicationModel->getFunsionariu();
        $count_generated = 0;

        $this->db->transBegin();

        foreach ($funsionariu as $f) {
            // Count "Falta" for this employee in this month
            $falta_count = $this->db->table('prezensa')
                ->where('funsionariu_id', $f['id'])
                ->where('estadu_prezensa', 'Falta')
                ->where('MONTH(data_prezensa)', $fulan)
                ->where('YEAR(data_prezensa)', $tinan)
                ->countAllResults();

            if ($falta_count >= 3) {
                $multiplier = floor($falta_count / 3);
                $percentagem = $multiplier * 0.009; // 0.9% for every 3 faltas
                $valor_dedusaun = $f['salariu_baziku'] * $percentagem;

                // Check if sanction for this specific reason already exists to avoid duplication
                $motivu_identifikador = "Sansaun Absénsia: Falta dala $falta_count iha $fulan/$tinan";
                $check = $this->db->table('sansaun')
                    ->where('funsionariu_id', $f['id'])
                    ->where('motivu LIKE', "Sansaun Absénsia% %$fulan/$tinan")
                    ->get()->getRowArray();

                if (!$check && $valor_dedusaun > 0) {
                    // Get or create a generic 'Korta Saláriu' type for attendance
                    $tipu = $this->db->table('tipu_sansaun')
                        ->where('naran_tipu', 'Falta Absénsia')
                        ->get()->getRowArray();
                    
                    if (!$tipu) {
                        $this->db->table('tipu_sansaun')->insert([
                            'naran_tipu' => 'Falta Absénsia',
                            'kategoria' => 'Korta Saláriu',
                            'valor_dedusaun' => 0, // We set the total value in the sansaun record directly
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                        $tipu_id = $this->db->insertID();
                    } else {
                        $tipu_id = $tipu['id'];
                    }

                    $this->db->table('sansaun')->insert([
                        'funsionariu_id' => $f['id'],
                        'tipu_sansaun_id' => $tipu_id,
                        'motivu' => $motivu_identifikador,
                        'data_sansaun' => date('Y-m-d'),
                        'estadu_sansaun' => 'Ativu',
                        'valor_total' => $valor_dedusaun,
                        'valor_pagadu' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $count_generated++;
                }
            }
        }

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Erro wainhira jera sansaun absénsia!');
        } else {
            $this->db->transCommit();
            if ($count_generated > 0) {
                session()->setFlashdata('success', "Susesu jera sansaun absénsia ba funsionáriu na'in $count_generated!");
            } else {
                session()->setFlashdata('info', 'La iha sansaun foun ne\'ebé presiza jera ba fulan ne\'e.');
            }
        }

        return redirect()->back();
    }
}
