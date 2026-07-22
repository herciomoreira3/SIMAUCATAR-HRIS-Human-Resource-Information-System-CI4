<?php

namespace App\Commands;

use App\Models\ApplicationModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MarkAbsent extends BaseCommand
{
    protected $group       = 'Attendance';
    protected $name        = 'attendance:mark-absent';
    protected $description = 'Marks missing attendance records after the configured work windows.';

    public function run(array $params)
    {
        $db = db_connect();
        $model = new ApplicationModel();
        $settings = $model->getAttendanceSettings();

        if (!$settings) {
            CLI::error('Attendance settings not found.');
            return;
        }

        $date = $params[0] ?? date('Y-m-d');
        $now = date('H:i:s');
        $dayOfWeek = (int) date('N', strtotime($date));
        $isWorkDay = !($dayOfWeek === 6 && (int) $settings['sabadu'] === 0) &&
                     !($dayOfWeek === 7 && (int) $settings['domingu'] === 0);

        if (!$isWorkDay) {
            CLI::write("Skipped $date because it is not configured as a work day.");
            return;
        }

        if ($db->tableExists('holidays')) {
            $holiday = $db->table('holidays')->where('holiday_date', $date)->get()->getRowArray();
            if ($holiday) {
                CLI::write("Skipped $date because it is a holiday: " . $holiday['title']);
                return;
            }
        }

        $createdFalta = 0;
        $markedLoronSorin = 0;
        $addedToSansaun = 0;

        foreach ($model->getFunsionariu() as $employee) {
            $existing = $db->table('prezensa')
                ->where('funsionariu_id', $employee['id'])
                ->where('data_prezensa', $date)
                ->get()->getRowArray();

            if ($existing) {
                // Determine status
                $hasTamaDader = !empty($existing['oras_tama_dader']);
                $hasSaiDader = !empty($existing['oras_sai_dader']);
                $hasTamaLokraik = !empty($existing['oras_tama_lokraik']);
                $hasSaiLokraik = !empty($existing['oras_sai_lokraik']);
                $allPresent = $hasTamaDader && $hasSaiDader && $hasTamaLokraik && $hasSaiLokraik;
                $anyPresent = $hasTamaDader || $hasSaiDader || $hasTamaLokraik || $hasSaiLokraik;

                $newStatus = '';
                if ($allPresent) {
                    $newStatus = 'Prezente';
                } elseif ($anyPresent) {
                    $newStatus = 'Loron Sorin';
                } else {
                    // Already has a record but no times, could be Lisensa or Falta
                    $approvedLeave = $db->table('lisensa')
                        ->where('funsionariu_id', $employee['id'])
                        ->where('estadu_lisensa', 'Aprovadu')
                        ->where('data_hahu <=', $date)
                        ->where('data_remata >=', $date)
                        ->get()->getRowArray();
                    if ($approvedLeave) {
                        $newStatus = 'Lisensa';
                    }
                }

                if ($newStatus && $existing['estadu_prezensa'] !== $newStatus) {
                    $db->table('prezensa')->where('id', $existing['id'])->update([
                        'estadu_prezensa' => $newStatus,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    if ($newStatus === 'Loron Sorin') {
                        $markedLoronSorin++;
                    }
                }
            } else {
                // Check for leave
                $approvedLeave = $db->table('lisensa')
                    ->where('funsionariu_id', $employee['id'])
                    ->where('estadu_lisensa', 'Aprovadu')
                    ->where('data_hahu <=', $date)
                    ->where('data_remata >=', $date)
                    ->get()->getRowArray();

                $status = $approvedLeave ? 'Lisensa' : 'Falta';
                $db->table('prezensa')->insert([
                    'funsionariu_id' => $employee['id'],
                    'data_prezensa' => $date,
                    'estadu_prezensa' => $status,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                if ($status === 'Falta') {
                    $createdFalta++;
                }
            }

            // Now check if we need to add to sansaun
            // Calculate total falta days
            $loronSorinCount = $db->table('prezensa')
                ->where('funsionariu_id', $employee['id'])
                ->where('estadu_prezensa', 'Loron Sorin')
                ->countAllResults();
            $faltaCount = $db->table('prezensa')
                ->where('funsionariu_id', $employee['id'])
                ->where('estadu_prezensa', 'Falta')
                ->countAllResults();
            $totalFaltaDays = (int) floor($loronSorinCount / 2) + $faltaCount;

            if ($totalFaltaDays >= 3) {
                $existingSansaun = $db->table('sansaun')
                    ->where('funsionariu_id', $employee['id'])
                    ->orderBy('data_sansaun', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();

                if (!$existingSansaun || strtotime($existingSansaun['data_sansaun']) < strtotime('-30 days')) {
                    $tipuSansaunId = null;
                    if ($db->tableExists('tipu_sansaun')) {
                        $tipuSansaun = $db->table('tipu_sansaun')->limit(1)->get()->getRowArray();
                        $tipuSansaunId = $tipuSansaun['id'] ?? null;
                    }

                    $sansaunData = [
                        'funsionariu_id' => $employee['id'],
                        'motivu' => 'Absente ' . $totalFaltaDays . ' loron ka liu',
                        'data_sansaun' => date('Y-m-d'),
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    if ($tipuSansaunId && $db->fieldExists('tipu_sansaun_id', 'sansaun')) {
                        $sansaunData['tipu_sansaun_id'] = $tipuSansaunId;
                    }

                    $db->table('sansaun')->insert($sansaunData);
                    $addedToSansaun++;
                    CLI::write("Added employee " . $employee['naran_kompletu'] . " to Sansaun.");
                }
            }
        }

        CLI::write("Attendance checked for $date. Falta created: $createdFalta. Loron Sorin marked: $markedLoronSorin. Added to Sansaun: $addedToSansaun.");
    }
}
