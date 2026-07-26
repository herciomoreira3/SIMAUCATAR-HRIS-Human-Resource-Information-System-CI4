<?php

use CodeIgniter\Test\CIUnitTestCase;

/** @internal */
final class AuditPaginationTest extends CIUnitTestCase
{
    public function testAuditUsesBoundedServerSidePaginationAndAllowlistedSorting(): void
    {
        $controller = file_get_contents(APPPATH . 'Controllers/Administrador.php');

        $this->assertStringContainsString('in_array($perPage, [10, 25, 50, 100], true)', $controller);
        $this->assertStringContainsString("\$sorts = ['created_at' => 'audit_logs.created_at'];", $controller);
        $this->assertStringContainsString("'sort' => \$sorts[\$sortKey] ?? \$sorts['created_at']", $controller);
        $this->assertStringContainsString("->orderBy('audit_logs.id', \$pagination['direction'])", $controller);
        $this->assertStringContainsString("->limit(\$pagination['per_page'], \$pagination['offset'])", $controller);
        $this->assertStringNotContainsString('->limit(500)', $controller);
    }

    public function testAuditViewUsesServerPagerInsteadOfClientDataTable(): void
    {
        $view = file_get_contents(APPPATH . 'Views/pages/administrador/audit.php');

        $this->assertStringNotContainsString('datatable', $view);
        $this->assertStringContainsString("name=\"per_page\"", $view);
        $this->assertStringContainsString("\$pagination['pages']", $view);
        $this->assertStringContainsString('http_build_query($query)', $view);
    }
}
