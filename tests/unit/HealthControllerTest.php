<?php

use App\Controllers\Health;
use CodeIgniter\Test\CIUnitTestCase;

final class HealthControllerTest extends CIUnitTestCase
{
    public function testLiveReturnsNoContentWithoutAReadinessProbe(): void
    {
        $controller = new Health();
        $controller->initController(service('request'), service('response'), service('logger'));

        $response = $controller->live();

        $this->assertSame(204, $response->getStatusCode());
        $this->assertNull($response->getBody());
    }
}
