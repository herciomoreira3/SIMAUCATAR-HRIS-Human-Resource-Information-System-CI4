<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RelatoriuModel;

class Relatoriu extends BaseController
{
    protected $RelatoriuModel;

    public function __construct()
    {
        $this->RelatoriuModel = new RelatoriuModel();
    }

    public function index()
    {
        $data = array_merge($this->data, [
            'title' => 'Módulu Relatóriu',
        ]);
        return view('pages/administrador/relatoriu/index', $data);
    }

    public function funsionariu()
    {
        $dept_id = $this->request->getGet('diresaun_id');
        $poz_id = $this->request->getGet('pozisaun_id');
        $kat_id = $this->request->getGet('kategoria_id');
        $grau_id = $this->request->getGet('grau_id');

        $data = array_merge($this->data, [
            'title' => 'Relatóriu Funsionáriu',
            'funsionariu' => $this->RelatoriuModel->getRekapFunsionariu($dept_id, $poz_id, $kat_id, $grau_id),
            'diresaun' => $this->ApplicationModel->getDiresaun(),
            'pozisaun' => $this->ApplicationModel->getPozisaun(),
            'kategoria' => $this->ApplicationModel->getKategoria(),
            'grau' => $this->ApplicationModel->getGrau(),
            'filter' => [
                'diresaun_id' => $dept_id,
                'pozisaun_id' => $poz_id,
                'kategoria_id' => $kat_id,
                'grau_id' => $grau_id
            ]
        ]);
        return view('pages/administrador/relatoriu/funsionariu', $data);
    }

    public function prezensa()
    {
        $data_hahu = $this->request->getGet('data_hahu') ?? date('Y-m-01');
        $data_remata = $this->request->getGet('data_remata') ?? date('Y-m-t');
        $dept_id = $this->request->getGet('diresaun_id');
        $estadu = $this->request->getGet('estadu');

        $data = array_merge($this->data, [
            'title' => 'Relatóriu Prezensa',
            'prezensa' => $this->RelatoriuModel->getRekapPrezensa($data_hahu, $data_remata, $dept_id, $estadu),
            'diresaun' => $this->ApplicationModel->getDiresaun(),
            'filter' => [
                'data_hahu' => $data_hahu,
                'data_remata' => $data_remata,
                'diresaun_id' => $dept_id,
                'estadu' => $estadu
            ]
        ]);
        return view('pages/administrador/relatoriu/prezensa', $data);
    }

    public function salariu()
    {
        $fulan = $this->request->getGet('fulan') ?? date('m');
        $tinan = $this->request->getGet('tinan') ?? date('Y');

        $data = array_merge($this->data, [
            'title' => 'Relatóriu Saláriu',
            'salariu' => $this->RelatoriuModel->getRekapSalariu($fulan, $tinan),
            'filter' => [
                'fulan' => $fulan,
                'tinan' => $tinan
            ]
        ]);
        return view('pages/administrador/relatoriu/salariu', $data);
    }

    public function lisensa()
    {
        $data_hahu = $this->request->getGet('data_hahu') ?? date('Y-m-01');
        $data_remata = $this->request->getGet('data_remata') ?? date('Y-m-t');
        $estadu = $this->request->getGet('estadu');
        $tipu_lisensa = $this->request->getGet('tipu_lisensa');

        $data = array_merge($this->data, [
            'title' => 'Relatóriu Lisensa',
            'lisensa' => $this->RelatoriuModel->getRekapLisensa($data_hahu, $data_remata, $estadu, $tipu_lisensa),
            'tipu_lisensa' => $this->ApplicationModel->getTipuLisensa(),
            'filter' => [
                'data_hahu' => $data_hahu,
                'data_remata' => $data_remata,
                'estadu' => $estadu,
                'tipu_lisensa' => $tipu_lisensa
            ]
        ]);
        return view('pages/administrador/relatoriu/lisensa', $data);
    }

    public function sansaun()
    {
        $fulan = $this->request->getGet('fulan') ?? date('m');
        $tinan = $this->request->getGet('tinan') ?? date('Y');
        $estadu = $this->request->getGet('estadu');
        $tipu_sansaun_id = $this->request->getGet('tipu_sansaun_id');

        $data = array_merge($this->data, [
            'title' => 'Relatóriu Sansaun',
            'sansaun' => $this->RelatoriuModel->getRekapSansaun($fulan, $tinan, $estadu, $tipu_sansaun_id),
            'tipu_sansaun' => $this->ApplicationModel->getTipuSansaun(),
            'filter' => [
                'fulan' => $fulan,
                'tinan' => $tinan,
                'estadu' => $estadu,
                'tipu_sansaun_id' => $tipu_sansaun_id
            ]
        ]);
        return view('pages/administrador/relatoriu/sansaun', $data);
    }

    // --- Export Methods ---

    public function exportFunsionariu()
    {
        $dept_id = $this->request->getPost('diresaun_id');
        $poz_id = $this->request->getPost('pozisaun_id');
        $kat_id = $this->request->getPost('kategoria_id');
        $grau_id = $this->request->getPost('grau_id');
        $type = $this->request->getPost('export_type');

        $data = [
            'title' => 'Relatóriu Funsionáriu',
            'funsionariu' => $this->RelatoriuModel->getRekapFunsionariu($dept_id, $poz_id, $kat_id, $grau_id),
            'data_print' => date('d-m-Y H:i')
        ];

        if ($type == 'pdf') {
            return $this->generatePDF('pages/administrador/relatoriu/export/funsionariu_pdf', $data, 'Relatoriu_Funsionariu.pdf');
        } else {
            return $this->generateCSV('Relatoriu_Funsionariu.csv', ['NID', 'Naran Kompletu', 'Diresaun', 'Pozisaun', 'Kategoria', 'Grau'], $data['funsionariu'], function($row) {
                return [$row['nid'], $row['naran_kompletu'], $row['naran_diresaun'], $row['naran_pozisaun'], $row['naran_kategoria'], $row['naran_grau']];
            });
        }
    }

    public function exportPrezensa()
    {
        $data_hahu = $this->request->getPost('data_hahu');
        $data_remata = $this->request->getPost('data_remata');
        $dept_id = $this->request->getPost('diresaun_id');
        $estadu = $this->request->getPost('estadu');
        $type = $this->request->getPost('export_type');

        $data = [
            'title' => 'Relatóriu Prezensa',
            'prezensa' => $this->RelatoriuModel->getRekapPrezensa($data_hahu, $data_remata, $dept_id, $estadu),
            'data_hahu' => $data_hahu,
            'data_remata' => $data_remata,
            'data_print' => date('d-m-Y H:i')
        ];

        if ($type == 'pdf') {
            return $this->generatePDF('pages/administrador/relatoriu/export/prezensa_pdf', $data, 'Relatoriu_Prezensa.pdf');
        } else {
            return $this->generateCSV('Relatoriu_Prezensa.csv', ['NID', 'Naran Kompletu', 'Diresaun', 'Prezente', 'Loron Sorin', 'Falta', 'Lisensa', 'Incomplete'], $data['prezensa'], function($row) {
                return [$row['nid'], $row['naran_kompletu'], $row['naran_diresaun'], $row['total_prezente'], $row['total_loron_sorin'], $row['total_falta'], $row['total_lisensa'], $row['total_incomplete']];
            });
        }
    }

    public function exportSalariu()
    {
        $fulan = $this->request->getPost('fulan');
        $tinan = $this->request->getPost('tinan');
        $type = $this->request->getPost('export_type');

        $data = [
            'title' => 'Relatóriu Saláriu',
            'salariu' => $this->RelatoriuModel->getRekapSalariu($fulan, $tinan),
            'fulan' => $fulan,
            'tinan' => $tinan,
            'data_print' => date('d-m-Y H:i')
        ];

        if ($type == 'pdf') {
            return $this->generatePDF('pages/administrador/relatoriu/export/salariu_pdf', $data, 'Relatoriu_Salariu.pdf');
        } else {
            return $this->generateCSV('Relatoriu_Salariu.csv', ['NID', 'Naran Kompletu', 'Saláriu Báziku', 'Subsídiu', 'Deskontu', 'Líquidu'], $data['salariu'], function($row) {
                return [$row['nid'], $row['naran_kompletu'], $row['salariu_baziku'], $row['total_subsidiu'], $row['total_deskontu'], $row['salariu_liquidu']];
            });
        }
    }

    public function exportLisensa()
    {
        $data_hahu = $this->request->getPost('data_hahu');
        $data_remata = $this->request->getPost('data_remata');
        $estadu = $this->request->getPost('estadu');
        $tipu_lisensa = $this->request->getPost('tipu_lisensa');
        $type = $this->request->getPost('export_type');

        $data = [
            'title' => 'Relatóriu Lisensa',
            'lisensa' => $this->RelatoriuModel->getRekapLisensa($data_hahu, $data_remata, $estadu, $tipu_lisensa),
            'data_hahu' => $data_hahu,
            'data_remata' => $data_remata,
            'data_print' => date('d-m-Y H:i')
        ];

        if ($type == 'pdf') {
            return $this->generatePDF('pages/administrador/relatoriu/export/lisensa_pdf', $data, 'Relatoriu_Lisensa.pdf');
        } else {
            return $this->generateCSV('Relatoriu_Lisensa.csv', ['NID', 'Naran Kompletu', 'Tipu', 'Data Hahu', 'Data Remata', 'Estadu'], $data['lisensa'], function($row) {
                return [$row['nid'], $row['naran_kompletu'], $row['tipu_lisensa'], $row['data_hahu'], $row['data_remata'], $row['estadu_lisensa']];
            });
        }
    }

    public function exportSansaun()
    {
        $fulan = $this->request->getPost('fulan');
        $tinan = $this->request->getPost('tinan');
        $estadu = $this->request->getPost('estadu');
        $tipu_sansaun_id = $this->request->getPost('tipu_sansaun_id');
        $type = $this->request->getPost('export_type');

        $data = [
            'title' => 'Relatóriu Sansaun',
            'sansaun' => $this->RelatoriuModel->getRekapSansaun($fulan, $tinan, $estadu, $tipu_sansaun_id),
            'fulan' => $fulan,
            'tinan' => $tinan,
            'data_print' => date('d-m-Y H:i')
        ];

        if ($type == 'pdf') {
            return $this->generatePDF('pages/administrador/relatoriu/export/sansaun_pdf', $data, 'Relatoriu_Sansaun.pdf');
        } else {
            return $this->generateCSV('Relatoriu_Sansaun.csv', ['NID', 'Naran Kompletu', 'Tipu Sansaun', 'Data', 'Valor', 'Estadu'], $data['sansaun'], function($row) {
                return [$row['nid'], $row['naran_kompletu'], $row['naran_tipu'], $row['data_sansaun'], $row['valor_total'], $row['estadu_sansaun']];
            });
        }
    }

    private function generatePDF($view, $data, $filename)
    {
        $dompdf = new \Dompdf\Dompdf();
        $html = view($view, $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($filename, ["Attachment" => false]);
        exit;
    }

    private function generateCSV($filename, $header, $data, $callback)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        $output = fopen('php://output', 'w');
        fputcsv($output, $header);
        foreach ($data as $row) {
            fputcsv($output, $callback($row));
        }
        fclose($output);
        exit;
    }
}
