<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Repositories\DashboardRepository;
use App\Services\Storage\StorageManager;
use DateTimeImmutable;
use DateTimeZone;

class Administrador extends BaseController
{
    private function storage(): StorageManager
    {
        return StorageManager::fromConfig();
    }

    private function postText(string $key): string
    {
        return trim((string) $this->request->getPost($key));
    }

    private function backWithError(string $message)
    {
        session()->setFlashdata('error', $message);
        return redirect()->back()->withInput();
    }

    private function valueExists(string $table, string $column, string $value, ?int $excludeId = null): bool
    {
        $builder = $this->db->table($table)->where($column, $value);
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    private function findOrCreateId(string $table, string $column, string $value, array $defaults = []): int
    {
        $value = trim($value);
        $row = $this->db->table($table)->where($column, $value)->get()->getRowArray();
        if ($row) {
            return (int) $row['id'];
        }

        $this->db->table($table)->insert(array_merge($defaults, [$column => $value]));
        return (int) $this->db->insertID();
    }

    private function getOrCreatePayrollPeriod(int $fulan, int $tinan): array
    {
        $period = $this->db->table('payroll_periods')
            ->where('fulan', $fulan)
            ->where('tinan', $tinan)
            ->get()->getRowArray();

        if ($period) {
            return $period;
        }

        $this->db->table('payroll_periods')->insert([
            'fulan' => $fulan,
            'tinan' => $tinan,
            'status' => 'Draft',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->db->table('payroll_periods')->where('id', $this->db->insertID())->get()->getRowArray();
    }

    public function dashboard()
    {
        $repository = new DashboardRepository($this->db);
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Dili'));
        $start = $now->setTime(0, 0)->modify('-14 days');
        $trend = $repository->getAdminAttendanceTrend($start, $now->setTime(0, 0));
        $kpis = $repository->getAdminKpis($now);

        $data = array_merge($this->data, [
            'title' => 'Painel Administrador',
            'total_funsionariu' => (int) ($kpis['total_funsionariu'] ?? 0),
            'total_prezensa_ohin' => (int) ($kpis['total_prezensa_ohin'] ?? 0),
            'pendente_lisensa' => (int) ($kpis['pendente_lisensa'] ?? 0),
            'avizu_ikus' => $repository->getLatestAnnouncements(5, $now),
            'sansaun_ikus' => $repository->getLatestSanctions(5),
            'chart_labels' => json_encode($trend['labels'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
            'chart_prezente' => json_encode($trend['series']['Prezente']),
            'chart_loron_sorin' => json_encode($trend['series']['Loron Sorin']),
            'chart_falta' => json_encode($trend['series']['Falta']),
            'chart_lisensa' => json_encode($trend['series']['Lisensa']),
            'dept_comp' => json_encode($repository->getDepartmentComposition(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_NUMERIC_CHECK),
        ]);
        return view('pages/administrador/dashboard', $data);
    }


    // Jestaun Dadus Báziku
    public function diresaun()
    {
        $data = array_merge($this->data, [
            'title' => 'Jestaun Diresaun',
            'diresaun' => $this->ApplicationModel->getDiresaun(),
        ]);
        return view('pages/administrador/diresaun', $data);
    }

    public function createDiresaun()
    {
        $naran = $this->postText('naran_diresaun');
        if ($naran === '' || strlen($naran) > 100) {
            return $this->backWithError('Naran diresaun tenke prense no labele liu karakter 100.');
        }
        if ($this->valueExists('departamentu', 'naran_departamentu', $naran)) {
            return $this->backWithError('Diresaun ne\'e iha ona.');
        }

        $this->ApplicationModel->saveData('departamentu', ['naran_departamentu' => $naran]);
        session()->setFlashdata('success', 'Diresaun foun aumenta ona!');
        return redirect()->back();
    }

    public function updateDiresaun($id)
    {
        $id = (int) $id;
        $naran = $this->postText('naran_diresaun');
        if ($naran === '' || strlen($naran) > 100) {
            return $this->backWithError('Naran diresaun tenke prense no labele liu karakter 100.');
        }
        if ($this->valueExists('departamentu', 'naran_departamentu', $naran, $id)) {
            return $this->backWithError('Diresaun ne\'e iha ona.');
        }

        $this->ApplicationModel->updateData('departamentu', ['naran_departamentu' => $naran], ['id' => $id]);
        session()->setFlashdata('success', 'Diresaun atualiza ona!');
        return redirect()->back();
    }

    public function deleteDiresaun($id)
    {
        $check = $this->db->table('funsionariu')->where('departamentu_id', $id)->countAllResults();
        if ($check > 0) {
            session()->setFlashdata('error', 'La bele hamos diresaun ne\'e tanba iha funsionáriu ne\'ebé utiliza hela!');
            return redirect()->back();
        }

        $this->ApplicationModel->deleteData('departamentu', ['id' => $id]);
        session()->setFlashdata('success', 'Diresaun hamos ona!');
        return redirect()->back();
    }

    public function grau()
    {
        $data = array_merge($this->data, [
            'title' => 'Jestaun Grau',
            'grau' => $this->ApplicationModel->getGrau(),
        ]);
        return view('pages/administrador/grau', $data);
    }

    public function createGrau()
    {
        $naran = $this->postText('naran_grau');
        $salariu = $this->postText('salariu_baziku');
        if ($naran === '' || strlen($naran) > 100) {
            return $this->backWithError('Naran grau tenke prense no labele liu karakter 100.');
        }
        if ($this->valueExists('grau', 'naran_grau', $naran)) {
            return $this->backWithError('Grau ne\'e iha ona.');
        }
        if (!is_numeric($salariu) || (float) $salariu < 0) {
            return $this->backWithError('Salariu baziku tenke numeriku no labele negativu.');
        }

        $this->ApplicationModel->saveData('grau', [
            'naran_grau' => $naran,
            'salariu_baziku' => (float) $salariu,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        session()->setFlashdata('success', 'Grau foun aumenta ona!');
        return redirect()->back();
    }

    public function updateGrau($id)
    {
        $id = (int) $id;
        $naran = $this->postText('naran_grau');
        $salariu = $this->postText('salariu_baziku');
        if ($naran === '' || strlen($naran) > 100) {
            return $this->backWithError('Naran grau tenke prense no labele liu karakter 100.');
        }
        if ($this->valueExists('grau', 'naran_grau', $naran, $id)) {
            return $this->backWithError('Grau ne\'e iha ona.');
        }
        if (!is_numeric($salariu) || (float) $salariu < 0) {
            return $this->backWithError('Salariu baziku tenke numeriku no labele negativu.');
        }

        $this->ApplicationModel->updateData('grau', [
            'naran_grau' => $naran,
            'salariu_baziku' => (float) $salariu,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
        session()->setFlashdata('success', 'Grau atualiza ona!');
        return redirect()->back();
    }

    public function deleteGrau($id)
    {
        $check = $this->db->table('pozisaun')->where('grau_id', $id)->countAllResults();
        if ($check > 0) {
            session()->setFlashdata('error', 'La bele hamos grau ne\'e tanba iha pozisaun ne\'ebé utiliza hela!');
            return redirect()->back();
        }

        $this->ApplicationModel->deleteData('grau', ['id' => $id]);
        session()->setFlashdata('success', 'Grau hamos ona!');
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
        $naran = $this->postText('naran_pozisaun');
        if ($naran === '' || strlen($naran) > 100) {
            return $this->backWithError('Naran pozisaun tenke prense no labele liu karakter 100.');
        }
        if ($this->valueExists('pozisaun', 'naran_pozisaun', $naran)) {
            return $this->backWithError('Pozisaun ne\'e iha ona.');
        }

        $this->ApplicationModel->saveData('pozisaun', [
            'naran_pozisaun' => $naran,
        ]);
        session()->setFlashdata('success', 'Pozisaun foun aumenta ona!');
        return redirect()->back();
    }

    public function updatePozisaun($id)
    {
        $id = (int) $id;
        $naran = $this->postText('naran_pozisaun');
        if ($naran === '' || strlen($naran) > 100) {
            return $this->backWithError('Naran pozisaun tenke prense no labele liu karakter 100.');
        }
        if ($this->valueExists('pozisaun', 'naran_pozisaun', $naran, $id)) {
            return $this->backWithError('Pozisaun ne\'e iha ona.');
        }

        $this->ApplicationModel->updateData('pozisaun', [
            'naran_pozisaun' => $naran,
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
        $naran = $this->postText('naran_kategoria');
        if ($naran === '' || strlen($naran) > 100) {
            return $this->backWithError('Naran kategoria tenke prense no labele liu karakter 100.');
        }
        if ($this->valueExists('kategoria', 'naran_kategoria', $naran)) {
            return $this->backWithError('Kategoria ne\'e iha ona.');
        }

        $this->ApplicationModel->saveData('kategoria', ['naran_kategoria' => $naran]);
        session()->setFlashdata('success', 'Kategoria foun aumenta ona!');
        return redirect()->back();
    }

    public function updateKategoria($id)
    {
        $id = (int) $id;
        $naran = $this->postText('naran_kategoria');
        if ($naran === '' || strlen($naran) > 100) {
            return $this->backWithError('Naran kategoria tenke prense no labele liu karakter 100.');
        }
        if ($this->valueExists('kategoria', 'naran_kategoria', $naran, $id)) {
            return $this->backWithError('Kategoria ne\'e iha ona.');
        }

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
            'diresaun' => $this->ApplicationModel->getDiresaun(),
            'pozisaun' => $this->ApplicationModel->getPozisaun(),
            'grau' => $this->ApplicationModel->getGrau(),
            'kategoria' => $this->ApplicationModel->getKategoria(),
            'papel' => $this->ApplicationModel->getUserRole(),
        ]);
        return view('pages/administrador/funsionariu', $data);
    }

    public function saveFunsionariu()
    {
        if (!$this->validate([
            'nid' => 'required|max_length[50]|is_unique[funsionariu.nid]',
            'naran_kompletu' => 'required|max_length[150]',
            'username' => 'required|max_length[255]|is_unique[users.username]',
            'password' => 'required|min_length[8]',
            'foto_perfil' => 'if_exist|is_image[foto_perfil]|mime_in[foto_perfil,image/jpg,image/jpeg,image/png]|max_size[foto_perfil,2048]',
        ])) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }

        $employeeRole = $this->db->table('user_role')->where('role_name', 'funsionariu')->get()->getRowArray();
        $roleId = $employeeRole['id'] ?? $this->request->getVar('papel_id');

        // 1. Insert to users first
        $userData = [
            'fullname'         => $this->request->getVar('naran_kompletu'),
            'username'         => $this->request->getVar('username'),
            'password'         => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'role'             => $roleId,
            'status'           => 'active',
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
            'grau_id'           => $this->request->getVar('grau_id'),
            'kategoria_id'      => $this->request->getVar('kategoria_id'),
            'data_hahu_servisu' => $this->request->getVar('data_hahu_servisu'),
            'created_at'        => date('Y-m-d H:i:s'),
        ];

        // Handle File Upload
        $file = $this->request->getFile('foto_perfil');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/perfil', $newName);
            $funsionariuData['foto_perfil'] = $newName;
        }

        $this->ApplicationModel->saveData('funsionariu', $funsionariuData);
        session()->setFlashdata('success', 'Funsionáriu foun aumenta ona!');
        return redirect()->to(base_url('administrador/funsionariu'));
    }

    public function downloadFunsionariuTemplate()
    {
        $csv = "nid,naran_kompletu,seksu,fatin_moris,data_moris,hela_fatin,estadu_sivil,nu_telefone,diresaun,pozisaun,kategoria,data_hahu_servisu,username,password\n";
        $csv .= "2026001,Exemplo Funsionariu,Mane,Maucatar,1995-01-01,Maucatar,Solteiru,77000000,Administrasaun,Staff,Kategoria A,2026-01-01,2026001,Maucatar123\n";

        return $this->response->download('template_import_funsionariu.csv', $csv);
    }

    public function importFunsionariu()
    {
        if (!$this->validate([
            'file_import' => 'uploaded[file_import]|max_size[file_import,4096]|ext_in[file_import,csv,txt]|mime_in[file_import,text/plain,text/csv,application/vnd.ms-excel,application/octet-stream]',
        ])) {
            return $this->backWithError(implode(' ', $this->validator->getErrors()));
        }

        $file = $this->request->getFile('file_import');
        $handle = fopen($file->getTempName(), 'r');
        if (!$handle) {
            return $this->backWithError('Fail importasaun la bele lee.');
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return $this->backWithError('Header CSV la loos.');
        }

        $headers = array_map(static fn($h) => trim(strtolower($h)), $headers);
        $created = 0;
        $skipped = 0;
        $employeeRole = $this->db->table('user_role')->where('role_name', 'funsionariu')->get()->getRowArray();
        $roleId = $employeeRole['id'] ?? 3;

        $this->db->transBegin();
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, array_pad($row, count($headers), ''));
            if (!$data || empty($data['nid']) || empty($data['naran_kompletu']) || empty($data['username'])) {
                $skipped++;
                continue;
            }

            $nid = trim($data['nid']);
            $username = trim($data['username']);
            $exists = $this->db->table('funsionariu')->where('nid', $nid)->countAllResults() > 0
                || $this->db->table('users')->where('username', $username)->countAllResults() > 0;
            if ($exists) {
                $skipped++;
                continue;
            }

            $departamentuId = $this->findOrCreateId('departamentu', 'naran_departamentu', $data['diresaun'] ?: 'Administrasaun');
            $pozisaunId = $this->findOrCreateId('pozisaun', 'naran_pozisaun', $data['pozisaun'] ?: 'Staff');
            $kategoriaId = $this->findOrCreateId('kategoria', 'naran_kategoria', $data['kategoria'] ?: 'Kategoria A');

            $this->db->table('users')->insert([
                'fullname' => trim($data['naran_kompletu']),
                'username' => $username,
                'password' => password_hash($data['password'] ?: 'Maucatar123', PASSWORD_DEFAULT),
                'role' => $roleId,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $userId = $this->db->insertID();

            $this->db->table('funsionariu')->insert([
                'utilizador_id' => $userId,
                'nid' => $nid,
                'naran_kompletu' => trim($data['naran_kompletu']),
                'seksu' => $data['seksu'] ?: 'Mane',
                'fatin_moris' => $data['fatin_moris'] ?? null,
                'data_moris' => $data['data_moris'] ?: null,
                'hela_fatin' => $data['hela_fatin'] ?? null,
                'estadu_sivil' => $data['estadu_sivil'] ?? null,
                'nu_telefone' => $data['nu_telefone'] ?? null,
                'departamentu_id' => $departamentuId,
                'pozisaun_id' => $pozisaunId,
                'kategoria_id' => $kategoriaId,
                'data_hahu_servisu' => $data['data_hahu_servisu'] ?: null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $created++;
        }
        fclose($handle);

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return $this->backWithError('Importasaun falla.');
        }

        $this->db->transCommit();
        $this->logAudit('import_employees', 'funsionariu', null, null, ['created' => $created, 'skipped' => $skipped]);
        session()->setFlashdata('success', "Importasaun remata. Kria: $created, liu hela: $skipped.");
        return redirect()->back();
    }

    public function updateFunsionariu($id)
    {
        $funsionariu = $this->ApplicationModel->getFunsionariu($id);
        if (!$funsionariu) {
            session()->setFlashdata('error', 'Dadus la konese!');
            return redirect()->back();
        }

        if (!$this->validate([
            'nid' => "required|max_length[50]|is_unique[funsionariu.nid,id,{$id}]",
            'naran_kompletu' => 'required|max_length[150]',
            'username' => "required|max_length[255]|is_unique[users.username,id,{$funsionariu['utilizador_id']}]",
            'password' => 'permit_empty|min_length[8]',
            'foto_perfil' => 'if_exist|is_image[foto_perfil]|mime_in[foto_perfil,image/jpg,image/jpeg,image/png]|max_size[foto_perfil,2048]',
        ])) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }

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
            'grau_id'           => $this->request->getVar('grau_id'),
            'kategoria_id'      => $this->request->getVar('kategoria_id'),
            'data_hahu_servisu' => $this->request->getVar('data_hahu_servisu'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        // Handle File Upload
        $file = $this->request->getFile('foto_perfil');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/perfil', $newName);
            if (!empty($funsionariu['foto_perfil'])) {
                $oldPath = FCPATH . 'uploads/perfil/' . $funsionariu['foto_perfil'];
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $funsionariuData['foto_perfil'] = $newName;
        }

        $this->ApplicationModel->updateData('funsionariu', $funsionariuData, ['id' => $id]);

        // Update user account
        if ($funsionariu) {
            $employeeRole = $this->db->table('user_role')->where('role_name', 'funsionariu')->get()->getRowArray();
            $userData = [
                'fullname'   => $this->request->getVar('naran_kompletu'),
                'username'   => $this->request->getVar('username'),
                'role'       => $employeeRole['id'] ?? $this->request->getVar('papel_id'),
                'status'     => 'active',
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

    public function resetFunsionariuPassword($id)
    {
        $funsionariu = $this->ApplicationModel->getFunsionariu((int) $id);
        if (!$funsionariu || empty($funsionariu['utilizador_id'])) {
            return $this->backWithError('Funsionariu ka akun utilizador la konese.');
        }

        $password = (string) $this->request->getPost('password_baru');
        if (strlen($password) < 8 || $password !== (string) $this->request->getPost('password_konfirma')) {
            return $this->backWithError('Senha foun minimal 8 karakter no konfirmasaun tenke hanesan.');
        }

        $this->ApplicationModel->updateData('users', [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'failed_login_count' => 0,
            'locked_until' => null,
            'password_changed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $funsionariu['utilizador_id']]);

        $this->logAudit('admin_reset_employee_password', 'users', $funsionariu['utilizador_id']);
        session()->setFlashdata('success', 'Senha funsionariu troka ona.');
        return redirect()->back();
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
        $fields = $this->db->getFieldNames('attendance_settings');
        
        $data = [
            'tama_hahu'         => $this->request->getVar('tama_hahu'),
            'tama_remata'       => $this->request->getVar('tama_remata'),
            'sai_hahu'          => $this->request->getVar('sai_hahu'),
            'sai_remata'        => $this->request->getVar('sai_remata'),
            'toleransia_minutu' => $this->request->getVar('toleransia_minutu'),
            'sabadu'            => $this->request->getVar('sabadu') ? 1 : 0,
            'domingu'           => $this->request->getVar('domingu') ? 1 : 0,
            'tama_manual'       => $this->request->getVar('tama_manual') ? 1 : 0,
            'sai_manual'        => $this->request->getVar('sai_manual') ? 1 : 0,
            'updated_at'        => date('Y-m-d H:i:s'),
        ];
        
        // Add new dader/lokraik fields if they exist
        foreach ([
            'tama_hahu_dader', 'tama_remata_dader', 'sai_hahu_dader', 'sai_remata_dader',
            'tama_hahu_lokraik', 'tama_remata_lokraik', 'sai_hahu_lokraik', 'sai_remata_lokraik',
            'tama_manual_dader', 'sai_manual_dader', 'tama_manual_lokraik', 'sai_manual_lokraik',
        ] as $field) {
            if (in_array($field, $fields)) {
                if (str_starts_with($field, 'tama_manual') || str_starts_with($field, 'sai_manual')) {
                    $data[$field] = $this->request->getVar($field) ? 1 : 0;
                } else {
                    $data[$field] = $this->request->getVar($field);
                }
            }
        }
        
        $this->ApplicationModel->updateData('attendance_settings', $data, ['id' => 1]);

        // Automatically create an announcement
        $this->ApplicationModel->saveData('avizu', [
            'titulu' => 'Konfigurasaun Absénsia Foun',
            'konteudu' => 'Absénsia Dader: Tama loke husi ' . ($data['tama_hahu_dader'] ?? '08:00') . ' to\'o ' . ($data['tama_remata_dader'] ?? '09:00') . '. Sai loke husi ' . ($data['sai_hahu_dader'] ?? '12:00') . ' to\'o ' . ($data['sai_remata_dader'] ?? '13:00') . '. Absénsia Lokraik: Tama loke husi ' . ($data['tama_hahu_lokraik'] ?? '14:00') . ' to\'o ' . ($data['tama_remata_lokraik'] ?? '15:00') . '. Sai loke husi ' . ($data['sai_hahu_lokraik'] ?? '17:00') . ' to\'o ' . ($data['sai_remata_lokraik'] ?? '18:00') . '. Favor absénte iha tempu ne\'ebé konese!',
            'data_publikasaun' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Konfigurasaun prezensa atualiza ona!');
        return redirect()->back();
    }

    public function feriadu()
    {
        $holidays = $this->db->table('holidays')
            ->orderBy('holiday_date', 'DESC')
            ->get()
            ->getResultArray();

        return view('pages/administrador/feriadu', array_merge($this->data, [
            'title' => 'Jestaun Feriadu',
            'holidays' => $holidays,
        ]));
    }

    public function createFeriadu()
    {
        if (!$this->validate([
            'holiday_date' => 'required|valid_date[Y-m-d]',
            'title' => 'required|max_length[150]',
            'description' => 'permit_empty|max_length[1000]',
        ])) {
            return $this->backWithError(implode(' ', $this->validator->getErrors()));
        }

        $date = $this->request->getPost('holiday_date');
        if ($this->db->table('holidays')->where('holiday_date', $date)->countAllResults() > 0) {
            return $this->backWithError('Feriadu iha data nee iha ona.');
        }

        $data = [
            'holiday_date' => $date,
            'title' => $this->postText('title'),
            'description' => $this->postText('description'),
            'created_by' => session()->get('userID'),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->ApplicationModel->saveData('holidays', $data);
        $this->logAudit('create_holiday', 'holidays', $this->db->insertID(), null, $data);
        
        // Automatically create an announcement (avizu)
        $date_formatted = date('d-m-Y', strtotime($date));
        $announcement_title = 'Feriadu Foun: ' . $this->postText('title');
        $announcement_content = 'Halo konesimentu ba hotu-hotu funsionáriu katak iha feriadu foun iha loron ' . $date_formatted;
        if (!empty($this->postText('description'))) {
            $announcement_content .= ': ' . $this->postText('description');
        }
        $announcement_content .= '.';
        
        $this->ApplicationModel->saveData('avizu', [
            'titulu' => $announcement_title,
            'konteudu' => $announcement_content,
            'data_publikasaun' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Feriadu aumenta ona.');
        return redirect()->back();
    }

    public function deleteFeriadu($id)
    {
        $holiday = $this->db->table('holidays')->where('id', (int) $id)->get()->getRowArray();
        if (!$holiday) {
            return $this->backWithError('Feriadu la konese.');
        }

        $this->ApplicationModel->deleteData('holidays', ['id' => (int) $id]);
        $this->logAudit('delete_holiday', 'holidays', $id, $holiday, null);
        session()->setFlashdata('success', 'Feriadu hamos ona.');
        return redirect()->back();
    }

    // Jestaun Lisensa
    public function lisensa()
    {
        $data = array_merge($this->data, [
            'title'       => 'Jestaun Lisensa',
            'lisensa'     => $this->ApplicationModel->getLisensa(),
            'funsionariu' => $this->ApplicationModel->getFunsionariu(),
            'tipu_lisensa' => $this->ApplicationModel->getTipuLisensa(),
        ]);
        return view('pages/administrador/lisensa', $data);
    }

    public function adminCreateLisensa()
    {
        if (!$this->validate([
            'funsionariu_id' => 'required|is_natural_no_zero',
            'tipu_lisensa'   => 'required|max_length[100]',
            'sesaun'         => 'required|in_list[Loron Tomak,Dader,Lokraik]',
            'data_hahu'      => 'required|valid_date[Y-m-d]',
            'data_remata'    => 'required|valid_date[Y-m-d]',
            'razaun'         => 'required|min_length[3]|max_length[1000]',
        ])) {
            return $this->backWithError(implode(' ', $this->validator->getErrors()));
        }

        $funsionariu_id = (int) $this->request->getPost('funsionariu_id');
        $tipu_lisensa   = $this->postText('tipu_lisensa');
        $sesaun         = $this->postText('sesaun');
        $data_hahu      = $this->request->getPost('data_hahu');
        $data_remata    = $this->request->getPost('data_remata');
        $razaun         = $this->postText('razaun');

        // Validate date range
        if ($data_remata < $data_hahu) {
            return $this->backWithError('Data remata la bele kiik liu data hahu.');
        }

        // Half-day: only one day allowed
        if (in_array($sesaun, ['Dader', 'Lokraik'], true) && $data_hahu !== $data_remata) {
            return $this->backWithError('Lisensa Dader/Lokraik deit bele ba loron ida de\'it.');
        }

        // Validate funsionariu exists
        $funsionariu = $this->db->table('funsionariu')->where('id', $funsionariu_id)->get()->getRowArray();
        if (!$funsionariu) {
            return $this->backWithError('Funsionáriu la konese.');
        }

        // Save as Aprovadu immediately (admin-created)
        $record = [
            'funsionariu_id' => $funsionariu_id,
            'tipu_lisensa'   => $tipu_lisensa,
            'sesaun'         => $sesaun,
            'data_hahu'      => $data_hahu,
            'data_remata'    => $data_remata,
            'razaun'         => $razaun,
            'estadu_lisensa' => 'Aprovadu',
            'komentariu_admin' => 'Kria diretamente husi Administrasaun.',
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        $this->ApplicationModel->saveData('lisensa', $record);
        $newId = $this->db->insertID();

        // Update prezensa records to mark as Lisensa
        if ($sesaun === 'Loron Tomak') {
            $dates = [];
            $cur = strtotime($data_hahu);
            $end = strtotime($data_remata);
            while ($cur <= $end) {
                $dates[] = date('Y-m-d', $cur);
                $cur = strtotime('+1 day', $cur);
            }
            foreach ($dates as $d) {
                $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('data_prezensa', $d)
                    ->update(['estadu_prezensa' => 'Lisensa']);
            }
        } else {
            $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('data_prezensa', $data_hahu)
                ->update(['estadu_prezensa' => 'Loron Sorin']);
        }

        // Recalculate leave balance
        for ($year = (int) date('Y', strtotime($data_hahu)); $year <= (int) date('Y', strtotime($data_remata)); $year++) {
            $this->ApplicationModel->recalculateLeaveBalance($funsionariu_id, $tipu_lisensa, $year);
        }

        $this->logAudit('admin_create_lisensa', 'lisensa', $newId, null, $record);
        session()->setFlashdata('success', "Lisensa ba {$funsionariu['naran_kompletu']} kria no aprovadu ona!");
        return redirect()->back();
    }

    public function leaveBalance()
    {
        $year = (int) ($this->request->getGet('year') ?: date('Y'));
        $data = array_merge($this->data, [
            'title' => 'Balansu Lisensa',
            'year' => $year,
            'balances' => $this->ApplicationModel->getLeaveBalances(year: $year),
            'funsionariu' => $this->ApplicationModel->getFunsionariu(),
            'tipu_lisensa' => $this->ApplicationModel->getTipuLisensa(),
        ]);

        return view('pages/administrador/leave_balance', $data);
    }

    public function generateLeaveBalance()
    {
        $year = (int) $this->request->getPost('year');
        $leaveType = $this->postText('leave_type') ?: 'Anuál';
        $entitlement = max(0, (float) $this->request->getPost('entitlement_days'));

        if ($year < 2000 || $year > 2100 || $entitlement <= 0) {
            return $this->backWithError('Tinan ka entitlement la loos.');
        }

        $created = 0;
        foreach ($this->ApplicationModel->getFunsionariu() as $employee) {
            $before = $this->db->table('leave_balances')
                ->where('funsionariu_id', $employee['id'])
                ->where('leave_type', $leaveType)
                ->where('year', $year)
                ->countAllResults();
            $this->ApplicationModel->ensureLeaveBalance((int) $employee['id'], $leaveType, $year, $entitlement);
            $this->ApplicationModel->recalculateLeaveBalance((int) $employee['id'], $leaveType, $year);
            if ($before === 0) {
                $created++;
            }
        }

        $this->logAudit('generate_leave_balance', 'leave_balances', null, null, [
            'year' => $year,
            'leave_type' => $leaveType,
            'entitlement_days' => $entitlement,
            'created' => $created,
        ]);
        session()->setFlashdata('success', "Balansu lisensa generate ona. Record foun: $created.");
        return redirect()->to(base_url('administrador/lisensa/balansu?year=' . $year));
    }

    public function updateLeaveBalance($id)
    {
        $balance = $this->db->table('leave_balances')->where('id', (int) $id)->get()->getRowArray();
        if (!$balance) {
            return $this->backWithError('Balansu lisensa la konese.');
        }

        $entitlement = max(0, (float) $this->request->getPost('entitlement_days'));
        $this->ApplicationModel->updateData('leave_balances', [
            'entitlement_days' => $entitlement,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => (int) $id]);
        $this->ApplicationModel->recalculateLeaveBalance((int) $balance['funsionariu_id'], $balance['leave_type'], (int) $balance['year']);
        $this->logAudit('update_leave_balance', 'leave_balances', $id, $balance, ['entitlement_days' => $entitlement]);

        session()->setFlashdata('success', 'Balansu lisensa atualiza ona.');
        return redirect()->back();
    }

    public function createTipuLisensa()
    {
        $naran = $this->postText('naran_tipu');
        if ($naran === '' || strlen($naran) > 100) {
            return $this->backWithError('Naran tipu lisensa tenke prense no labele liu karakter 100.');
        }

        if ($this->valueExists('tipu_lisensa', 'naran_tipu', $naran)) {
            return $this->backWithError('Tipu lisensa nee iha ona.');
        }

        $this->ApplicationModel->saveData('tipu_lisensa', [
            'naran_tipu' => $naran,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Tipu lisensa foun aumenta ona!');
        return redirect()->back();
    }

    public function updateTipuLisensa($id)
    {
        $naran = $this->postText('naran_tipu');
        if ($naran === '' || strlen($naran) > 100) {
            return $this->backWithError('Naran tipu lisensa tenke prense no labele liu karakter 100.');
        }

        if ($this->valueExists('tipu_lisensa', 'naran_tipu', $naran, $id)) {
            return $this->backWithError('Tipu lisensa ho naran nee iha ona.');
        }

        $this->ApplicationModel->updateData('tipu_lisensa', [
            'naran_tipu' => $naran,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);

        session()->setFlashdata('success', 'Tipu lisensa atualiza ona!');
        return redirect()->back();
    }

    public function deleteTipuLisensa($id)
    {
        $tipu = $this->ApplicationModel->getTipuLisensa($id);
        if (!$tipu) {
            return $this->backWithError('Tipu lisensa la konese.');
        }

        $naranTipu = $tipu['naran_tipu'];

        // Count records to inform audit log
        $totalBalansu = $this->db->table('leave_balances')->where('leave_type', $naranTipu)->countAllResults();
        $totalLisensa = $this->db->table('lisensa')->where('tipu_lisensa', $naranTipu)->countAllResults();

        // Cascade delete: remove all leave_balances with this leave_type
        $this->db->table('leave_balances')->where('leave_type', $naranTipu)->delete();

        // Cascade delete: remove all lisensa requests with this leave_type
        $this->db->table('lisensa')->where('tipu_lisensa', $naranTipu)->delete();

        // Finally delete the tipu_lisensa itself
        $this->ApplicationModel->deleteData('tipu_lisensa', ['id' => $id]);

        $this->logAudit('delete_tipu_lisensa', 'tipu_lisensa', $id, $tipu, [
            'balansu_deleted' => $totalBalansu,
            'lisensa_deleted' => $totalLisensa,
        ]);

        session()->setFlashdata('success', "Tipu lisensa '{$naranTipu}' hamos ona! (Balansu hamos: {$totalBalansu}, Pedidu hamos: {$totalLisensa})");
        return redirect()->back();
    }

    public function aprovaLisensa($id)
    {
        $estadu = $this->request->getPost('estadu_lisensa');
        $komentariu = trim((string) $this->request->getPost('komentariu_admin'));

        if (!in_array($estadu, ['Aprovadu', 'Rezeitadu', 'Pendente'], true)) {
            session()->setFlashdata('error', 'Estadu lisensa la loos.');
            return redirect()->back();
        }

        if ($estadu === 'Rezeitadu' && $komentariu === '') {
            session()->setFlashdata('error', 'Komentariu admin obrigatoriu bainhira reject lisensa.');
            return redirect()->back();
        }

        $lisensa = $this->ApplicationModel->getLisensa($id);
        if (!$lisensa) {
            session()->setFlashdata('error', 'Dadus lisensa la konese.');
            return redirect()->back();
        }

        $this->db->transBegin();
        $this->ApplicationModel->updateData('lisensa', [
            'estadu_lisensa'   => $estadu,
            'komentariu_admin' => $komentariu,
            'updated_at'       => date('Y-m-d H:i:s')
        ], ['id' => $id]);

        if ($estadu === 'Aprovadu') {
            $sesaun = $lisensa['sesaun'] ?? 'Loron Tomak';
            $start = new \DateTime($lisensa['data_hahu']);
            $end = new \DateTime($lisensa['data_remata']);
            $period = new \DatePeriod($start, new \DateInterval('P1D'), $end->modify('+1 day'));

            foreach ($period as $date) {
                $checkDate = $date->format('Y-m-d');
                $existing = $this->db->table('prezensa')
                    ->where('funsionariu_id', $lisensa['funsionariu_id'])
                    ->where('data_prezensa', $checkDate)
                    ->get()->getRowArray();

                if ($sesaun === 'Loron Tomak') {
                    // Full-day: block if employee already attended
                    if ($existing && in_array($existing['estadu_prezensa'], ['Prezente', 'Loron Sorin'], true)) {
                        $this->db->transRollback();
                        session()->setFlashdata('error', 'Lisensa la bele aprova tanba iha data neebe funsionariu prezente/loron sorin ona.');
                        return redirect()->back();
                    }
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
                } elseif ($sesaun === 'Dader') {
                    // Dader: block if already clocked in for Dader
                    if ($existing && !empty($existing['oras_tama_dader'])) {
                        $this->db->transRollback();
                        session()->setFlashdata('error', 'Lisensa Dader la bele aprova tanba funsionariu tama dader ona iha ' . $checkDate . '.');
                        return redirect()->back();
                    }
                    $updateFields = [
                        'oras_tama_dader' => 'LISENSA',
                        'oras_sai_dader'  => 'LISENSA',
                        'updated_at'      => date('Y-m-d H:i:s'),
                    ];
                    if ($existing) {
                        // Recalculate status: if lokraik already done => Prezente
                        $hasTamaLokraik = !empty($existing['oras_tama_lokraik']);
                        $hasSaiLokraik  = !empty($existing['oras_sai_lokraik']);
                        $newStatus = ($hasTamaLokraik && $hasSaiLokraik) ? 'Prezente' : 'Loron Sorin';
                        $updateFields['estadu_prezensa'] = $newStatus;
                        $this->ApplicationModel->updateData('prezensa', $updateFields, ['id' => $existing['id']]);
                    } else {
                        $this->ApplicationModel->saveData('prezensa', array_merge($updateFields, [
                            'funsionariu_id'  => $lisensa['funsionariu_id'],
                            'data_prezensa'   => $checkDate,
                            'estadu_prezensa' => 'Loron Sorin',
                            'created_at'      => date('Y-m-d H:i:s'),
                        ]));
                    }
                } elseif ($sesaun === 'Lokraik') {
                    // Lokraik: block if already clocked in for Lokraik
                    if ($existing && !empty($existing['oras_tama_lokraik'])) {
                        $this->db->transRollback();
                        session()->setFlashdata('error', 'Lisensa Lokraik la bele aprova tanba funsionariu tama lokraik ona iha ' . $checkDate . '.');
                        return redirect()->back();
                    }
                    $updateFields = [
                        'oras_tama_lokraik' => 'LISENSA',
                        'oras_sai_lokraik'  => 'LISENSA',
                        'updated_at'        => date('Y-m-d H:i:s'),
                    ];
                    if ($existing) {
                        // Recalculate status: if dader already done => Prezente
                        $hasTamaDader = !empty($existing['oras_tama_dader']);
                        $hasSaiDader  = !empty($existing['oras_sai_dader']);
                        $newStatus = ($hasTamaDader && $hasSaiDader) ? 'Prezente' : 'Loron Sorin';
                        $updateFields['estadu_prezensa'] = $newStatus;
                        $this->ApplicationModel->updateData('prezensa', $updateFields, ['id' => $existing['id']]);
                    } else {
                        $this->ApplicationModel->saveData('prezensa', array_merge($updateFields, [
                            'funsionariu_id'  => $lisensa['funsionariu_id'],
                            'data_prezensa'   => $checkDate,
                            'estadu_prezensa' => 'Loron Sorin',
                            'created_at'      => date('Y-m-d H:i:s'),
                        ]));
                    }
                }
            }
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Erro wainhira atualiza lisensa.');
        } else {
            $this->db->transCommit();
            $this->logAudit('update_lisensa_status', 'lisensa', $id, $lisensa, [
                'estadu_lisensa' => $estadu,
                'komentariu_admin' => $komentariu,
            ]);
            for ($year = (int) date('Y', strtotime($lisensa['data_hahu'])); $year <= (int) date('Y', strtotime($lisensa['data_remata'])); $year++) {
                $this->ApplicationModel->recalculateLeaveBalance((int) $lisensa['funsionariu_id'], $lisensa['tipu_lisensa'], $year);
            }
            session()->setFlashdata('success', 'Estadu lisensa atualiza ona!');
        }

        return redirect()->back();
    }

    // Jestaun Saláriu
    public function salariu()
    {
        $salariu = $this->ApplicationModel->getSalariu();
        $data = array_merge($this->data, [
            'title' => 'Jestaun Saláriu',
            'salariu' => $salariu,
            'salariu_detallu' => $this->ApplicationModel->getSalariuDetalluBySalariuIds(array_column($salariu, 'id')),
            'funsionariu' => $this->ApplicationModel->getFunsionariu(),
            'subsidiu' => $this->ApplicationModel->getSubsidiu(),
            'pozisaun' => $this->ApplicationModel->getPozisaun(),
            'payroll_periods' => $this->db->table('payroll_periods')->orderBy('tinan', 'DESC')->orderBy('fulan', 'DESC')->get()->getResultArray(),
        ]);
        return view('pages/administrador/salariu', $data);
    }

    public function lockPayrollPeriod()
    {
        $fulan = (int) $this->request->getPost('fulan');
        $tinan = (int) $this->request->getPost('tinan');
        if ($fulan < 1 || $fulan > 12 || $tinan < 2000) {
            return $this->backWithError('Periodu saláriu la loos.');
        }

        $period = $this->getOrCreatePayrollPeriod($fulan, $tinan);
        $this->ApplicationModel->updateData('payroll_periods', [
            'status' => 'Locked',
            'locked_by' => session()->get('userID'),
            'locked_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $period['id']]);

        $this->logAudit('lock_payroll_period', 'payroll_periods', $period['id'], $period, ['status' => 'Locked']);
        session()->setFlashdata('success', 'Periodu saláriu taka ona.');
        return redirect()->back();
    }

    public function unlockPayrollPeriod()
    {
        $fulan = (int) $this->request->getPost('fulan');
        $tinan = (int) $this->request->getPost('tinan');
        $period = $this->getOrCreatePayrollPeriod($fulan, $tinan);

        $this->ApplicationModel->updateData('payroll_periods', [
            'status' => 'Draft',
            'locked_by' => null,
            'locked_at' => null,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $period['id']]);

        $this->logAudit('unlock_payroll_period', 'payroll_periods', $period['id'], $period, ['status' => 'Draft']);
        session()->setFlashdata('success', 'Periodu saláriu loke ona.');
        return redirect()->back();
    }

    public function createSubsidiu()
    {
        $naran = $this->postText('naran_subsidiu');
        $valor = $this->postText('valor_padrao');
        $deskrisaun = $this->postText('deskrisaun');
        $pozisaun_id = $this->postText('pozisaun_id');
        $tipu_valor = $this->postText('tipu_valor');

        if ($naran === '' || strlen($naran) > 100) {
            return $this->backWithError('Naran subsidiu tenke prense no labele liu karakter 100.');
        }
        if ($this->valueExists('subsidiu', 'naran_subsidiu', $naran)) {
            return $this->backWithError('Subsidiu ne\'e iha ona.');
        }
        if (!is_numeric($valor) || (float) $valor < 0) {
            return $this->backWithError('Valor padrao tenke numeriku no labele negativu.');
        }

        $data = [
            'naran_subsidiu' => $naran,
            'pozisaun_id'    => $pozisaun_id ? (int) $pozisaun_id : null,
            'tipu_valor'     => $tipu_valor ?: 'Fiksu',
            'valor_padrao'   => (float) $valor,
            'deskrisaun'     => $deskrisaun,
            'created_at'     => date('Y-m-d H:i:s'),
        ];
        $this->ApplicationModel->saveData('subsidiu', $data);
        session()->setFlashdata('success', 'Subsídiu foun aumenta ona!');
        return redirect()->back();
    }

    public function updateSubsidiu($id)
    {
        $id = (int) $id;
        $naran = $this->postText('naran_subsidiu');
        $valor = $this->postText('valor_padrao');
        $deskrisaun = $this->postText('deskrisaun');
        $pozisaun_id = $this->postText('pozisaun_id');
        $tipu_valor = $this->postText('tipu_valor');

        if ($naran === '' || strlen($naran) > 100) {
            return $this->backWithError('Naran subsidiu tenke prense no labele liu karakter 100.');
        }
        if ($this->valueExists('subsidiu', 'naran_subsidiu', $naran, $id)) {
            return $this->backWithError('Subsidiu ne\'e iha ona.');
        }
        if (!is_numeric($valor) || (float) $valor < 0) {
            return $this->backWithError('Valor padrao tenke numeriku no labele negativu.');
        }

        $data = [
            'naran_subsidiu' => $naran,
            'pozisaun_id'    => $pozisaun_id ? (int) $pozisaun_id : null,
            'tipu_valor'     => $tipu_valor ?: 'Fiksu',
            'valor_padrao'   => (float) $valor,
            'deskrisaun'     => $deskrisaun,
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
        $funsionariu_id = (int) $this->request->getPost('funsionariu_id');
        $fulan = (int) $this->request->getPost('fulan');
        $tinan = (int) $this->request->getPost('tinan');
        $manual_deskontu = max(0, (float) $this->request->getPost('total_deskontu'));
        $subsidiu_ids = $this->request->getPost('subsidiu_ids') ?? [];

        if ($funsionariu_id <= 0 || $fulan < 1 || $fulan > 12 || $tinan < 2000) {
            session()->setFlashdata('error', 'Dadus periodu pagamentu la loos.');
            return redirect()->back();
        }

        $period = $this->getOrCreatePayrollPeriod($fulan, $tinan);
        if (($period['status'] ?? 'Draft') === 'Locked') {
            session()->setFlashdata('error', 'Periodu saláriu ne\'e taka ona. La bele prosesa pagamentu foun.');
            return redirect()->back();
        }

        $funsionariu = $this->ApplicationModel->getFunsionariu($funsionariu_id);
        if (!$funsionariu) {
            session()->setFlashdata('error', 'Funsionariu la konese.');
            return redirect()->back();
        }

        $check = $this->db->table('salariu')
            ->where('funsionariu_id', $funsionariu_id)
            ->where('fulan', $fulan)
            ->where('tinan', $tinan)
            ->get()->getRowArray();

        if ($check) {
            session()->setFlashdata('error', 'Salariu ba funsionariu nee iha fulan/tinan nee prosesa ona!');
            return redirect()->back();
        }

        $salariu_baziku = (float) ($funsionariu['salariu_baziku'] ?? 0);
        $selectedSubsidiu = [];
        $total_subsidiu = 0.0;
        foreach ($subsidiu_ids as $sub_id) {
            $sub_data = $this->ApplicationModel->getSubsidiu((int) $sub_id);
            if (!$sub_data) {
                continue;
            }
            
            if (!empty($sub_data['pozisaun_id']) && $sub_data['pozisaun_id'] != $funsionariu['pozisaun_id']) {
                continue;
            }

            $valor_subsidiu = 0.0;
            if (isset($sub_data['tipu_valor']) && $sub_data['tipu_valor'] === 'Persentajen') {
                $valor_subsidiu = ($salariu_baziku * (float) $sub_data['valor_padrao']) / 100;
            } else {
                $valor_subsidiu = (float) $sub_data['valor_padrao'];
            }

            $sub_data['calculated_valor'] = $valor_subsidiu;
            $selectedSubsidiu[] = $sub_data;
            $total_subsidiu += $valor_subsidiu;
        }

        $active_sansauns = $this->db->table('sansaun')
            ->select('sansaun.*')
            ->join('tipu_sansaun', 'sansaun.tipu_sansaun_id = tipu_sansaun.id')
            ->where('funsionariu_id', $funsionariu_id)
            ->where('estadu_sansaun', 'Ativu')
            ->groupStart()
                ->where('tipu_sansaun.kategoria', 'Korta Saláriu')
                ->orWhere('tipu_sansaun.kategoria', 'Korta Salariu')
                ->orWhere('tipu_sansaun.kategoria', 'salary_deduction')
            ->groupEnd()
            ->where('valor_pagadu < valor_total')
            ->orderBy('data_sansaun', 'ASC')
            ->get()->getResultArray();

        $sansaun_outstanding = 0.0;
        foreach ($active_sansauns as $sansaun) {
            $sansaun_outstanding += max(0, (float) $sansaun['valor_total'] - (float) $sansaun['valor_pagadu']);
        }

        $available_before_sanction = max(0, $salariu_baziku + $total_subsidiu - $manual_deskontu);
        $sansaun_dedusaun = min($sansaun_outstanding, $available_before_sanction);
        $total_deskontu = $manual_deskontu + $sansaun_dedusaun;
        $salariu_liquidu = max(0, $salariu_baziku + $total_subsidiu - $total_deskontu);

        $data = [
            'funsionariu_id'   => $funsionariu_id,
            'payroll_period_id' => $period['id'],
            'fulan'            => $fulan,
            'tinan'            => $tinan,
            'salariu_baziku'   => $salariu_baziku,
            'total_subsidiu'   => $total_subsidiu,
            'total_deskontu'   => $total_deskontu,
            'salariu_liquidu'  => $salariu_liquidu,
            'estadu_pagamentu' => 'Selu Ona',
            'data_pagamentu'   => date('Y-m-d'),
            'processed_by'      => session()->get('userID'),
            'processed_at'      => date('Y-m-d H:i:s'),
            'created_at'       => date('Y-m-d H:i:s'),
        ];

        $this->db->transBegin();
        $this->ApplicationModel->saveData('salariu', $data);
        $salariu_id = $this->db->insertID();

        foreach ($selectedSubsidiu as $sub_data) {
            $this->ApplicationModel->saveData('salariu_detallu', [
                'salariu_id'       => $salariu_id,
                'naran_komponente' => $sub_data['naran_subsidiu'],
                'valor'            => $sub_data['calculated_valor'] ?? $sub_data['valor_padrao'],
                'tipu'             => 'Subsidiu'
            ]);
        }

        if ($manual_deskontu > 0) {
            $this->ApplicationModel->saveData('salariu_detallu', [
                'salariu_id'       => $salariu_id,
                'naran_komponente' => 'Deskontu Jeral',
                'valor'            => $manual_deskontu,
                'tipu'             => 'Deskontu'
            ]);
        }

        if ($sansaun_dedusaun > 0) {
            $this->ApplicationModel->saveData('salariu_detallu', [
                'salariu_id'       => $salariu_id,
                'naran_komponente' => 'Potongan Sansaun',
                'valor'            => $sansaun_dedusaun,
                'tipu'             => 'Deskontu'
            ]);

            $amount_to_pay = $sansaun_dedusaun;
            foreach ($active_sansauns as $as) {
                if ($amount_to_pay <= 0) {
                    break;
                }
                $remaining = (float) $as['valor_total'] - (float) $as['valor_pagadu'];
                $pay_now = min($amount_to_pay, $remaining);
                $new_pagadu = (float) $as['valor_pagadu'] + $pay_now;
                $up_data = [
                    'valor_pagadu' => $new_pagadu,
                    'updated_at'   => date('Y-m-d H:i:s')
                ];
                if ($new_pagadu >= (float) $as['valor_total']) {
                    $up_data['estadu_sansaun'] = 'Konkluidu';
                }
                $this->db->table('sansaun')->where('id', $as['id'])->update($up_data);
                $amount_to_pay -= $pay_now;
            }
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Erro wainhira prosesa salariu!');
        } else {
            $this->db->transCommit();
            $this->logAudit('process_payroll', 'salariu', $salariu_id, null, [
                'funsionariu_id' => $funsionariu_id,
                'fulan' => $fulan,
                'tinan' => $tinan,
                'salariu_baziku' => $salariu_baziku,
                'total_subsidiu' => $total_subsidiu,
                'total_deskontu' => $total_deskontu,
                'salariu_liquidu' => $salariu_liquidu,
            ]);
            session()->setFlashdata('success', 'Pagamentu salariu prosesa ho susesu!');
        }

        return redirect()->back();
    }

    // Anunsiu & Sansaun
    public function avizu()
    {
        $data = array_merge($this->data, [
            'title' => 'Jestaun Anunsiu',
            'avizu' => $this->ApplicationModel->getAvizu(),
        ]);
        return view('pages/administrador/avizu', $data);
    }

    public function createAvizu()
    {
        $titulu = $this->postText('titulu');
        $konteudu = $this->postText('konteudu');

        if ($titulu === '' || strlen($titulu) > 150) {
            return $this->backWithError('Titulu anunsiu tenke prense no labele liu karakter 150.');
        }

        if ($konteudu === '' || strlen($konteudu) > 5000) {
            return $this->backWithError('Konteudu anunsiu tenke prense no labele liu karakter 5000.');
        }

        $data = [
            'titulu' => $titulu,
            'konteudu' => $konteudu,
            'data_publikasaun' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->fieldExists('published_at', 'avizu')) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }
        if ($this->db->fieldExists('created_by', 'avizu')) {
            $data['created_by'] = session()->get('userID');
        }
        if ($this->db->fieldExists('status', 'avizu')) {
            $data['status'] = 'Published';
        }

        $this->ApplicationModel->saveData('avizu', $data);
        session()->setFlashdata('success', 'Anunsiu foun publika ona!');
        return redirect()->back();
    }

    public function deleteAvizu($id)
    {
        $this->ApplicationModel->deleteData('avizu', ['id' => $id]);
        session()->setFlashdata('success', 'Anunsiu hamos ona!');
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
        session()->setFlashdata('success', 'Tempu remata anunsiu atualiza ona!');
        return redirect()->back();
    }

    public function documentu()
    {
        $documents = $this->db->table('employee_documents')
            ->select('employee_documents.*, funsionariu.nid, funsionariu.naran_kompletu')
            ->join('funsionariu', 'employee_documents.funsionariu_id = funsionariu.id', 'left')
            ->where('employee_documents.deleted_at', null)
            ->orderBy('employee_documents.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = array_merge($this->data, [
            'title' => 'Jestaun Dokumentu',
            'documents' => $documents,
            'funsionariu' => $this->ApplicationModel->getFunsionariu(),
            'categories' => $this->db->tableExists('document_categories') ? $this->db->table('document_categories')->orderBy('name', 'ASC')->get()->getResultArray() : [],
        ]);

        return view('pages/administrador/documentu', $data);
    }

    public function createDocumentCategory()
    {
        $name = $this->postText('name');
        if ($name === '' || strlen($name) > 100) {
            return $this->backWithError('Naran kategoria dokumentu tenke prense no labele liu karakter 100.');
        }
        if ($this->db->table('document_categories')->where('name', $name)->countAllResults() > 0) {
            return $this->backWithError('Kategoria dokumentu iha ona.');
        }

        $this->db->table('document_categories')->insert([
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $this->logAudit('create_document_category', 'document_categories', $this->db->insertID(), null, ['name' => $name]);
        session()->setFlashdata('success', 'Kategoria dokumentu aumenta ona.');
        return redirect()->back();
    }

    public function deleteDocumentCategory($id)
    {
        $category = $this->db->table('document_categories')->where('id', (int) $id)->get()->getRowArray();
        if (!$category) {
            return $this->backWithError('Kategoria dokumentu la konese.');
        }
        $used = $this->db->table('employee_documents')->where('category', $category['name'])->where('deleted_at', null)->countAllResults();
        if ($used > 0) {
            return $this->backWithError('Kategoria ne\'e seidauk bele hamos tanba dokumentu uza hela.');
        }

        $this->ApplicationModel->deleteData('document_categories', ['id' => (int) $id]);
        $this->logAudit('delete_document_category', 'document_categories', $id, $category, null);
        session()->setFlashdata('success', 'Kategoria dokumentu hamos ona.');
        return redirect()->back();
    }

    public function uploadDocumentu()
    {
        if (!$this->validate([
            'funsionariu_id' => 'required|is_natural_no_zero',
            'category' => 'required|max_length[100]',
            'visibility' => 'required|in_list[admin_only,employee_visible]',
            'documentu' => 'uploaded[documentu]|max_size[documentu,5120]|ext_in[documentu,pdf,jpg,jpeg,png]|mime_in[documentu,application/pdf,image/jpg,image/jpeg,image/png]',
        ])) {
            return $this->backWithError(implode(' ', $this->validator->getErrors()));
        }

        $funsionariu = $this->ApplicationModel->getFunsionariu((int) $this->request->getPost('funsionariu_id'));
        if (!$funsionariu) {
            return $this->backWithError('Funsionariu la konese.');
        }

        $file = $this->request->getFile('documentu');
        $storedName = $file->getRandomName();
        $this->storage()->putUpload('documentu', $storedName, $file);

        $data = [
            'funsionariu_id' => (int) $funsionariu['id'],
            'category' => $this->postText('category'),
            'original_name' => $file->getClientName(),
            'stored_name' => $storedName,
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
            'visibility' => $this->request->getPost('visibility'),
            'uploaded_by' => session()->get('userID'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->ApplicationModel->saveData('employee_documents', $data);
        $documentId = $this->db->insertID();
        $this->logAudit('upload_employee_document', 'employee_documents', $documentId, null, $data);

        session()->setFlashdata('success', 'Dokumentu upload ho susesu.');
        return redirect()->back();
    }

    public function deleteDocumentu($id)
    {
        $document = $this->db->table('employee_documents')
            ->where('id', (int) $id)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$document) {
            return $this->backWithError('Dokumentu la konese.');
        }

        $this->ApplicationModel->updateData('employee_documents', [
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => (int) $id]);

        $this->logAudit('delete_employee_document', 'employee_documents', $id, $document, null);
        session()->setFlashdata('success', 'Dokumentu hamos ona.');
        return redirect()->back();
    }

    public function audit()
    {
        $pagination = $this->auditPagination();
        $total = (int) $this->db->table('audit_logs')->countAllResults();
        $pagination['total'] = $total;
        $pagination['pages'] = max(1, (int) ceil($total / $pagination['per_page']));
        $pagination['page'] = min($pagination['page'], $pagination['pages']);
        $pagination['offset'] = ($pagination['page'] - 1) * $pagination['per_page'];

        $logs = $this->db->table('audit_logs')
            ->select('audit_logs.id, audit_logs.created_at, audit_logs.actor_role, audit_logs.action, audit_logs.entity_type, audit_logs.entity_id, audit_logs.ip_address, users.username, users.fullname')
            ->join('users', 'audit_logs.actor_user_id = users.id', 'left')
            ->orderBy($pagination['sort'], $pagination['direction'])
            ->orderBy('audit_logs.id', $pagination['direction'])
            ->limit($pagination['per_page'], $pagination['offset'])
            ->get()
            ->getResultArray();

        return view('pages/administrador/audit', array_merge($this->data, [
            'title' => 'Rejistu Auditoria',
            'logs' => $logs,
            'pagination' => $pagination,
        ]));
    }

    /**
     * @return array{page: int, per_page: int, offset: int, sort: string, direction: string}
     */
    private function auditPagination(): array
    {
        $perPage = (int) $this->request->getGet('per_page');
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
        $sorts = ['created_at' => 'audit_logs.created_at'];
        $sortKey = (string) $this->request->getGet('sort');
        $direction = strtolower((string) $this->request->getGet('direction'));

        return [
            'page' => max(1, (int) $this->request->getGet('page')),
            'per_page' => $perPage,
            'offset' => 0,
            'sort' => $sorts[$sortKey] ?? $sorts['created_at'],
            'direction' => $direction === 'asc' ? 'ASC' : 'DESC',
        ];
    }

    public function maintenance()
    {
        $files = [];
        foreach ($this->storage()->list('backups') as $backup) {
            $fileName = basename($backup->key);
            if (!preg_match('/^backup_\d{8}_\d{6}\.sql$/', $fileName)) {
                continue;
            }
            $files[] = [
                'name' => $fileName,
                'size' => $backup->size,
                'modified_at' => date('Y-m-d H:i:s', $backup->modifiedAt ?? time()),
            ];
        }
        usort($files, static fn($a, $b) => strcmp($b['modified_at'], $a['modified_at']));

        return view('pages/administrador/maintenance', array_merge($this->data, [
            'title' => 'Manutensaun',
            'backups' => $files,
        ]));
    }

    public function createBackup()
    {
        $fileName = 'backup_' . date('Ymd_His') . '.sql';
        $backup = $this->generateSqlBackup();
        $this->storage()->putContents('backups', $fileName, $backup, 'application/sql');

        $this->logAudit('create_backup', 'backup', $fileName, null, ['size' => strlen($backup)]);
        session()->setFlashdata('success', 'Kopia seguransa kria ona: ' . $fileName);
        return redirect()->back();
    }

    public function downloadBackup($fileName)
    {
        $fileName = basename((string) $fileName);
        if (!preg_match('/^backup_\d{8}_\d{6}\.sql$/', $fileName)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $contents = $this->storage()->read('backups', $fileName);
        if ($contents === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response->download($fileName, $contents);
    }

    public function restoreBackup()
    {
        if (!$this->validate([
            'backup_file' => 'uploaded[backup_file]|max_size[backup_file,51200]|ext_in[backup_file,sql]|mime_in[backup_file,text/plain,application/octet-stream,application/sql]',
        ])) {
            return $this->backWithError(implode(' ', $this->validator->getErrors()));
        }

        $file = $this->request->getFile('backup_file');
        $sql = file_get_contents($file->getTempName());
        if ($sql === false || trim($sql) === '') {
            return $this->backWithError('Fail kopia seguransa mamuk ka la bele lee.');
        }

        $statements = $this->splitSqlStatements($sql);
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        foreach ($statements as $statement) {
            $trimmed = trim($statement);
            if ($trimmed !== '') {
                $this->db->query($trimmed);
            }
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');

        $this->logAudit('restore_backup', 'backup', $file->getClientName(), null, ['statements' => count($statements)]);
        session()->setFlashdata('success', 'Restaura kopia seguransa remata.');
        return redirect()->back();
    }

    private function backupDir(): string
    {
        $dir = WRITEPATH . 'backups';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return $dir;
    }

    private function generateSqlBackup(): string
    {
        $sql = "-- SIMAUCATAR backup " . date('Y-m-d H:i:s') . "\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($this->db->listTables() as $table) {
            $create = $this->db->query('SHOW CREATE TABLE ' . $this->quoteIdentifier($table))->getRowArray();
            $createSql = $create['Create Table'] ?? array_values($create)[1] ?? '';
            $sql .= "DROP TABLE IF EXISTS " . $this->quoteIdentifier($table) . ";\n";
            $sql .= $createSql . ";\n\n";

            $rows = $this->db->table($table)->get()->getResultArray();
            foreach ($rows as $row) {
                $columns = array_map(fn($col) => $this->quoteIdentifier($col), array_keys($row));
                $values = array_map(fn($value) => $value === null ? 'NULL' : $this->db->escape($value), array_values($row));
                $sql .= 'INSERT INTO ' . $this->quoteIdentifier($table)
                    . ' (' . implode(', ', $columns) . ') VALUES ('
                    . implode(', ', $values) . ");\n";
            }
            $sql .= "\n";
        }

        return $sql . "SET FOREIGN_KEY_CHECKS=1;\n";
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $inString = false;
        $quote = '';
        $length = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $next = $sql[$i + 1] ?? '';

            if (!$inString && $char === '-' && $next === '-') {
                while ($i < $length && $sql[$i] !== "\n") {
                    $i++;
                }
                continue;
            }

            if (($char === "'" || $char === '"') && ($i === 0 || $sql[$i - 1] !== '\\')) {
                if (!$inString) {
                    $inString = true;
                    $quote = $char;
                } elseif ($quote === $char) {
                    $inString = false;
                }
            }

            if ($char === ';' && !$inString) {
                $statements[] = $buffer;
                $buffer = '';
                continue;
            }

            $buffer .= $char;
        }

        if (trim($buffer) !== '') {
            $statements[] = $buffer;
        }

        return $statements;
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
        $used = $this->db->table('sansaun')->where('tipu_sansaun_id', $id)->countAllResults();
        if ($used > 0) {
            session()->setFlashdata('error', 'Tipu sansaun nee la bele hamos tanba uza ona iha dadus sansaun.');
            return redirect()->back();
        }

        $this->ApplicationModel->deleteData('tipu_sansaun', ['id' => $id]);
        session()->setFlashdata('success', 'Tipu sansaun hamos ona!');
        return redirect()->back();
    }

    public function createSansaun()
    {
        $funsionariu_id = $this->request->getVar('funsionariu_id');
        $tipu_id = $this->request->getVar('tipu_sansaun_id');
        $tipu = $this->ApplicationModel->getTipuSansaun($tipu_id);
        if (!$tipu) {
            session()->setFlashdata('error', 'Tipu sansaun la konese.');
            return redirect()->back();
        }

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
            'retira_reason'  => $this->request->getPost('retira_reason') ?: 'Retira husi administrasaun',
            'retira_by'      => session()->get('userID'),
            'retira_at'      => date('Y-m-d H:i:s'),
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
