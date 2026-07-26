<?php

namespace App\Libraries;

/**
 * Per-request performance counters. This intentionally stores aggregate timing
 * only: SQL statements, bindings, request payloads, and identity data never
 * enter the context or the structured log.
 */
final class PerformanceContext
{
    private static bool $enabled = false;

    private static ?string $requestId = null;

    private static ?int $startedAtNs = null;

    private static float $databaseDurationMs = 0.0;

    private static int $queryCount = 0;

    public static function isEnabled(): bool
    {
        $value = env('PERF_TELEMETRY_ENABLED', false);

        return filter_var($value, FILTER_VALIDATE_BOOL);
    }

    public static function start(): void
    {
        if (! self::isEnabled()) {
            self::reset();

            return;
        }

        self::$enabled            = true;
        self::$requestId          = self::uuidV4();
        self::$startedAtNs        = hrtime(true);
        self::$databaseDurationMs = 0.0;
        self::$queryCount         = 0;
    }

    public static function recordQueryDuration(float $seconds): void
    {
        if (! self::$enabled) {
            return;
        }

        self::$queryCount++;
        self::$databaseDurationMs += max(0.0, $seconds * 1000);
    }

    /**
     * @return array{request_id:string,duration_ms:float,db_duration_ms:float,query_count:int,memory_peak_mb:float}|null
     */
    public static function summary(): ?array
    {
        if (! self::$enabled || self::$requestId === null || self::$startedAtNs === null) {
            return null;
        }

        return [
            'request_id'     => self::$requestId,
            'duration_ms'    => round((hrtime(true) - self::$startedAtNs) / 1_000_000, 3),
            'db_duration_ms' => round(self::$databaseDurationMs, 3),
            'query_count'    => self::$queryCount,
            'memory_peak_mb' => round(memory_get_peak_usage(true) / 1_048_576, 3),
        ];
    }

    public static function reset(): void
    {
        self::$enabled            = false;
        self::$requestId          = null;
        self::$startedAtNs        = null;
        self::$databaseDurationMs = 0.0;
        self::$queryCount         = 0;
    }

    private static function uuidV4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
