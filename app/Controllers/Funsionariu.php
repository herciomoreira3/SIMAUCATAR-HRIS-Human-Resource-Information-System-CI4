<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Repositories\DashboardRepository;
use DateTimeImmutable;
use DateTimeZone;

class Funsionariu extends BaseController
{
    private function currentFunsionariu(): ?array
    {
        $funsionariu = $this->ApplicationModel->getFunsionariuByUserId(session()->get('userID'));
        if (!$funsionariu) {
            session()->setFlashdata('error', 'Akun nee seidauk ligadu ba dadus funsionariu. Favor kontaktu admin.');
            return null;
        }

        return $funsionariu;
    }

    public function dashboard()
    {
        $funsionariu = $this->currentFunsionariu();
        if (!$funsionariu) {
            return redirect()->to(base_url('blocked'));
        }

        $funsionariu_id = (int) $funsionariu['id'];
        $repository = new DashboardRepository($this->db);
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Dili'));
        $trend = $repository->getEmployeeAttendanceTrend($funsionariu_id, $now->setTime(0, 0)->modify('-14 days'), $now->setTime(0, 0));
        $totals = $repository->getEmployeeAttendanceTotals($funsionariu_id);

        $data = array_merge($this->data, [
            'title' => 'Painel Funsionariu',
            'avizu' => $repository->getLatestAnnouncements(3, $now),
            'funsionariu' => $funsionariu,
            'chart_data' => json_encode(array_values($totals)),
            'trend_labels' => json_encode($trend['labels'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
            'trend_prezente' => json_encode($trend['series']['Prezente']),
            'trend_loron_sorin' => json_encode($trend['series']['Loron Sorin']),
            'trend_falta' => json_encode($trend['series']['Falta']),
            'trend_lisensa' => json_encode($trend['series']['Lisensa']),
        ]);

        return view('pages/funsionariu/dashboard', $data);
    }

    public function prezensa()
    {
        $funsionariu = $this->currentFunsionariu();
        if (!$funsionariu) {
            return redirect()->to(base_url('blocked'));
        }

        $funsionariu_id = $funsionariu['id'];
        $ohin = date('Y-m-d');
        $prezensa_ohin = $this->db->table('prezensa')
            ->where('funsionariu_id', $funsionariu_id)
            ->where('data_prezensa', $ohin)
            ->get()->getRowArray();

        $data = array_merge($this->data, [
            'title' => 'Prezensa Loron-loron',
            'prezensa_ohin' => $prezensa_ohin,
            'istoria_prezensa' => $this->ApplicationModel->getPrezensa(funsionariu_id: $funsionariu_id),
            'settings' => $this->ApplicationModel->getAttendanceSettings(),
        ]);

        return view('pages/funsionariu/prezensa', $data);
    }

    // Helper function to calculate attendance status
    private function calculateAttendanceStatus(array $prezensa): string
    {
        // Check if all 4 are present
        $hasTamaDader = !empty($prezensa['oras_tama_dader']);
        $hasSaiDader = !empty($prezensa['oras_sai_dader']);
        $hasTamaLokraik = !empty($prezensa['oras_tama_lokraik']);
        $hasSaiLokraik = !empty($prezensa['oras_sai_lokraik']);
        
        if ($hasTamaDader && $hasSaiDader && $hasTamaLokraik && $hasSaiLokraik) {
            return 'Prezente';
        } elseif ($hasTamaDader || $hasSaiDader || $hasTamaLokraik || $hasSaiLokraik) {
            // At least one but not all
            return 'Loron Sorin';
        }
        
        // No attendance at all
        return 'Incomplete';
    }

    // Generic attendance action handler
    private function handleAttendance(string $type, string $timeField): \CodeIgniter\HTTP\RedirectResponse
    {
        $funsionariu = $this->currentFunsionariu();
        if (!$funsionariu) {
            return redirect()->to(base_url('blocked'));
        }

        $funsionariu_id = $funsionariu['id'];
        $ohin = date('Y-m-d');
        $now = date('H:i:s');
        $settings = $this->ApplicationModel->getAttendanceSettings();

        if (!$settings) {
            session()->setFlashdata('error', 'Konfigurasaun prezensa seidauk iha.');
            return redirect()->back();
        }

        $check = $this->db->table('prezensa')
            ->where('funsionariu_id', $funsionariu_id)
            ->where('data_prezensa', $ohin)
            ->get()->getRowArray();
        
        if ($check && !empty($check[$timeField])) {
            $label = $type === 'tama_dader' ? 'Clock In Dader' :
                     ($type === 'sai_dader' ? 'Clock Out Dader' :
                     ($type === 'tama_lokraik' ? 'Clock In Lokraik' : 'Clock Out Lokraik'));
            session()->setFlashdata('error', "Ita $label ona ohin.");
            return redirect()->back();
        }

        $dayOfWeek = date('N');
        if ($dayOfWeek == 6 && (int) $settings['sabadu'] === 0) {
            session()->setFlashdata('error', 'Ohin Sabadu, sistema absensia taka.');
            return redirect()->back();
        }

        if ($dayOfWeek == 7 && (int) $settings['domingu'] === 0) {
            session()->setFlashdata('error', 'Ohin Domingu, sistema absensia taka.');
            return redirect()->back();
        }

        if ($this->db->tableExists('holidays')) {
            $holiday = $this->db->table('holidays')->where('holiday_date', $ohin)->get()->getRowArray();
            if ($holiday) {
                session()->setFlashdata('error', 'Ohin feriadu: ' . $holiday['title']);
                return redirect()->back();
            }
        }

        // Check manual mode and time window
        // DB stores keys as: tama_manual_dader, sai_manual_dader, tama_manual_lokraik, sai_manual_lokraik
        // $type is: tama_dader, sai_dader, tama_lokraik, sai_lokraik
        $parts = explode('_', $type, 2); // ['tama','dader'] or ['sai','lokraik']
        $manualConfigKey = $parts[0] . '_manual_' . $parts[1]; // tama_manual_dader
        $hahuConfigKey   = $parts[0] . '_hahu_' . $parts[1];   // tama_hahu_dader
        $remataConfigKey = $parts[0] . '_remata_' . $parts[1]; // tama_remata_dader

        if (!isset($settings[$manualConfigKey]) || (int)$settings[$manualConfigKey] !== 1) {
            $hahu = $settings[$hahuConfigKey] ?? '00:00:00';
            $remata = $settings[$remataConfigKey] ?? '23:59:59';
            if (strtotime($now) < strtotime($hahu) || strtotime($now) > strtotime($remata)) {
                $label = $type === 'tama_dader' ? 'Tama Dader' :
                        ($type === 'sai_dader' ? 'Sai Dader' :
                        ($type === 'tama_lokraik' ? 'Tama Lokraik' : 'Sai Lokraik'));
                session()->setFlashdata('error', "Tempu absensia $label taka ona.");
                return redirect()->back();
            }
        }

        // Prepare data
        $updateData = [
            $timeField => $now,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($check) {
            // Update existing record
            $this->ApplicationModel->updateData('prezensa', $updateData, ['id' => $check['id']]);
            
            // Calculate new status and update
            $check[$timeField] = $now;
            $newStatus = $this->calculateAttendanceStatus($check);
            $this->ApplicationModel->updateData('prezensa', ['estadu_prezensa' => $newStatus], ['id' => $check['id']]);
            
            $this->logAudit("attendance_$type", 'prezensa', $check['id'], $check, $updateData);
        } else {
            // Create new record
            $insertData = [
                'funsionariu_id' => $funsionariu_id,
                'data_prezensa' => $ohin,
                $timeField => $now,
                'estadu_prezensa' => 'Loron Sorin',
                'created_at' => date('Y-m-d H:i:s'),
            ];
            
            $this->ApplicationModel->saveData('prezensa', $insertData);
            $this->logAudit("attendance_$type", 'prezensa', $funsionariu_id . ':' . $ohin, null, $insertData);
        }

        $label = $type === 'tama_dader' ? 'Clock In Dader' :
                 ($type === 'sai_dader' ? 'Clock Out Dader' :
                 ($type === 'tama_lokraik' ? 'Clock In Lokraik' : 'Clock Out Lokraik'));
        session()->setFlashdata('success', "$label ho susesu.");
        return redirect()->back();
    }

    public function tamaDader()
    {
        return $this->handleAttendance('tama_dader', 'oras_tama_dader');
    }

    public function saiDader()
    {
        return $this->handleAttendance('sai_dader', 'oras_sai_dader');
    }

    public function tamaLokraik()
    {
        return $this->handleAttendance('tama_lokraik', 'oras_tama_lokraik');
    }

    public function saiLokraik()
    {
        return $this->handleAttendance('sai_lokraik', 'oras_sai_lokraik');
    }

    public function perfil()
    {
        $funsionariu = $this->currentFunsionariu();
        if (!$funsionariu) {
            return redirect()->to(base_url('blocked'));
        }

        $data = array_merge($this->data, [
            'title' => 'Hau-nia Perfil',
            'funsionariu' => $funsionariu,
        ]);

        return view('pages/funsionariu/perfil', $data);
    }

    public function updateFoto()
    {
        $funsionariu = $this->currentFunsionariu();
        if (!$funsionariu) {
            return redirect()->to(base_url('blocked'));
        }

        if (!$this->validate([
            'foto_perfil' => 'uploaded[foto_perfil]|is_image[foto_perfil]|mime_in[foto_perfil,image/jpg,image/jpeg,image/png]|max_size[foto_perfil,2048]',
        ])) {
            session()->setFlashdata('error', 'Foto perfil tenke JPG/PNG no maksimal 2MB.');
            return redirect()->back();
        }

        $file = $this->request->getFile('foto_perfil');
        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/perfil', $newName);

        if (!empty($funsionariu['foto_perfil'])) {
            $oldPath = FCPATH . 'uploads/perfil/' . $funsionariu['foto_perfil'];
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $this->ApplicationModel->updateData('funsionariu', [
            'foto_perfil' => $newName,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $funsionariu['id']]);

        $this->logAudit('update_profile_photo', 'funsionariu', $funsionariu['id'], [
            'foto_perfil' => $funsionariu['foto_perfil'] ?? null,
        ], [
            'foto_perfil' => $newName,
        ]);
        session()->setFlashdata('success', 'Foto perfil atualiza ona.');
        return redirect()->back();
    }

    public function updatePassword()
    {
        $funsionariu = $this->currentFunsionariu();
        if (!$funsionariu) {
            return redirect()->to(base_url('blocked'));
        }

        if (!$this->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min_length[8]',
            'password_konfirma' => 'required|matches[password_baru]',
        ])) {
            session()->setFlashdata('error', 'Senha foun minimal 8 karakter no konfirmasaun tenke hanesan.');
            return redirect()->back();
        }

        $user = $this->ApplicationModel->getUser(userID: session()->get('userID'));
        if (!$user || !password_verify((string) $this->request->getPost('password_lama'), $user['password'])) {
            session()->setFlashdata('error', 'Senha tuan la loos.');
            return redirect()->back();
        }

        $this->ApplicationModel->updateData('users', [
            'password' => password_hash((string) $this->request->getPost('password_baru'), PASSWORD_DEFAULT),
            'password_changed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => session()->get('userID')]);

        $this->logAudit('change_password', 'user', session()->get('userID'));
        session()->setFlashdata('success', 'Senha atualiza ona.');
        return redirect()->back();
    }

    public function lisensa()
    {
        $funsionariu = $this->currentFunsionariu();
        if (!$funsionariu) {
            return redirect()->to(base_url('blocked'));
        }

        $data = array_merge($this->data, [
            'title' => 'Pedidu Lisensa',
            'lisensa' => $this->ApplicationModel->getLisensa(funsionariu_id: $funsionariu['id']),
            'leave_balances' => $this->ApplicationModel->getLeaveBalances(funsionariu_id: $funsionariu['id'], year: date('Y')),
            'tipu_lisensa' => $this->ApplicationModel->getTipuLisensa(),
        ]);

        return view('pages/funsionariu/lisensa', $data);
    }

    public function saveLisensa()
    {
        $funsionariu = $this->currentFunsionariu();
        if (!$funsionariu) {
            return redirect()->to(base_url('blocked'));
        }

        if (!$this->validate([
            'tipu_lisensa'    => 'required|max_length[100]',
            'sesaun'          => 'required|in_list[Loron Tomak,Dader,Lokraik]',
            'data_hahu'       => 'required|valid_date[Y-m-d]',
            'data_remata'     => 'required|valid_date[Y-m-d]',
            'razaun'          => 'required|min_length[5]|max_length[1000]',
            'dokumentu_suporta' => 'if_exist|max_size[dokumentu_suporta,4096]|ext_in[dokumentu_suporta,pdf,jpg,jpeg,png]|mime_in[dokumentu_suporta,application/pdf,image/jpg,image/jpeg,image/png]',
        ])) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }

        $funsionariu_id = $funsionariu['id'];
        $sesaun         = $this->request->getPost('sesaun');
        $data_hahu      = $this->request->getPost('data_hahu');
        $data_remata    = $this->request->getPost('data_remata');

        // Half-day: only one day allowed
        if (in_array($sesaun, ['Dader', 'Lokraik'], true) && $data_hahu !== $data_remata) {
            session()->setFlashdata('error', 'Lisensa Dader/Lokraik deit bele ba loron ida de\'it.');
            return redirect()->back()->withInput();
        }

        if ($data_remata < $data_hahu) {
            session()->setFlashdata('error', 'Data remata la bele kiik liu data hahu.');
            return redirect()->back()->withInput();
        }

        $tipu_lisensa = (string) $this->request->getPost('tipu_lisensa');

        // Leave balance check
        for ($year = (int) date('Y', strtotime($data_hahu)); $year <= (int) date('Y', strtotime($data_remata)); $year++) {
            $this->ApplicationModel->recalculateLeaveBalance((int) $funsionariu['id'], $tipu_lisensa, $year);
            $balance = $this->db->table('leave_balances')
                ->where('funsionariu_id', $funsionariu['id'])
                ->where('leave_type', $tipu_lisensa)
                ->where('year', $year)
                ->get()->getRowArray();
            $requestedDays = $this->ApplicationModel->countLeaveDays($data_hahu, $data_remata, $year, $sesaun);
            if ($balance && $requestedDays > (float) $balance['remaining_days']) {
                session()->setFlashdata('error', 'Balansu lisensa la sufisiente ba tinan ' . $year . '. Restu: ' . $balance['remaining_days'] . ' loron.');
                return redirect()->back()->withInput();
            }
        }

        // Conflict check with existing attendance — session-aware
        if ($sesaun === 'Loron Tomak') {
            // Whole-day: block if any session already clocked in
            $existingPrezensa = $this->db->table('prezensa')
                ->where('funsionariu_id', $funsionariu_id)
                ->where('data_prezensa >=', $data_hahu)
                ->where('data_prezensa <=', $data_remata)
                ->whereIn('estadu_prezensa', ['Prezente', 'Loron Sorin'])
                ->get()->getResultArray();

            if (!empty($existingPrezensa)) {
                session()->setFlashdata('error', 'Ita la bele husu lisensa loron tomak iha loron neebe ita prezente/loron sorin ona.');
                return redirect()->back()->withInput();
            }
        } elseif ($sesaun === 'Dader') {
            // Dader: block if tama_dader already filled
            $existing = $this->db->table('prezensa')
                ->where('funsionariu_id', $funsionariu_id)
                ->where('data_prezensa', $data_hahu)
                ->get()->getRowArray();
            if ($existing && !empty($existing['oras_tama_dader'])) {
                session()->setFlashdata('error', 'Ita tama dader ona ohin, la bele husu lisensa sesion Dader.');
                return redirect()->back()->withInput();
            }
        } elseif ($sesaun === 'Lokraik') {
            // Lokraik: block if tama_lokraik already filled
            $existing = $this->db->table('prezensa')
                ->where('funsionariu_id', $funsionariu_id)
                ->where('data_prezensa', $data_hahu)
                ->get()->getRowArray();
            if ($existing && !empty($existing['oras_tama_lokraik'])) {
                session()->setFlashdata('error', 'Ita tama lokraik ona ohin, la bele husu lisensa sesion Lokraik.');
                return redirect()->back()->withInput();
            }
        }

        // Check overlap with existing approved/pending leave of same type+sesaun
        $overlapLisensa = $this->db->table('lisensa')
            ->where('funsionariu_id', $funsionariu_id)
            ->whereIn('estadu_lisensa', ['Pendente', 'Aprovadu'])
            ->where('data_hahu <=', $data_remata)
            ->where('data_remata >=', $data_hahu)
            ->where('sesaun', $sesaun)
            ->get()->getRowArray();

        if ($overlapLisensa) {
            session()->setFlashdata('error', 'Ita iha ona pedidu lisensa Pendente/Aprovadu iha range data no sesion nee.');
            return redirect()->back()->withInput();
        }

        $data = [
            'funsionariu_id' => $funsionariu_id,
            'tipu_lisensa'   => $tipu_lisensa,
            'sesaun'         => $sesaun,
            'data_hahu'      => $data_hahu,
            'data_remata'    => $data_remata,
            'razaun'         => $this->request->getPost('razaun'),
            'estadu_lisensa' => 'Pendente',
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        $file = $this->request->getFile('dokumentu_suporta');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/lisensa', $newName);
            $data['dokumentu_suporta'] = $newName;
        }

        $this->ApplicationModel->saveData('lisensa', $data);
        for ($year = (int) date('Y', strtotime($data_hahu)); $year <= (int) date('Y', strtotime($data_remata)); $year++) {
            $this->ApplicationModel->recalculateLeaveBalance((int) $funsionariu['id'], $tipu_lisensa, $year);
        }
        $this->logAudit('create_leave_request', 'lisensa', $this->db->insertID(), null, $data);
        session()->setFlashdata('success', 'Pedidu lisensa haruka ona.');
        return redirect()->back();
    }

    public function salariu()
    {
        $funsionariu = $this->currentFunsionariu();
        if (!$funsionariu) {
            return redirect()->to(base_url('blocked'));
        }

        $salariu = $this->ApplicationModel->getSalariu(funsionariu_id: $funsionariu['id']);
        $salariu_detallu = $this->ApplicationModel->getSalariuDetalluBySalariuIds(array_column($salariu, 'id'));

        $data = array_merge($this->data, [
            'title' => 'Resibu Salariu',
            'salariu' => $salariu,
            'salariu_detallu' => $salariu_detallu,
        ]);

        return view('pages/funsionariu/salariu', $data);
    }

    public function dokumentu()
    {
        $funsionariu = $this->currentFunsionariu();
        if (!$funsionariu) {
            return redirect()->to(base_url('blocked'));
        }

        $documents = $this->db->table('employee_documents')
            ->where('funsionariu_id', $funsionariu['id'])
            ->where('visibility', 'employee_visible')
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = array_merge($this->data, [
            'title' => 'Dokumentu',
            'documents' => $documents,
        ]);

        return view('pages/funsionariu/dokumentu', $data);
    }
}
