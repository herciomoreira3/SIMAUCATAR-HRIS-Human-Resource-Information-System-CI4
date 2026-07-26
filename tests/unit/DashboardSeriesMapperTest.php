<?php

use App\Repositories\DashboardRepository;
use CodeIgniter\Test\CIUnitTestCase;

final class DashboardSeriesMapperTest extends CIUnitTestCase
{
    public function testItZeroFillsAllFourStatusesAcrossAnInclusiveRange(): void
    {
        $start = new DateTimeImmutable('2026-07-01', new DateTimeZone('Asia/Dili'));
        $end = new DateTimeImmutable('2026-07-03', new DateTimeZone('Asia/Dili'));
        $mapped = DashboardRepository::mapAttendanceSeries([
            ['data_prezensa' => '2026-07-01', 'estadu_prezensa' => 'Prezente', 'total' => '2'],
            ['data_prezensa' => '2026-07-03', 'estadu_prezensa' => 'Loron Sorin', 'total' => '1'],
            ['data_prezensa' => '2026-07-02', 'estadu_prezensa' => 'Other', 'total' => '9'],
        ], $start, $end);

        $this->assertSame(['01 Jul', '02 Jul', '03 Jul'], $mapped['labels']);
        $this->assertSame([2, 0, 0], $mapped['series']['Prezente']);
        $this->assertSame([0, 0, 1], $mapped['series']['Loron Sorin']);
        $this->assertSame([0, 0, 0], $mapped['series']['Falta']);
        $this->assertSame([0, 0, 0], $mapped['series']['Lisensa']);
    }
}
