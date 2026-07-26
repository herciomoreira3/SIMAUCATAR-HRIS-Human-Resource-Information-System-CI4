<?php

namespace App\Repositories;

use CodeIgniter\Database\BaseConnection;
use DateTimeImmutable;

final class DashboardRepository
{
    private const STATUSES = ['Prezente', 'Loron Sorin', 'Falta', 'Lisensa'];

    public function __construct(private BaseConnection $db)
    {
    }

    public function getAdminAttendanceTrend(DateTimeImmutable $start, DateTimeImmutable $end): array
    {
        return $this->attendanceTrend($start, $end);
    }

    public function getEmployeeAttendanceTrend(int $employeeId, DateTimeImmutable $start, DateTimeImmutable $end): array
    {
        return $this->attendanceTrend($start, $end, $employeeId);
    }

    public function getAdminKpis(DateTimeImmutable $today): array
    {
        return $this->db->query(
            "SELECT
                (SELECT COUNT(*) FROM funsionariu) AS total_funsionariu,
                (SELECT COUNT(*) FROM prezensa WHERE data_prezensa = ?) AS total_prezensa_ohin,
                (SELECT COUNT(*) FROM lisensa WHERE estadu_lisensa = 'Pendente') AS pendente_lisensa",
            [$today->format('Y-m-d')]
        )->getRowArray() ?: [];
    }

    public function getEmployeeAttendanceTotals(int $employeeId): array
    {
        $rows = $this->db->table('prezensa')
            ->select('estadu_prezensa, COUNT(*) AS total')
            ->where('funsionariu_id', $employeeId)
            ->groupBy('estadu_prezensa')
            ->get()->getResultArray();

        $totals = array_fill_keys(self::STATUSES, 0);
        foreach ($rows as $row) {
            if (array_key_exists($row['estadu_prezensa'], $totals)) {
                $totals[$row['estadu_prezensa']] = (int) $row['total'];
            }
        }

        return $totals;
    }

    public function getDepartmentComposition(): array
    {
        return $this->db->table('funsionariu')
            ->select('departamentu.naran_departamentu AS naran_diresaun, COUNT(funsionariu.id) AS total')
            ->join('departamentu', 'funsionariu.departamentu_id = departamentu.id')
            ->groupBy('funsionariu.departamentu_id, departamentu.naran_departamentu')
            ->get()->getResultArray();
    }

    public function getLatestAnnouncements(int $limit, DateTimeImmutable $now): array
    {
        return $this->db->table('avizu')
            ->select('id, titulu, konteudu, data_publikasaun')
            ->groupStart()
                ->where('data_remata', null)
                ->orWhere('data_remata >', $now->format('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('data_publikasaun', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    public function getLatestSanctions(int $limit): array
    {
        return $this->db->table('sansaun s')
            ->select('s.id, s.data_sansaun, f.naran_kompletu, ts.naran_tipu, ts.kategoria')
            ->join('funsionariu f', 'f.id = s.funsionariu_id')
            ->join('tipu_sansaun ts', 'ts.id = s.tipu_sansaun_id', 'left')
            ->orderBy('s.created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /** @return array{labels: list<string>, series: array<string, list<int>>} */
    public static function mapAttendanceSeries(array $rows, DateTimeImmutable $start, DateTimeImmutable $end): array
    {
        $seriesByDate = [];
        $labels = [];
        for ($date = $start; $date <= $end; $date = $date->modify('+1 day')) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            foreach (self::STATUSES as $status) {
                $seriesByDate[$status][$key] = 0;
            }
        }

        foreach ($rows as $row) {
            $status = $row['estadu_prezensa'] ?? null;
            $date = $row['data_prezensa'] ?? null;
            if (isset($seriesByDate[$status][$date])) {
                $seriesByDate[$status][$date] = (int) $row['total'];
            }
        }

        $series = [];
        foreach (self::STATUSES as $status) {
            $series[$status] = array_values($seriesByDate[$status]);
        }

        return ['labels' => $labels, 'series' => $series];
    }

    private function attendanceTrend(DateTimeImmutable $start, DateTimeImmutable $end, ?int $employeeId = null): array
    {
        $builder = $this->db->table('prezensa')
            ->select('data_prezensa, estadu_prezensa, COUNT(*) AS total')
            ->where('data_prezensa >=', $start->format('Y-m-d'))
            ->where('data_prezensa <=', $end->format('Y-m-d'));
        if ($employeeId !== null) {
            $builder->where('funsionariu_id', $employeeId);
        }

        return self::mapAttendanceSeries(
            $builder->groupBy('data_prezensa, estadu_prezensa')->orderBy('data_prezensa', 'ASC')->get()->getResultArray(),
            $start,
            $end
        );
    }
}
