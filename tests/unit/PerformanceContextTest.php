<?php

use App\Libraries\PerformanceContext;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PerformanceContextTest extends CIUnitTestCase
{
    private mixed $previousTelemetryValue;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousTelemetryValue = $_ENV['PERF_TELEMETRY_ENABLED'] ?? null;
    }

    protected function tearDown(): void
    {
        PerformanceContext::reset();
        if ($this->previousTelemetryValue === null) {
            unset($_ENV['PERF_TELEMETRY_ENABLED']);
        } else {
            $_ENV['PERF_TELEMETRY_ENABLED'] = $this->previousTelemetryValue;
        }
        parent::tearDown();
    }

    public function testDisabledTelemetryKeepsNoRequestState(): void
    {
        $_ENV['PERF_TELEMETRY_ENABLED'] = 'false';

        PerformanceContext::start();
        PerformanceContext::recordQueryDuration(42.0);

        $this->assertNull(PerformanceContext::summary());
    }

    public function testSummaryContainsOnlyAggregateMeasurements(): void
    {
        $_ENV['PERF_TELEMETRY_ENABLED'] = 'true';

        PerformanceContext::start();
        PerformanceContext::recordQueryDuration(0.0125);
        $summary = PerformanceContext::summary();

        $this->assertNotNull($summary);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $summary['request_id'],
        );
        $this->assertSame(1, $summary['query_count']);
        $this->assertSame(12.5, $summary['db_duration_ms']);
        $this->assertSame(
            ['request_id', 'duration_ms', 'db_duration_ms', 'query_count', 'memory_peak_mb'],
            array_keys($summary),
        );
    }
}
