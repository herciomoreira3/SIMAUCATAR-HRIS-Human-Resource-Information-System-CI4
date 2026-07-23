<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * CronJob Controller
 *
 * Endpoint HTTP aman yang dipanggil oleh cronjob.org
 * untuk menandai funsionáriu sebagai Falta atau Loron Sorin secara otomatis
 * setelah sesi pagi (Dader) atau sesi sore (Lokraik) berakhir.
 *
 * Setup di cronjob.org:
 *   URL Sesi Pagi   : https://your-app.onrender.com/cron/mark-absent?token=YOUR_TOKEN
 *   URL Sesi Sore   : https://your-app.onrender.com/cron/mark-absent?token=YOUR_TOKEN
 *   Metode          : GET
 *   Jadwal Pagi     : 30 menit setelah sai_remata_dader (mis. jam 13:30)
 *   Jadwal Sore     : 30 menit setelah sai_remata_lokraik (mis. jam 18:30)
 *
 * Set env variable CRON_SECRET_TOKEN di Render.com dashboard.
 */
class CronJob extends Controller
{
    protected $db;
    protected ApplicationModel $model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db    = \Config\Database::connect();
        $this->model = new ApplicationModel();
    }

    // -----------------------------------------------------------------------
    // Validasi Token Keamanan
    // -----------------------------------------------------------------------
    private function isAuthorized(): bool
    {
        $expectedToken = getenv('CRON_SECRET_TOKEN') ?: env('cron.secretToken') ?: '';

        if (empty($expectedToken)) {
            log_message('warning', '[CronJob] CRON_SECRET_TOKEN not configured. Rejecting request.');
            return false;
        }

        $providedToken = $this->request->getGet('token')
            ?? $this->request->getHeaderLine('X-Cron-Token');

        return hash_equals($expectedToken, (string) $providedToken);
    }

    // -----------------------------------------------------------------------
    // Endpoint Utama: Tandai Falta / Loron Sorin
    // GET /cron/mark-absent?token=YOUR_TOKEN
    // -----------------------------------------------------------------------
    public function markAbsent(): ResponseInterface
    {
        if (!$this->isAuthorized()) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $settings = $this->model->getAttendanceSettings();
        if (!$settings) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Attendance settings not found.']);
        }

        $date      = date('Y-m-d');
        $now       = date('H:i:s');
        $dayOfWeek = (int) date('N');

        $isWorkDay = true;
        if ($dayOfWeek === 6 && (int) ($settings['sabadu'] ?? 0) === 0) $isWorkDay = false;
        if ($dayOfWeek === 7 && (int) ($settings['domingu'] ?? 0) === 0) $isWorkDay = false;

        if (!$isWorkDay) {
            return $this->response->setJSON([
                'status'  => 'skipped',
                'message' => "Bukan hari kerja ($date). Tidak ada yang diproses.",
            ]);
        }

        if ($this->db->tableExists('holidays')) {
            $holiday = $this->db->table('holidays')
                ->where('holiday_date', $date)
                ->get()->getRowArray();
            if ($holiday) {
                return $this->response->setJSON([
                    'status'  => 'skipped',
                    'message' => 'Hari libur: ' . ($holiday['title'] ?? $date) . '. Tidak ada yang diproses.',
                ]);
            }
        }

        $daderEnded   = $now > ($settings['sai_remata_dader']   ?? '13:00:00');
        $lokraikEnded = $now > ($settings['sai_remata_lokraik'] ?? '18:00:00');

        if (!$daderEnded && !$lokraikEnded) {
            return $this->response->setJSON([
                'status'  => 'skipped',
                'message' => "Kedua sesi belum berakhir. Cron berjalan terlalu awal ($now).",
            ]);
        }

        $employees = $this->db->table('funsionariu')->get()->getResultArray();

        $stats = [
            'falta_baru'    => 0,
            'loron_sorin'   => 0,
            'prezente'      => 0,
            'lisensa'       => 0,
            'tidak_berubah' => 0,
            'total'         => count($employees),
        ];

        foreach ($employees as $employee) {
            $result = $this->processEmployee($employee, $date, $settings, $daderEnded, $lokraikEnded);
            $stats[$result]++;
        }

        log_message('info', "[CronJob markAbsent] $date | " . json_encode($stats));

        return $this->response->setJSON([
            'status' => 'success',
            'date'   => $date,
            'time'   => $now,
            'session'=> $lokraikEnded ? 'Loron Tomak (Dader + Lokraik)' : 'Dader saja',
            'stats'  => $stats,
        ]);
    }

    // -----------------------------------------------------------------------
    // Proses setiap funsionáriu
    // -----------------------------------------------------------------------
    private function processEmployee(array $employee, string $date, array $settings, bool $daderEnded, bool $lokraikEnded): string
    {
        $empId = $employee['id'];

        if ($this->isOnApprovedLeave($empId, $date)) {
            $existing = $this->db->table('prezensa')
                ->where('funsionariu_id', $empId)
                ->where('data_prezensa', $date)
                ->get()->getRowArray();

            if (!$existing) {
                $this->db->table('prezensa')->insert([
                    'funsionariu_id'  => $empId,
                    'data_prezensa'   => $date,
                    'estadu_prezensa' => 'Lisensa',
                    'created_at'      => date('Y-m-d H:i:s'),
                ]);
            } elseif ($existing['estadu_prezensa'] === 'Incomplete') {
                $this->db->table('prezensa')
                    ->where('id', $existing['id'])
                    ->update(['estadu_prezensa' => 'Lisensa', 'updated_at' => date('Y-m-d H:i:s')]);
            }
            return 'lisensa';
        }

        $existing = $this->db->table('prezensa')
            ->where('funsionariu_id', $empId)
            ->where('data_prezensa', $date)
            ->get()->getRowArray();

        if (!$existing) {
            if ($lokraikEnded) {
                $this->db->table('prezensa')->insert([
                    'funsionariu_id'  => $empId,
                    'data_prezensa'   => $date,
                    'estadu_prezensa' => 'Falta',
                    'created_at'      => date('Y-m-d H:i:s'),
                ]);
                return 'falta_baru';
            } elseif ($daderEnded) {
                $this->db->table('prezensa')->insert([
                    'funsionariu_id'  => $empId,
                    'data_prezensa'   => $date,
                    'estadu_prezensa' => 'Incomplete',
                    'created_at'      => date('Y-m-d H:i:s'),
                ]);
                return 'tidak_berubah';
            }
        }

        $hasTamaDader   = !empty($existing['oras_tama_dader']);
        $hasSaiDader    = !empty($existing['oras_sai_dader']);
        $hasTamaLokraik = !empty($existing['oras_tama_lokraik']);
        $hasSaiLokraik  = !empty($existing['oras_sai_lokraik']);

        $hasDaderSession   = $hasTamaDader   || $hasSaiDader;
        $hasLokraikSession = $hasTamaLokraik || $hasSaiLokraik;

        $newStatus = null;

        if ($lokraikEnded) {
            if ($hasDaderSession && $hasLokraikSession) {
                $newStatus = 'Prezente';
            } elseif ($hasDaderSession || $hasLokraikSession) {
                $newStatus = 'Loron Sorin';
            } else {
                $skip = ['Lisensa', 'Holiday', 'Weekend'];
                if (!in_array($existing['estadu_prezensa'], $skip, true)) {
                    $newStatus = 'Falta';
                }
            }
        } elseif ($daderEnded) {
            if (!$hasDaderSession) {
                $fixed = ['Lisensa', 'Holiday', 'Weekend', 'Prezente', 'Loron Sorin', 'Falta'];
                if (!in_array($existing['estadu_prezensa'], $fixed, true)) {
                    $newStatus = 'Incomplete';
                }
            }
        }

        if ($newStatus !== null && $existing['estadu_prezensa'] !== $newStatus) {
            $this->db->table('prezensa')
                ->where('id', $existing['id'])
                ->update(['estadu_prezensa' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);

            if ($newStatus === 'Falta')      return 'falta_baru';
            if ($newStatus === 'Loron Sorin') return 'loron_sorin';
            if ($newStatus === 'Prezente')    return 'prezente';
        }

        return 'tidak_berubah';
    }

    // -----------------------------------------------------------------------
    // Cek cuti yang disetujui
    // -----------------------------------------------------------------------
    private function isOnApprovedLeave(int $empId, string $date): bool
    {
        if (!$this->db->tableExists('lisensa')) return false;

        $leave = $this->db->table('lisensa')
            ->where('funsionariu_id', $empId)
            ->where('estadu_lisensa', 'Aprovadu')
            ->where('data_hahu <=', $date)
            ->where('data_remata >=', $date)
            ->get()->getRowArray();

        return !empty($leave);
    }

    // -----------------------------------------------------------------------
    // Health-check Ping (tidak mengubah data)
    // GET /cron/ping?token=YOUR_TOKEN
    // -----------------------------------------------------------------------
    public function ping(): ResponseInterface
    {
        if (!$this->isAuthorized()) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $settings = $this->model->getAttendanceSettings();

        return $this->response->setJSON([
            'status'               => 'ok',
            'server_time'          => date('Y-m-d H:i:s'),
            'sai_remata_dader'     => $settings['sai_remata_dader']   ?? '13:00:00',
            'sai_remata_lokraik'   => $settings['sai_remata_lokraik'] ?? '18:00:00',
            'message'              => 'CronJob endpoint aktif dan siap.',
        ]);
    }
}
