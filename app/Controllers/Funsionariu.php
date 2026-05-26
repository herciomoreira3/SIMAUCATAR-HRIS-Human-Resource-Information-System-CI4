<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Funsionariu extends BaseController
{
    public function dashboard()
    {
        $funsionariu_id = session()->get('funsionariu_id');
        $funsionariu = $this->ApplicationModel->getFunsionariuByUserId(session()->get('userID'));
        
        // Personal Performance Chart Data
        $prezente = $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('estadu_prezensa', 'Prezente')->countAllResults();
        $falta = $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('estadu_prezensa', 'Falta')->countAllResults();
        $lisensa = $this->db->table('prezensa')->where('funsionariu_id', $funsionariu_id)->where('estadu_prezensa', 'Lisensa')->countAllResults();

        $data = array_merge($this->data, [
            'title' => 'Dashboard Funsionáriu',
            'avizu' => $this->ApplicationModel->getAvizu(),
            'prezensa_fulan' => count($this->ApplicationModel->getPrezensa(funsionariu_id: $funsionariu_id)), 
            'funsionariu' => $funsionariu,
            'chart_data' => json_encode([$prezente, $falta, $lisensa])
        ]);

        return view('pages/funsionariu/dashboard', $data);
    }


    public function prezensa()
    {
        $funsionariu_id = session()->get('funsionariu_id');
        $ohin = date('Y-m-d');
        
        // Get today's record specifically for the toggle logic
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
        $funsionariu_id = session()->get('funsionariu_id');
        if (!$funsionariu_id) {
            session()->setFlashdata('error', 'Ita-nia akun seidauk ligadu ba dadus funsionáriu. Favor kontaktu admin!');
            return redirect()->back();
        }

        $ohin = date('Y-m-d');
        $now = date('H:i:s');
        
        $settings = $this->ApplicationModel->getAttendanceSettings();
        if (!$settings) {
            session()->setFlashdata('error', 'Konfigurasaun prezensa seidauk iha!');
            return redirect()->back();
        }

        // Check if already clocked in today
        $check = $this->ApplicationModel->getPrezensa(funsionariu_id: $funsionariu_id, data: $ohin);
        if ($check) {
            session()->setFlashdata('error', 'Ita absénte ona ohin!');
            return redirect()->back();
        }

        // Check if today is a weekend and if it's allowed
        $dayOfWeek = date('N'); // 1 (Mon) to 7 (Sun)
        if ($dayOfWeek == 6 && $settings['sabadu'] == 0) {
            session()->setFlashdata('error', 'Ohin loron Sábadu, sistema absénsia taka!');
            return redirect()->back();
        }
        if ($dayOfWeek == 7 && $settings['domingu'] == 0) {
            session()->setFlashdata('error', 'Ohin loron Domingu, sistema absénsia taka!');
            return redirect()->back();
        }


        // Check if outside time range
        if ($now < $settings['tama_hahu'] || $now > $settings['tama_remata']) {
             session()->setFlashdata('error', 'Tempu absénsia tama taka ona! (Loke husi ' . $settings['tama_hahu'] . ' to\'o ' . $settings['tama_remata'] . ')');
             return redirect()->back();
        }

        // Status is 'Prezente' if within the window
        $estadu = 'Prezente';

        $data = [
            'funsionariu_id' => $funsionariu_id,
            'data_prezensa'  => $ohin,
            'oras_tama'      => $now,
            'estadu_prezensa'=> $estadu,
            'created_at'     => date('Y-m-d H:i:s'),
        ];
        $this->ApplicationModel->saveData('prezensa', $data);
        session()->setFlashdata('success', 'Clock In ho susesu! Ita-nia estadu: ' . $estadu);
        return redirect()->back();
    }

    public function clockOut()
    {
        $funsionariu_id = session()->get('funsionariu_id');
        $ohin = date('Y-m-d');
        $now = date('H:i:s');

        $prezensa = $this->db->table('prezensa')
            ->where('funsionariu_id', $funsionariu_id)
            ->where('data_prezensa', $ohin)
            ->get()->getRowArray();

        if (!$prezensa || empty($prezensa['oras_tama'])) {
             session()->setFlashdata('error', 'Ita seidauk absénte tama (Clock In)!');
             return redirect()->back();
        }

        if (!empty($prezensa['oras_sai'])) {
            session()->setFlashdata('error', 'Ita absénte sai ona ohin!');
            return redirect()->back();
        }

        $settings = $this->ApplicationModel->getAttendanceSettings();
        $hahu_sai = $settings['sai_hahu'] ?? '00:00:00';
        $remata_sai = $settings['sai_remata'] ?? '23:59:59';

        if (strtotime($now) < strtotime($hahu_sai) || strtotime($now) > strtotime($remata_sai)) {
             session()->setFlashdata('error', 'Tempu absénsia sai taka ona! (Loke husi ' . date('H:i', strtotime($hahu_sai)) . ' to\'o ' . date('H:i', strtotime($remata_sai)) . ')');
             return redirect()->back();
        }

        $this->ApplicationModel->updateData('prezensa', [
            'oras_sai' => $now,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['funsionariu_id' => $funsionariu_id, 'data_prezensa' => $ohin]);
        
        session()->setFlashdata('success', 'Clock Out ho susesu!');
        return redirect()->back();
    }

    public function perfil()
    {
        $funsionariu = $this->ApplicationModel->getFunsionariuByUserId(session()->get('userID'));
        $data = array_merge($this->data, [
            'title' => 'Ha\'u-nia Perfil',
            'funsionariu' => $funsionariu,
        ]);
        return view('pages/funsionariu/perfil', $data);
    }

    public function updatePerfil()
    {
        // Update logic for profile
        return redirect()->back();
    }

    public function lisensa()
    {
        $funsionariu_id = session()->get('funsionariu_id');
        $data = array_merge($this->data, [
            'title' => 'Pedidu Lisensa',
            'lisensa' => $this->ApplicationModel->getLisensa(funsionariu_id: $funsionariu_id),
        ]);
        return view('pages/funsionariu/lisensa', $data);
    }

    public function saveLisensa()
    {
        $funsionariu_id = session()->get('funsionariu_id');
        $data_hahu = $this->request->getVar('data_hahu');
        $data_remata = $this->request->getVar('data_remata');

        // Check if there is any attendance record in the date range that is NOT 'Falta'
        // Employees who are already 'Prezente' or 'Tardi' cannot apply for leave.
        // If they are 'Falta' or have no record, they can apply for leave.
        $existingPrezensa = $this->db->table('prezensa')
            ->where('funsionariu_id', $funsionariu_id)
            ->where('data_prezensa >=', $data_hahu)
            ->where('data_prezensa <=', $data_remata)
            ->whereIn('estadu_prezensa', ['Prezente'])
            ->get()->getResultArray();


        if ($data_remata < $data_hahu) {
            session()->setFlashdata('error', "Data remata la bele ki'ik liu fali data hahu!");
            return redirect()->back()->withInput();
        }

        if (!empty($existingPrezensa)) {
            $msg = "Ita la bele husu lisensa iha loron ne'ebé ita prezente ona! Iha data ne'ebé ita hili, ita iha ona rejistu prezensa (Prezente/Tardi).";
            session()->setFlashdata('error', $msg);
            return redirect()->back()->withInput();
        }

        $data = [
            'funsionariu_id' => $funsionariu_id,
            'tipu_lisensa'   => $this->request->getVar('tipu_lisensa'),
            'data_hahu'      => $data_hahu,
            'data_remata'    => $data_remata,
            'razaun'         => $this->request->getVar('razaun'),
            'estadu_lisensa' => 'Pendente',
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        // Handle File Upload
        $file = $this->request->getFile('dokumentu_suporta');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/lisensa', $newName);
            $data['dokumentu_suporta'] = $newName;
        }

        $this->ApplicationModel->saveData('lisensa', $data);
        session()->setFlashdata('success', 'Pedidu lisensa haruka ona!');
        return redirect()->back();
    }

    public function salariu()
    {
        $funsionariu_id = session()->get('funsionariu_id');
        $data = array_merge($this->data, [
            'title' => 'Resibu Saláriu',
            'salariu' => $this->ApplicationModel->getSalariu(funsionariu_id: $funsionariu_id),
        ]);
        return view('pages/funsionariu/salariu', $data);
    }
}
