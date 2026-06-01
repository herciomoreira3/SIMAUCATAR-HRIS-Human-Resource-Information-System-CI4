<?php

use App\Models\ApplicationModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class HrisImplementationTest extends CIUnitTestCase
{
    public function testLeaveDayCounterIsInclusive(): void
    {
        $model = new ApplicationModel();

        $this->assertSame(1, $model->countLeaveDays('2026-06-01', '2026-06-01'));
        $this->assertSame(3, $model->countLeaveDays('2026-06-01', '2026-06-03'));
        $this->assertSame(2, $model->countLeaveDays('2025-12-31', '2026-01-02', 2026));
    }

    public function testDangerousRoutesUseSafeHttpMethods(): void
    {
        $routes = file_get_contents(APPPATH . 'Config/Routes.php');

        $this->assertStringContainsString("\$routes->post('logout'", $routes);
        $this->assertStringNotContainsString("\$routes->get('logout'", $routes);
        $this->assertStringContainsString("\$routes->delete('departamentu/delete/(:num)'", $routes);
        $this->assertStringContainsString("\$routes->post('sansaun/retira/(:num)'", $routes);
    }

    public function testOperationalBacklogRoutesAreWired(): void
    {
        $routes = file_get_contents(APPPATH . 'Config/Routes.php');

        foreach ([
            "'audit', 'Administrador::audit'",
            "'maintenance', 'Administrador::maintenance'",
            "'feriadu', 'Administrador::feriadu'",
            "'lisensa/balansu', 'Administrador::leaveBalance'",
            "'documentu/category', 'Administrador::createDocumentCategory'",
            "'funsionariu/import', 'Administrador::importFunsionariu'",
            "'funsionariu/reset-password/(:num)', 'Administrador::resetFunsionariuPassword/$1'",
            "'salariu/periodu/lock', 'Administrador::lockPayrollPeriod'",
            "'avizu', 'Administrador::avizu'",
        ] as $route) {
            $this->assertStringContainsString($route, $routes);
        }
    }

    public function testOperationalViewsExist(): void
    {
        foreach ([
            APPPATH . 'Views/pages/administrador/audit.php',
            APPPATH . 'Views/pages/administrador/feriadu.php',
            APPPATH . 'Views/pages/administrador/leave_balance.php',
            APPPATH . 'Views/pages/administrador/maintenance.php',
            APPPATH . 'Views/pages/administrador/documentu.php',
            APPPATH . 'Views/pages/administrador/avizu.php',
            APPPATH . 'Views/pages/funsionariu/dokumentu.php',
        ] as $file) {
            $this->assertFileExists($file);
        }
    }

    public function testStandaloneNotificationModuleIsMergedIntoAnunsiu(): void
    {
        $routes = file_get_contents(APPPATH . 'Config/Routes.php');
        $header = file_get_contents(APPPATH . 'Views/layouts/header.php');
        $baseController = file_get_contents(APPPATH . 'Controllers/BaseController.php');

        $this->assertStringNotContainsString('Notifikasaun::', $routes);
        $this->assertStringNotContainsString('notifikasaun', $routes);
        $this->assertStringNotContainsString('notifikasaun', $header);
        $this->assertStringNotContainsString('notifications', $baseController);
        $this->assertStringContainsString("base_url('administrador/avizu')", $header);
        $this->assertStringContainsString('Anunsiu', $header);
        $this->assertFileDoesNotExist(APPPATH . 'Controllers/Notifikasaun.php');
        $this->assertFileDoesNotExist(APPPATH . 'Views/pages/commons/notifikasaun.php');
    }

    public function testDashboardChartsUseLocalApexAndTrendData(): void
    {
        $adminController = file_get_contents(APPPATH . 'Controllers/Administrador.php');
        $employeeController = file_get_contents(APPPATH . 'Controllers/Funsionariu.php');
        $adminDashboard = file_get_contents(APPPATH . 'Views/pages/administrador/dashboard.php');
        $employeeDashboard = file_get_contents(APPPATH . 'Views/pages/funsionariu/dashboard.php');
        $appJs = file_get_contents(ROOTPATH . 'public/assets/js/app.js');

        $this->assertStringContainsString('window.ApexCharts', $appJs);
        $this->assertStringContainsString('initNotyfSafely', $appJs);
        $this->assertStringContainsString('chart_tardi', $adminController);
        $this->assertStringContainsString('chart_lisensa', $adminController);
        $this->assertStringContainsString('trend_prezente', $employeeController);
        $this->assertStringContainsString('trend_lisensa', $employeeController);
        $this->assertStringContainsString('#chart-prezensa-trend', $adminDashboard);
        $this->assertStringContainsString('#chart-personal-trend', $employeeDashboard);
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/apexcharts', $adminDashboard);
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/apexcharts', $employeeDashboard);
    }

    public function testLeavePayrollDocumentAndHolidayWorkflowGuardsExist(): void
    {
        $admin = file_get_contents(APPPATH . 'Controllers/Administrador.php');
        $employee = file_get_contents(APPPATH . 'Controllers/Funsionariu.php');
        $command = file_get_contents(APPPATH . 'Commands/MarkAbsent.php');

        $this->assertStringContainsString('recalculateLeaveBalance', $admin);
        $this->assertStringContainsString('remaining_days', $employee);
        $this->assertStringContainsString('payroll_periods', $admin);
        $this->assertStringContainsString("status' => 'Locked'", $admin);
        $this->assertStringContainsString('document_categories', $admin);
        $this->assertStringContainsString('holidays', $employee);
        $this->assertStringContainsString('holidays', $command);
    }

    public function testAppSourceDoesNotContainKnownMojibakeMarkers(): void
    {
        $markers = ['Ã', 'Â', '�', 'val¢', 'valór'];
        $violations = [];

        foreach ($this->appSourceFiles() as $file) {
            $content = file_get_contents($file);
            foreach ($markers as $marker) {
                if (str_contains($content, $marker)) {
                    $violations[] = str_replace(ROOTPATH, '', $file) . ' contains ' . $marker;
                }
            }
        }

        $this->assertSame([], $violations);
    }

    public function testAllPostFormsUseCsrfField(): void
    {
        $missing = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(APPPATH . 'Views'));

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $content = file_get_contents($file->getPathname());
            if (!preg_match_all('/<form\b[^>]*method=["\']post["\'][^>]*>/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            foreach ($matches[0] as $match) {
                $start = $match[1];
                $end = strpos($content, '</form>', $start);
                $form = substr($content, $start, $end === false ? null : $end - $start);
                if (!str_contains($form, 'csrf_field()')) {
                    $missing[] = $file->getFilename() . ':' . $start;
                }
            }
        }

        $this->assertSame([], $missing);
    }

    /**
     * @return list<string>
     */
    private function appSourceFiles(): array
    {
        $files = [];

        foreach ([APPPATH, ROOTPATH . 'public'] as $root) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
            foreach ($iterator as $file) {
                if (!$file->isFile()) {
                    continue;
                }

                if (!in_array($file->getExtension(), ['php', 'js', 'css'], true)) {
                    continue;
                }

                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
