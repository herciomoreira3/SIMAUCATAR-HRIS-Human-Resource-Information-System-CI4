<?php

namespace App\Commands;

use App\Models\ApplicationModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MarkAbsent extends BaseCommand
{
    protected $group = 'Attendance';
    protected $name = 'attendance:mark-absent';
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
        $isWorkDay = !($dayOfWeek === 6 && (int) $settings['sabadu'] === 0)
            && !($dayOfWeek === 7 && (int) $settings['domingu'] === 0);

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
        $markedIncomplete = 0;

        if ($date < date('Y-m-d') || strtotime($now) > strtotime($settings['tama_remata'])) {
            foreach ($model->getFunsionariu() as $employee) {
                $existing = $db->table('prezensa')
                    ->where('funsionariu_id', $employee['id'])
                    ->where('data_prezensa', $date)
                    ->get()->getRowArray();

                if ($existing) {
                    continue;
                }

                $approvedLeave = $db->table('lisensa')
                    ->where('funsionariu_id', $employee['id'])
                    ->where('estadu_lisensa', 'Aprovadu')
                    ->where('data_hahu <=', $date)
                    ->where('data_remata >=', $date)
                    ->get()->getRowArray();

                if ($approvedLeave) {
                    $db->table('prezensa')->insert([
                        'funsionariu_id' => $employee['id'],
                        'data_prezensa' => $date,
                        'estadu_prezensa' => 'Lisensa',
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    continue;
                }

                $db->table('prezensa')->insert([
                    'funsionariu_id' => $employee['id'],
                    'data_prezensa' => $date,
                    'estadu_prezensa' => 'Falta',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $createdFalta++;
            }
        }

        if ($date < date('Y-m-d') || strtotime($now) > strtotime($settings['sai_remata'])) {
            $incomplete = $db->table('prezensa')
                ->where('data_prezensa', $date)
                ->where('oras_tama !=', null)
                ->where('oras_sai', null)
                ->whereIn('estadu_prezensa', ['Prezente', 'Tardi'])
                ->get()->getResultArray();

            foreach ($incomplete as $row) {
                $db->table('prezensa')->where('id', $row['id'])->update([
                    'estadu_prezensa' => 'Incomplete',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $markedIncomplete++;
            }
        }

        CLI::write("Attendance checked for $date. Falta created: $createdFalta. Incomplete marked: $markedIncomplete.");
    }
}
