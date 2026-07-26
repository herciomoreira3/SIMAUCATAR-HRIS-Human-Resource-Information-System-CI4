<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class RelatoriuModel extends Model
{
    public function getRekapFunsionariu($departamentuId = null, $pozisaunId = null, $kategoriaId = null, $grauId = null, ?int $perPage = null, int $offset = 0, string $sort = 'funsionariu.naran_kompletu', string $direction = 'ASC'): array
    {
        $builder = $this->funsionariuBuilder($departamentuId, $pozisaunId, $kategoriaId, $grauId);
        return $this->result($builder, $perPage, $offset, $sort, $direction);
    }

    public function countRekapFunsionariu($departamentuId = null, $pozisaunId = null, $kategoriaId = null, $grauId = null): int
    {
        return $this->count($this->funsionariuBuilder($departamentuId, $pozisaunId, $kategoriaId, $grauId));
    }

    public function getRekapPrezensa(string $dataHahu, string $dataRemata, $departamentuId = null, $estadu = null, ?int $perPage = null, int $offset = 0, string $sort = 'funsionariu.naran_kompletu', string $direction = 'ASC'): array
    {
        $builder = $this->prezensaBuilder($dataHahu, $dataRemata, $departamentuId, $estadu);
        return $this->result($builder, $perPage, $offset, $sort, $direction);
    }

    public function countRekapPrezensa(string $dataHahu, string $dataRemata, $departamentuId = null, $estadu = null): int
    {
        $builder = $this->prezensaBuilder($dataHahu, $dataRemata, $departamentuId, $estadu);
        // The status filter is an aggregate HAVING clause, so count the grouped
        // result in SQL instead of loading every employee summary into PHP.
        $sql = $builder->getCompiledSelect();
        return (int) ($this->db->query('SELECT COUNT(*) AS total FROM (' . $sql . ') AS report_rows')->getRowArray()['total'] ?? 0);
    }

    public function getRekapSalariu($fulan, $tinan, ?int $perPage = null, int $offset = 0, string $sort = 'funsionariu.naran_kompletu', string $direction = 'ASC'): array
    {
        $builder = $this->salariuBuilder($fulan, $tinan);
        return $this->result($builder, $perPage, $offset, $sort, $direction);
    }

    public function countRekapSalariu($fulan, $tinan): int
    {
        return $this->count($this->salariuBuilder($fulan, $tinan));
    }

    public function getRekapLisensa(string $dataHahu, string $dataRemata, $estadu = null, $tipuLisensa = null, ?int $perPage = null, int $offset = 0, string $sort = 'lisensa.data_hahu', string $direction = 'DESC'): array
    {
        $builder = $this->lisensaBuilder($dataHahu, $dataRemata, $estadu, $tipuLisensa);
        return $this->result($builder, $perPage, $offset, $sort, $direction);
    }

    public function countRekapLisensa(string $dataHahu, string $dataRemata, $estadu = null, $tipuLisensa = null): int
    {
        return $this->count($this->lisensaBuilder($dataHahu, $dataRemata, $estadu, $tipuLisensa));
    }

    public function getRekapSansaun($fulan, $tinan, $estadu = null, $tipuSansaunId = null, ?int $perPage = null, int $offset = 0, string $sort = 'sansaun.data_sansaun', string $direction = 'DESC'): array
    {
        $builder = $this->sansaunBuilder($fulan, $tinan, $estadu, $tipuSansaunId);
        return $this->result($builder, $perPage, $offset, $sort, $direction);
    }

    public function countRekapSansaun($fulan, $tinan, $estadu = null, $tipuSansaunId = null): int
    {
        return $this->count($this->sansaunBuilder($fulan, $tinan, $estadu, $tipuSansaunId));
    }

    private function funsionariuBuilder($departamentuId, $pozisaunId, $kategoriaId, $grauId): BaseBuilder
    {
        $builder = $this->db->table('funsionariu')->select('funsionariu.*, departamentu.naran_departamentu, departamentu.naran_departamentu AS naran_diresaun, pozisaun.naran_pozisaun, kategoria.naran_kategoria, grau.naran_grau')->join('departamentu', 'funsionariu.departamentu_id = departamentu.id')->join('pozisaun', 'funsionariu.pozisaun_id = pozisaun.id')->join('kategoria', 'funsionariu.kategoria_id = kategoria.id')->join('grau', 'funsionariu.grau_id = grau.id', 'left');
        foreach (['departamentu_id' => $departamentuId, 'pozisaun_id' => $pozisaunId, 'kategoria_id' => $kategoriaId, 'grau_id' => $grauId] as $field => $value) {
            if ($value !== null) $builder->where('funsionariu.' . $field, $value);
        }
        return $builder;
    }

    private function prezensaBuilder(string $dataHahu, string $dataRemata, $departamentuId, $estadu): BaseBuilder
    {
        $builder = $this->db->table('prezensa')->select('funsionariu.nid, funsionariu.naran_kompletu, departamentu.naran_departamentu, departamentu.naran_departamentu AS naran_diresaun, SUM(IF(estadu_prezensa = "Prezente", 1, 0)) AS total_prezente, SUM(IF(estadu_prezensa = "Loron Sorin", 1, 0)) AS total_loron_sorin, SUM(IF(estadu_prezensa = "Falta", 1, 0)) AS total_falta, SUM(IF(estadu_prezensa = "Lisensa", 1, 0)) AS total_lisensa, SUM(IF(estadu_prezensa = "Incomplete", 1, 0)) AS total_incomplete')->join('funsionariu', 'prezensa.funsionariu_id = funsionariu.id')->join('departamentu', 'funsionariu.departamentu_id = departamentu.id')->where('data_prezensa >=', $dataHahu)->where('data_prezensa <=', $dataRemata)->groupBy('prezensa.funsionariu_id');
        if ($departamentuId !== null) $builder->where('funsionariu.departamentu_id', $departamentuId);
        $having = ['Prezente' => 'total_prezente', 'Loron Sorin' => 'total_loron_sorin', 'Falta' => 'total_falta', 'Lisensa' => 'total_lisensa', 'Incomplete' => 'total_incomplete'];
        if (isset($having[$estadu])) $builder->having($having[$estadu] . ' >', 0);
        return $builder;
    }

    private function salariuBuilder($fulan, $tinan): BaseBuilder
    {
        return $this->db->table('salariu')->select('salariu.*, funsionariu.nid, funsionariu.naran_kompletu')->join('funsionariu', 'salariu.funsionariu_id = funsionariu.id')->where('salariu.fulan', $fulan)->where('salariu.tinan', $tinan);
    }

    private function lisensaBuilder(string $dataHahu, string $dataRemata, $estadu, $tipuLisensa): BaseBuilder
    {
        $builder = $this->db->table('lisensa')->select('lisensa.*, funsionariu.nid, funsionariu.naran_kompletu')->join('funsionariu', 'lisensa.funsionariu_id = funsionariu.id')->where('data_hahu <=', $dataRemata)->where('data_remata >=', $dataHahu);
        if ($estadu !== null) $builder->where('estadu_lisensa', $estadu);
        if ($tipuLisensa !== null) $builder->where('tipu_lisensa', $tipuLisensa);
        return $builder;
    }

    private function sansaunBuilder($fulan, $tinan, $estadu, $tipuSansaunId): BaseBuilder
    {
        $start = sprintf('%04d-%02d-01', $tinan, $fulan);
        $end = (new \DateTimeImmutable($start))->modify('+1 month')->format('Y-m-d');
        $builder = $this->db->table('sansaun')->select('sansaun.*, funsionariu.nid, funsionariu.naran_kompletu, tipu_sansaun.naran_tipu')->join('funsionariu', 'sansaun.funsionariu_id = funsionariu.id')->join('tipu_sansaun', 'sansaun.tipu_sansaun_id = tipu_sansaun.id')->where('sansaun.data_sansaun >=', $start)->where('sansaun.data_sansaun <', $end);
        if ($estadu !== null) $builder->where('estadu_sansaun', $estadu);
        if ($tipuSansaunId !== null) $builder->where('sansaun.tipu_sansaun_id', $tipuSansaunId);
        return $builder;
    }

    private function result(BaseBuilder $builder, ?int $perPage, int $offset, string $sort, string $direction): array
    {
        $builder->orderBy($sort, $direction);
        return $perPage === null ? $builder->get()->getResultArray() : $builder->get($perPage, $offset)->getResultArray();
    }

    private function count(BaseBuilder $builder): int
    {
        return (int) $builder->countAllResults();
    }
}
