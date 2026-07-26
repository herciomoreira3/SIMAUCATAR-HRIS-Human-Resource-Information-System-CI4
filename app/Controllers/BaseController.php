<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Repositories\DashboardRepository;
use App\Repositories\NavigationRepository;
use App\Services\NavigationService;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['cookie', 'date', 'security', 'menu', 'useraccess'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session, $segment, $validation, $encrypter, $ApplicationModel, $db, $data = [];
    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        $this->session          = service('session');
        $this->segment          = service('uri');
        $this->ApplicationModel = new ApplicationModel();
        $this->db               = \Config\Database::connect();

        $segment = $this->segment->getSegment(1);
        if ($segment) {
            $subsegment = $this->segment->getSegment(2);
        } else {
            $subsegment = '';
        }
        $role = (int) session()->get('role');
        $authenticated = (bool) session()->get('isLoggedIn') && (int) session()->get('userID') > 0;
        $user = null;
        $navigation = [];
        $announcements = [];

        if ($authenticated && $segment !== '' && $segment !== 'dashboard') {
            $user = $this->ApplicationModel->getLayoutUser((int) session()->get('userID'));
            if ($role > 0) {
                $navigation = (new NavigationService(
                    new NavigationRepository($this->db),
                    cache()
                ))->forRole($role);
            }
            $announcements = (new DashboardRepository($this->db))->getLatestAnnouncements(5, new \DateTimeImmutable('now', new \DateTimeZone('Asia/Dili')));
        }

        $this->data = [
            'segment'        => $segment,
            'subsegment'     => $subsegment,
            'user'           => $user,
            'navigation'     => $navigation,
            'MenuCategory'   => [],
            'avizu_notif'    => $announcements,
        ];
    }

    protected function autoMarkAbsent()
    {
        // Don't run if DB not connected or model not ready
        if (!$this->ApplicationModel) return;

        $settings = $this->ApplicationModel->getAttendanceSettings();
        if (!$settings) return;

        $ohin = date('Y-m-d');
        $now = date('H:i:s');
        $dayOfWeek = date('N'); // 1 (Mon) to 7 (Sun)

        // 1. Handle missing Clock-In (Tama)
        // Only run if it's a weekday or enabled weekend
        $isWorkDay = true;
        if ($dayOfWeek == 6 && $settings['sabadu'] == 0) $isWorkDay = false;
        if ($dayOfWeek == 7 && $settings['domingu'] == 0) $isWorkDay = false;

        if ($isWorkDay && $now > $settings['tama_remata']) {
            $employees = $this->ApplicationModel->getFunsionariu();
            foreach ($employees as $e) {
                // Check if already has a record for today
                $prezensa = $this->db->table('prezensa')->where(['funsionariu_id' => $e['id'], 'data_prezensa' => $ohin])->get()->getRowArray();
                if (!$prezensa) {
                    // Check if employee has an approved license covering today
                    $lisensa = $this->ApplicationModel->getLisensa(funsionariu_id: $e['id'], estadu: 'Aprovadu');
                    $isOnLeave = false;
                    foreach ($lisensa as $l) {
                        if ($ohin >= $l['data_hahu'] && $ohin <= $l['data_remata']) {
                            $isOnLeave = true;
                            break;
                        }
                    }
                    
                    if (!$isOnLeave) {
                        $this->ApplicationModel->saveData('prezensa', [
                            'funsionariu_id' => $e['id'],
                            'data_prezensa'  => $ohin,
                            'estadu_prezensa' => 'Falta',
                            'created_at'     => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }

        // 2. Handle missing Clock-Out (Sai)
        // Check for records from past dates OR today past checkout window that are missing a clock-out
        $incompleteBuilder = $this->db->table('prezensa')
            ->where('oras_sai', NULL)
            ->where('estadu_prezensa !=', 'Falta')
            ->where('estadu_prezensa !=', 'Lisensa');
        
        $incompleteBuilder->groupStart()
            ->where('data_prezensa <', $ohin)
            ->orGroupStart()
                ->where('data_prezensa', $ohin)
                ->where("CAST('$now' AS TIME) >", $settings['sai_remata'])
            ->groupEnd()
        ->groupEnd();

        $incomplete = $incompleteBuilder->get()->getResultArray();
        
        foreach ($incomplete as $p) {
            $this->ApplicationModel->updateData('prezensa', [
                'estadu_prezensa' => 'Falta',
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $p['id']]);
        }
    }

    protected function logAudit(string $action, ?string $entityType = null, $entityId = null, ?array $oldValues = null, ?array $newValues = null): void
    {
        $this->db->table('audit_logs')->insert([
            'actor_user_id' => session()->get('userID'),
            'actor_role'    => session()->get('role_name') ?? session()->get('role'),
            'action'        => $action,
            'entity_type'   => $entityType,
            'entity_id'     => $entityId !== null ? (string) $entityId : null,
            'old_values'    => $oldValues !== null ? json_encode($oldValues) : null,
            'new_values'    => $newValues !== null ? json_encode($newValues) : null,
            'ip_address'    => $this->request?->getIPAddress(),
            'user_agent'    => substr((string) $this->request?->getUserAgent(), 0, 255),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

}
