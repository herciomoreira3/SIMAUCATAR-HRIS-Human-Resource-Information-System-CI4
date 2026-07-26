<?php

use CodeIgniter\Test\CIUnitTestCase;

/** @internal */
final class RelatoriuPaginationTest extends CIUnitTestCase
{
    public function testReportsUseBoundedServerSidePaginationAndAllowlistedSorting(): void
    {
        $controller = file_get_contents(APPPATH . 'Controllers/Relatoriu.php');

        $this->assertStringContainsString('in_array($perPage, [10, 25, 50, 100], true)', $controller);
        $this->assertStringContainsString("'sort' => \$sorts[\$sortKey] ?? \$sorts[\$defaultSort]", $controller);
        $this->assertStringContainsString("'direction' => \$direction === 'desc' ? 'DESC'", $controller);
        $this->assertStringContainsString("'offset' => (\$page - 1) * \$perPage", $controller);
    }

    public function testSanctionReportUsesHalfOpenDateRange(): void
    {
        $model = file_get_contents(APPPATH . 'Models/RelatoriuModel.php');

        $this->assertStringContainsString("->where('sansaun.data_sansaun >=', \$start)", $model);
        $this->assertStringContainsString("->where('sansaun.data_sansaun <', \$end)", $model);
        $this->assertStringNotContainsString("MONTH(data_sansaun)", $model);
        $this->assertStringNotContainsString("YEAR(data_sansaun)", $model);
    }

    public function testReportTablesDoNotEnableClientSideDataTablesAndKeepExportsFull(): void
    {
        foreach (glob(APPPATH . 'Views/pages/administrador/relatoriu/*.php') as $view) {
            $this->assertStringNotContainsString('datatable', file_get_contents($view), basename($view));
        }

        $controller = file_get_contents(APPPATH . 'Controllers/Relatoriu.php');
        $this->assertStringContainsString('getRekapSansaun($filter[\'fulan\'], $filter[\'tinan\'], $filter[\'estadu\'], $filter[\'tipu_sansaun_id\'])', $controller);
        $this->assertStringContainsString('getRekapFunsionariu($filter[\'diresaun_id\'], $filter[\'pozisaun_id\'], $filter[\'kategoria_id\'], $filter[\'grau_id\'])', $controller);
    }
}
