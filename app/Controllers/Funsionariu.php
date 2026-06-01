<?php

namespace App\Controllers;

use App\Controllers\BaseController;

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

        $funsionariu_id = $funsionariu['id'];
        $prezente = $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('estadu_prezensa', 'Prezente')->countAllResults();
        $tardi = $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('estadu_prezensa', 'Tardi')->countAllResults();
        $falta = $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('estadu_prezensa', 'Falta')->countAllResults();
        $lisensa = $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('estadu_prezensa', 'Lisensa')->countAllResults();

        $trendLabels = [];
        $trendPrezente = [];
        $trendTardi = [];
        $trendFalta = [];
        $trendLisensa = [];
        for ($i = 14; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $trendLabels[] = date('d M', strtotime($date));
            $trendPrezente[] = (int) $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('data_prezensa', $date)->where('estadu_prezensa', 'Prezente')->countAllResults();
            $trendTardi[] = (int) $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('data_prezensa', $date)->where('estadu_prezensa', 'Tardi')->countAllResults();
            $trendFalta[] = (int) $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('data_prezensa', $date)->where('estadu_prezensa', 'Falta')->countAllResults();
            $trendLisensa[] = (int) $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('data_prezensa', $date)->where('estadu_prezensa', 'Lisensa')->countAllResults();
        }

        $data = array_merge($this->data, [
            'title' => 'Painel Funsionariu',
            'avizu' => $this->ApplicationModel->getAvizu(),
            'prezensa_fulan' => count($this->ApplicationModel->getPrezensa(funsionariu_id: $funsionariu_id)),
            'funsionariu' => $funsionariu,
            'chart_data' => json_encode([(int) $prezente, (int) $tardi, (int) $falta, (int) $lisensa]),
            'trend_labels' => json_encode($trendLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
            'trend_prezente' => json_encode($trendPrezente),
            'trend_tardi' => json_encode($trendTardi),
            'trend_falta' => json_encode($trendFalta),
            'trend_lisensa' => json_encode($trendLisensa),
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

    public function clockIn()
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

        $check = $this->ApplicationModel->getPrezensa(funsionariu_id: $funsionariu_id, data: $ohin);
        if ($check) {
            session()->setFlashdata('error', 'Ita absente ona ohin.');
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

        if (strtotime($now) < strtotime($settings['tama_hahu']) || strtotime($now) > strtotime($settings['tama_remata'])) {
            session()->setFlashdata('error', 'Tempu absensia tama taka ona.');
            return redirect()->back();
        }

        $toleransia = (int) ($settings['toleransia_minutu'] ?? 0);
        $lateThreshold = date('H:i:s', strtotime($settings['tama_hahu'] . ' +' . $toleransia . ' minutes'));
        $estadu = (strtotime($now) > strtotime($lateThreshold)) ? 'Tardi' : 'Prezente';

        $this->ApplicationModel->saveData('prezensa', [
            'funsionariu_id' => $funsionariu_id,
            'data_prezensa' => $ohin,
            'oras_tama' => $now,
            'estadu_prezensa' => $estadu,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->logAudit('clock_in', 'prezensa', $funsionariu_id . ':' . $ohin, null, [
            'funsionariu_id' => $funsionariu_id,
            'data_prezensa' => $ohin,
            'oras_tama' => $now,
            'estadu_prezensa' => $estadu,
        ]);
        session()->setFlashdata('success', 'Clock In ho susesu. Estadu: ' . $estadu);
        return redirect()->back();
    }

    public function clockOut()
    {
        $funsionariu = $this->currentFunsionariu();
        if (!$funsionariu) {
            return redirect()->to(base_url('blocked'));
        }

        $funsionariu_id = $funsionariu['id'];
        $ohin = date('Y-m-d');
        $now = date('H:i:s');
        $prezensa = $this->db->table('prezensa')
            ->where('funsionariu_id', $funsionariu_id)
            ->where('data_prezensa', $ohin)
            ->get()->getRowArray();

        if (!$prezensa || empty($prezensa['oras_tama'])) {
            session()->setFlashdata('error', 'Ita seidauk clock in.');
            return redirect()->back();
        }

        if (!empty($prezensa['oras_sai'])) {
            session()->setFlashdata('error', 'Ita clock out ona ohin.');
            return redirect()->back();
        }

        $settings = $this->ApplicationModel->getAttendanceSettings();
        $hahu_sai = $settings['sai_hahu'] ?? '00:00:00';
        $remata_sai = $settings['sai_remata'] ?? '23:59:59';

        if (strtotime($now) < strtotime($hahu_sai) || strtotime($now) > strtotime($remata_sai)) {
            session()->setFlashdata('error', 'Tempu clock out taka ona.');
            return redirect()->back();
        }

        $this->ApplicationModel->updateData('prezensa', [
            'oras_sai' => $now,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['funsionariu_id' => $funsionariu_id, 'data_prezensa' => $ohin]);

        $this->logAudit('clock_out', 'prezensa', $prezensa['id'] ?? ($funsionariu_id . ':' . $ohin), $prezensa, [
            'oras_sai' => $now,
        ]);
        session()->setFlashdata('success', 'Clock Out ho susesu.');
        return redirect()->back();
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
            'tipu_lisensa' => 'required|max_length[100]',
            'data_hahu' => 'required|valid_date[Y-m-d]',
            'data_remata' => 'required|valid_date[Y-m-d]',
            'razaun' => 'required|min_length[5]|max_length[1000]',
            'dokumentu_suporta' => 'if_exist|max_size[dokumentu_suporta,4096]|ext_in[dokumentu_suporta,pdf,jpg,jpeg,png]|mime_in[dokumentu_suporta,application/pdf,image/jpg,image/jpeg,image/png]',
        ])) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }

        $funsionariu_id = $funsionariu['id'];
        $data_hahu = $this->request->getPost('data_hahu');
        $data_remata = $this->request->getPost('data_remata');

        if ($data_remata < $data_hahu) {
            session()->setFlashdata('error', 'Data remata la bele kiik liu data hahu.');
            return redirect()->back()->withInput();
        }

        $tipu_lisensa = (string) $this->request->getPost('tipu_lisensa');
        for ($year = (int) date('Y', strtotime($data_hahu)); $year <= (int) date('Y', strtotime($data_remata)); $year++) {
            $this->ApplicationModel->recalculateLeaveBalance((int) $funsionariu['id'], $tipu_lisensa, $year);
            $balance = $this->db->table('leave_balances')
                ->where('funsionariu_id', $funsionariu['id'])
                ->where('leave_type', $tipu_lisensa)
                ->where('year', $year)
                ->get()
                ->getRowArray();
            $requestedDays = $this->ApplicationModel->countLeaveDays($data_hahu, $data_remata, $year);
            if ($balance && $requestedDays > (float) $balance['remaining_days']) {
                session()->setFlashdata('error', 'Balansu lisensa la sufisiente ba tinan ' . $year . '. Restu: ' . $balance['remaining_days'] . ' loron.');
                return redirect()->back()->withInput();
            }
        }

        $existingPrezensa = $this->db->table('prezensa')
            ->where('funsionariu_id', $funsionariu_id)
            ->where('data_prezensa >=', $data_hahu)
            ->where('data_prezensa <=', $data_remata)
            ->whereIn('estadu_prezensa', ['Prezente', 'Tardi'])
            ->get()->getResultArray();

        if (!empty($existingPrezensa)) {
            session()->setFlashdata('error', 'Ita la bele husu lisensa iha loron neebe ita prezente/tardi ona.');
            return redirect()->back()->withInput();
        }

        $overlapLisensa = $this->db->table('lisensa')
            ->where('funsionariu_id', $funsionariu_id)
            ->whereIn('estadu_lisensa', ['Pendente', 'Aprovadu'])
            ->where('data_hahu <=', $data_remata)
            ->where('data_remata >=', $data_hahu)
            ->get()->getRowArray();

        if ($overlapLisensa) {
            session()->setFlashdata('error', 'Ita iha ona pedidu lisensa Pendente/Aprovadu iha range data nee.');
            return redirect()->back()->withInput();
        }

        $data = [
            'funsionariu_id' => $funsionariu_id,
            'tipu_lisensa' => $tipu_lisensa,
            'data_hahu' => $data_hahu,
            'data_remata' => $data_remata,
            'razaun' => $this->request->getPost('razaun'),
            'estadu_lisensa' => 'Pendente',
            'created_at' => date('Y-m-d H:i:s'),
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
