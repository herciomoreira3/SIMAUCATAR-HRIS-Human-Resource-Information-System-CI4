<?php

namespace App\Filters;

use App\Libraries\PerformanceContext;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class PerformanceTelemetry implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        PerformanceContext::start();
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $summary = PerformanceContext::summary();
        if ($summary === null) {
            return $response;
        }

        $response->setHeader('X-Request-ID', $summary['request_id']);
        $response->setHeader(
            'Server-Timing',
            sprintf(
                'app;dur=%.3f, db;dur=%.3f;desc="database", sql;desc="count"',
                $summary['duration_ms'],
                $summary['db_duration_ms'],
            ),
        );

        $payload = json_encode([
            'event'          => 'request_performance',
            'request_id'     => $summary['request_id'],
            'route'          => $this->routeName(),
            'method'         => $request->getMethod(),
            'status'         => $response->getStatusCode(),
            'duration_ms'    => $summary['duration_ms'],
            'db_duration_ms' => $summary['db_duration_ms'],
            'query_count'    => $summary['query_count'],
            'memory_peak_mb' => $summary['memory_peak_mb'],
        ], JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_SLASHES);

        // The payload contains only fixed field names and aggregate numbers.
        if ($payload !== false) {
            log_message('info', $payload);
        }

        PerformanceContext::reset();

        return $response;
    }

    private function routeName(): string
    {
        $matched = service('router')->getMatchedRoute();

        return is_array($matched) && isset($matched[0]) ? (string) $matched[0] : 'unmatched';
    }
}
