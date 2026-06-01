<?php

namespace App\Models;

use CodeIgniter\Model;

class RelatoriuModel extends Model
{
    public function getRekapFunsionariu($departamentu_id = null, $pozisaun_id = null)
    {
        $builder = $this->db->table('funsionariu')
            ->select('funsionariu.*, departamentu.naran_departamentu, pozisaun.naran_pozisaun, kategoria.naran_kategoria')
            ->join('departamentu', 'funsionariu.departamentu_id = departamentu.id')
            ->join('pozisaun', 'funsionariu.pozisaun_id = pozisaun.id')
            ->join('kategoria', 'funsionariu.kategoria_id = kategoria.id');

        if ($departamentu_id) {
            $builder->where('funsionariu.departamentu_id', $departamentu_id);
        }
        if ($pozisaun_id) {
            $builder->where('funsionariu.pozisaun_id', $pozisaun_id);
        }

        return $builder->get()->getResultArray();
    }

    public function getRekapPrezensa($data_hahu, $data_remata, $departamentu_id = null)
    {
        $builder = $this->db->table('prezensa')
            ->select('funsionariu.nid, funsionariu.naran_kompletu, departamentu.naran_departamentu,
                      SUM(IF(estadu_prezensa = "Prezente", 1, 0)) as total_prezente,
                      SUM(IF(estadu_prezensa = "Tardi", 1, 0)) as total_tardi,
                      SUM(IF(estadu_prezensa = "Falta", 1, 0)) as total_falta,
                      SUM(IF(estadu_prezensa = "Lisensa", 1, 0)) as total_lisensa,
                      SUM(IF(estadu_prezensa = "Incomplete", 1, 0)) as total_incomplete')
            ->join('funsionariu', 'prezensa.funsionariu_id = funsionariu.id')
            ->join('departamentu', 'funsionariu.departamentu_id = departamentu.id')
            ->where('data_prezensa >=', $data_hahu)
            ->where('data_prezensa <=', $data_remata);


        if ($departamentu_id) {
            $builder->where('funsionariu.departamentu_id', $departamentu_id);
        }

        return $builder->groupBy('prezensa.funsionariu_id')->get()->getResultArray();
    }

    public function getRekapSalariu($fulan, $tinan)
    {
        return $this->db->table('salariu')
            ->select('salariu.*, funsionariu.nid, funsionariu.naran_kompletu')
            ->join('funsionariu', 'salariu.funsionariu_id = funsionariu.id')
            ->where('fulan', $fulan)
            ->where('tinan', $tinan)
            ->get()->getResultArray();
    }

    public function getRekapLisensa($data_hahu, $data_remata, $estadu = null)
    {
        $builder = $this->db->table('lisensa')
            ->select('lisensa.*, funsionariu.nid, funsionariu.naran_kompletu')
            ->join('funsionariu', 'lisensa.funsionariu_id = funsionariu.id')
            ->where('data_hahu <=', $data_remata)
            ->where('data_remata >=', $data_hahu);

        if ($estadu) {
            $builder->where('estadu_lisensa', $estadu);
        }

        return $builder->get()->getResultArray();
    }

    public function getRekapSansaun($fulan, $tinan, $estadu = null)
    {
        $builder = $this->db->table('sansaun')
            ->select('sansaun.*, funsionariu.nid, funsionariu.naran_kompletu, tipu_sansaun.naran_tipu')
            ->join('funsionariu', 'sansaun.funsionariu_id = funsionariu.id')
            ->join('tipu_sansaun', 'sansaun.tipu_sansaun_id = tipu_sansaun.id')
            ->where('MONTH(data_sansaun)', $fulan)
            ->where('YEAR(data_sansaun)', $tinan);

        if ($estadu) {
            $builder->where('estadu_sansaun', $estadu);
        }

        return $builder->get()->getResultArray();
    }
}
