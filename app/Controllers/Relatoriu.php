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
        $filter = $this->employeeFilter('get');
        $page = $this->pagination(['nid' => 'funsionariu.nid', 'naran' => 'funsionariu.naran_kompletu', 'data_hahu' => 'funsionariu.data_hahu_servisu'], 'naran', 'asc');
        $total = $this->RelatoriuModel->countRekapFunsionariu($filter['diresaun_id'], $filter['pozisaun_id'], $filter['kategoria_id'], $filter['grau_id']);
        $page = $this->pager($page, $total);

        $data = array_merge($this->data, [
            'title' => 'Relatóriu Funsionáriu',
            'funsionariu' => $this->RelatoriuModel->getRekapFunsionariu($filter['diresaun_id'], $filter['pozisaun_id'], $filter['kategoria_id'], $filter['grau_id'], $page['per_page'], $page['offset'], $page['sort'], $page['direction']),
            'diresaun' => $this->ApplicationModel->getDiresaun(),
            'pozisaun' => $this->ApplicationModel->getPozisaun(),
            'kategoria' => $this->ApplicationModel->getKategoria(),
            'grau' => $this->ApplicationModel->getGrau(),
            'filter' => $filter,
            'pagination' => $page,
        ]);
        return view('pages/administrador/relatoriu/funsionariu', $data);
    }

    public function prezensa()
    {
        $filter = $this->attendanceFilter('get');
        $page = $this->pagination(['nid' => 'funsionariu.nid', 'naran' => 'funsionariu.naran_kompletu'], 'naran', 'asc');
        $total = $this->RelatoriuModel->countRekapPrezensa($filter['data_hahu'], $filter['data_remata'], $filter['diresaun_id'], $filter['estadu']);
        $page = $this->pager($page, $total);

        $data = array_merge($this->data, [
            'title' => 'Relatóriu Prezensa',
            'prezensa' => $this->RelatoriuModel->getRekapPrezensa($filter['data_hahu'], $filter['data_remata'], $filter['diresaun_id'], $filter['estadu'], $page['per_page'], $page['offset'], $page['sort'], $page['direction']),
            'diresaun' => $this->ApplicationModel->getDiresaun(),
            'filter' => $filter,
            'pagination' => $page,
        ]);
        return view('pages/administrador/relatoriu/prezensa', $data);
    }

    public function salariu()
    {
        $filter = $this->periodFilter('get');
        $page = $this->pagination(['nid' => 'funsionariu.nid', 'naran' => 'funsionariu.naran_kompletu', 'data' => 'salariu.data_pagamentu'], 'naran', 'asc');
        $total = $this->RelatoriuModel->countRekapSalariu($filter['fulan'], $filter['tinan']);
        $page = $this->pager($page, $total);

        $data = array_merge($this->data, [
            'title' => 'Relatóriu Saláriu',
            'salariu' => $this->RelatoriuModel->getRekapSalariu($filter['fulan'], $filter['tinan'], $page['per_page'], $page['offset'], $page['sort'], $page['direction']),
            'filter' => $filter,
            'pagination' => $page,
        ]);
        return view('pages/administrador/relatoriu/salariu', $data);
    }

    public function lisensa()
    {
        $filter = $this->leaveFilter('get');
        $page = $this->pagination(['nid' => 'funsionariu.nid', 'naran' => 'funsionariu.naran_kompletu', 'data_hahu' => 'lisensa.data_hahu', 'data_remata' => 'lisensa.data_remata'], 'data_hahu', 'desc');
        $total = $this->RelatoriuModel->countRekapLisensa($filter['data_hahu'], $filter['data_remata'], $filter['estadu'], $filter['tipu_lisensa']);
        $page = $this->pager($page, $total);

        $data = array_merge($this->data, [
            'title' => 'Relatóriu Lisensa',
            'lisensa' => $this->RelatoriuModel->getRekapLisensa($filter['data_hahu'], $filter['data_remata'], $filter['estadu'], $filter['tipu_lisensa'], $page['per_page'], $page['offset'], $page['sort'], $page['direction']),
            'tipu_lisensa' => $this->ApplicationModel->getTipuLisensa(),
            'filter' => $filter,
            'pagination' => $page,
        ]);
        return view('pages/administrador/relatoriu/lisensa', $data);
    }

    public function sansaun()
    {
        $filter = $this->sanctionFilter('get');
        $page = $this->pagination(['nid' => 'funsionariu.nid', 'naran' => 'funsionariu.naran_kompletu', 'data' => 'sansaun.data_sansaun'], 'data', 'desc');
        $total = $this->RelatoriuModel->countRekapSansaun($filter['fulan'], $filter['tinan'], $filter['estadu'], $filter['tipu_sansaun_id']);
        $page = $this->pager($page, $total);

        $data = array_merge($this->data, [
            'title' => 'Relatóriu Sansaun',
            'sansaun' => $this->RelatoriuModel->getRekapSansaun($filter['fulan'], $filter['tinan'], $filter['estadu'], $filter['tipu_sansaun_id'], $page['per_page'], $page['offset'], $page['sort'], $page['direction']),
            'tipu_sansaun' => $this->ApplicationModel->getTipuSansaun(),
            'filter' => $filter,
            'pagination' => $page,
        ]);
        return view('pages/administrador/relatoriu/sansaun', $data);
    }

    // --- Export Methods ---

    public function exportFunsionariu()
    {
        $filter = $this->employeeFilter('post');
        $type = $this->request->getPost('export_type');

        $data = [
            'title' => 'Relatóriu Funsionáriu',
            'funsionariu' => $this->RelatoriuModel->getRekapFunsionariu($filter['diresaun_id'], $filter['pozisaun_id'], $filter['kategoria_id'], $filter['grau_id']),
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
        $filter = $this->attendanceFilter('post');
        $type = $this->request->getPost('export_type');

        $data = [
            'title' => 'Relatóriu Prezensa',
            'prezensa' => $this->RelatoriuModel->getRekapPrezensa($filter['data_hahu'], $filter['data_remata'], $filter['diresaun_id'], $filter['estadu']),
            'data_hahu' => $filter['data_hahu'],
            'data_remata' => $filter['data_remata'],
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
        $filter = $this->periodFilter('post');
        $type = $this->request->getPost('export_type');

        $data = [
            'title' => 'Relatóriu Saláriu',
            'salariu' => $this->RelatoriuModel->getRekapSalariu($filter['fulan'], $filter['tinan']),
            'fulan' => $filter['fulan'],
            'tinan' => $filter['tinan'],
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
        $filter = $this->leaveFilter('post');
        $type = $this->request->getPost('export_type');

        $data = [
            'title' => 'Relatóriu Lisensa',
            'lisensa' => $this->RelatoriuModel->getRekapLisensa($filter['data_hahu'], $filter['data_remata'], $filter['estadu'], $filter['tipu_lisensa']),
            'data_hahu' => $filter['data_hahu'],
            'data_remata' => $filter['data_remata'],
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
        $filter = $this->sanctionFilter('post');
        $type = $this->request->getPost('export_type');

        $data = [
            'title' => 'Relatóriu Sansaun',
            'sansaun' => $this->RelatoriuModel->getRekapSansaun($filter['fulan'], $filter['tinan'], $filter['estadu'], $filter['tipu_sansaun_id']),
            'fulan' => $filter['fulan'],
            'tinan' => $filter['tinan'],
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

    private function pagination(array $sorts, string $defaultSort, string $defaultDirection): array
    {
        $perPage = (int) $this->request->getGet('per_page');
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
        $page = max(1, (int) $this->request->getGet('page'));
        $sortKey = (string) $this->request->getGet('sort');
        $direction = strtolower((string) $this->request->getGet('direction'));

        return [
            'page' => $page,
            'per_page' => $perPage,
            'offset' => ($page - 1) * $perPage,
            'sort' => $sorts[$sortKey] ?? $sorts[$defaultSort],
            'direction' => $direction === 'desc' ? 'DESC' : ($direction === 'asc' ? 'ASC' : strtoupper($defaultDirection)),
        ];
    }

    private function pager(array $page, int $total): array
    {
        $page['total'] = $total;
        $page['pages'] = max(1, (int) ceil($total / $page['per_page']));
        $page['page'] = min($page['page'], $page['pages']);
        $page['offset'] = ($page['page'] - 1) * $page['per_page'];
        return $page;
    }

    private function employeeFilter(string $method): array
    {
        return [
            'diresaun_id' => $this->positiveInt($method, 'diresaun_id'),
            'pozisaun_id' => $this->positiveInt($method, 'pozisaun_id'),
            'kategoria_id' => $this->positiveInt($method, 'kategoria_id'),
            'grau_id' => $this->positiveInt($method, 'grau_id'),
        ];
    }

    private function attendanceFilter(string $method): array
    {
        [$start, $end] = $this->dateRange($method);
        $status = $this->input($method, 'estadu');
        return ['data_hahu' => $start, 'data_remata' => $end, 'diresaun_id' => $this->positiveInt($method, 'diresaun_id'), 'estadu' => in_array($status, ['Prezente', 'Loron Sorin', 'Falta', 'Lisensa', 'Incomplete'], true) ? $status : null];
    }

    private function leaveFilter(string $method): array
    {
        [$start, $end] = $this->dateRange($method);
        $status = $this->input($method, 'estadu');
        $type = $this->input($method, 'tipu_lisensa');
        return ['data_hahu' => $start, 'data_remata' => $end, 'estadu' => in_array($status, ['Pendente', 'Aprovadu', 'Rejeitadu'], true) ? $status : null, 'tipu_lisensa' => $type !== '' && strlen($type) <= 100 ? $type : null];
    }

    private function periodFilter(string $method): array
    {
        $month = (int) $this->input($method, 'fulan');
        $year = (int) $this->input($method, 'tinan');
        return ['fulan' => $month >= 1 && $month <= 12 ? $month : (int) date('n'), 'tinan' => $year >= 2000 && $year <= (int) date('Y') + 1 ? $year : (int) date('Y')];
    }

    private function sanctionFilter(string $method): array
    {
        $filter = $this->periodFilter($method);
        $status = $this->input($method, 'estadu');
        $filter['estadu'] = in_array($status, ['Ativu', 'Konkluidu', 'Retira'], true) ? $status : null;
        $filter['tipu_sansaun_id'] = $this->positiveInt($method, 'tipu_sansaun_id');
        return $filter;
    }

    private function dateRange(string $method): array
    {
        $start = $this->validDate($this->input($method, 'data_hahu')) ?: date('Y-m-01');
        $end = $this->validDate($this->input($method, 'data_remata')) ?: date('Y-m-t');
        return $end < $start ? [$start, $start] : [$start, $end];
    }

    private function validDate(string $value): ?string
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : null;
    }

    private function positiveInt(string $method, string $key): ?int
    {
        $value = filter_var($this->input($method, $key), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return $value === false ? null : $value;
    }

    private function input(string $method, string $key): string
    {
        return trim((string) ($method === 'post' ? $this->request->getPost($key) : $this->request->getGet($key)));
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
