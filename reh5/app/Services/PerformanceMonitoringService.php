<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PerformanceMonitoringService
{
    /**
     * Request performansını ölçer
     */
    public static function measureRequestPerformance(callable $callback): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        try {
            $result = $callback();
            $success = true;
        } catch (\Exception $e) {
            $result = null;
            $success = false;
            Log::error('Request failed: ' . $e->getMessage());
        }
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $metrics = [
            'execution_time' => round(($endTime - $startTime) * 1000, 2), // ms
            'memory_usage' => round(($endMemory - $startMemory) / 1024, 2), // KB
            'peak_memory' => round(memory_get_peak_usage() / 1024, 2), // KB
            'success' => $success,
            'timestamp' => now()->toISOString()
        ];
        
        // Metrics'i cache'e kaydet
        self::storeMetrics('request', $metrics);
        
        return $metrics;
    }

    /**
     * Database performansını ölçer
     */
    public static function measureDatabasePerformance(callable $callback): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Query log'u etkinleştir
        DB::enableQueryLog();
        
        try {
            $result = $callback();
            $success = true;
        } catch (\Exception $e) {
            $result = null;
            $success = false;
            Log::error('Database operation failed: ' . $e->getMessage());
        }
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $metrics = [
            'execution_time' => round(($endTime - $startTime) * 1000, 2), // ms
            'memory_usage' => round(($endMemory - $startMemory) / 1024, 2), // KB
            'query_count' => count($queries),
            'queries' => $queries,
            'success' => $success,
            'timestamp' => now()->toISOString()
        ];
        
        // Metrics'i cache'e kaydet
        self::storeMetrics('database', $metrics);
        
        return $metrics;
    }

    /**
     * Cache performansını ölçer
     */
    public static function measureCachePerformance(callable $callback): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        try {
            $result = $callback();
            $success = true;
        } catch (\Exception $e) {
            $result = null;
            $success = false;
            Log::error('Cache operation failed: ' . $e->getMessage());
        }
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $metrics = [
            'execution_time' => round(($endTime - $startTime) * 1000, 2), // ms
            'memory_usage' => round(($endMemory - $startMemory) / 1024, 2), // KB
            'success' => $success,
            'timestamp' => now()->toISOString()
        ];
        
        // Metrics'i cache'e kaydet
        self::storeMetrics('cache', $metrics);
        
        return $metrics;
    }

    /**
     * Metrics'i cache'e kaydeder
     */
    private static function storeMetrics(string $type, array $metrics): void
    {
        $key = "performance:{$type}:" . now()->format('Y-m-d-H');
        
        try {
            $existingMetrics = Cache::get($key, []);
            $existingMetrics[] = $metrics;
            
            // Son 100 metric'i tut
            if (count($existingMetrics) > 100) {
                $existingMetrics = array_slice($existingMetrics, -100);
            }
            
            Cache::put($key, $existingMetrics, 3600); // 1 saat
        } catch (\Exception $e) {
            Log::warning("Failed to store performance metrics: " . $e->getMessage());
        }
    }

    /**
     * Performans istatistiklerini getirir
     */
    public static function getPerformanceStats(string $type = null, string $period = '1h'): array
    {
        $stats = [];
        
        try {
            if ($type) {
                $stats = self::getTypeStats($type, $period);
            } else {
                $stats = [
                    'request' => self::getTypeStats('request', $period),
                    'database' => self::getTypeStats('database', $period),
                    'cache' => self::getTypeStats('cache', $period)
                ];
            }
        } catch (\Exception $e) {
            Log::warning("Failed to get performance stats: " . $e->getMessage());
        }
        
        return $stats;
    }

    /**
     * Belirli tip için istatistikleri getirir
     */
    private static function getTypeStats(string $type, string $period): array
    {
        $stats = [
            'total_requests' => 0,
            'successful_requests' => 0,
            'failed_requests' => 0,
            'avg_execution_time' => 0,
            'avg_memory_usage' => 0,
            'max_execution_time' => 0,
            'max_memory_usage' => 0,
            'min_execution_time' => PHP_FLOAT_MAX,
            'min_memory_usage' => PHP_FLOAT_MAX
        ];
        
        try {
            $metrics = self::getMetricsForPeriod($type, $period);
            
            if (!empty($metrics)) {
                $executionTimes = array_column($metrics, 'execution_time');
                $memoryUsages = array_column($metrics, 'memory_usage');
                $successes = array_column($metrics, 'success');
                
                $stats['total_requests'] = count($metrics);
                $stats['successful_requests'] = count(array_filter($successes));
                $stats['failed_requests'] = count(array_filter($successes, fn($s) => !$s));
                $stats['avg_execution_time'] = round(array_sum($executionTimes) / count($executionTimes), 2);
                $stats['avg_memory_usage'] = round(array_sum($memoryUsages) / count($memoryUsages), 2);
                $stats['max_execution_time'] = max($executionTimes);
                $stats['max_memory_usage'] = max($memoryUsages);
                $stats['min_execution_time'] = min($executionTimes);
                $stats['min_memory_usage'] = min($memoryUsages);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to get type stats: " . $e->getMessage());
        }
        
        return $stats;
    }

    /**
     * Belirli periyot için metrics'leri getirir
     */
    private static function getMetricsForPeriod(string $type, string $period): array
    {
        $metrics = [];
        
        try {
            $hours = match($period) {
                '1h' => 1,
                '6h' => 6,
                '24h' => 24,
                '7d' => 168,
                default => 1
            };
            
            for ($i = 0; $i < $hours; $i++) {
                $key = "performance:{$type}:" . now()->subHours($i)->format('Y-m-d-H');
                $hourMetrics = Cache::get($key, []);
                $metrics = array_merge($metrics, $hourMetrics);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to get metrics for period: " . $e->getMessage());
        }
        
        return $metrics;
    }

    /**
     * Performance alert'leri kontrol eder
     */
    public static function checkPerformanceAlerts(): array
    {
        $alerts = [];
        
        try {
            $stats = self::getPerformanceStats();
            
            // Execution time alert'i
            if ($stats['request']['avg_execution_time'] > 1000) { // 1 saniye
                $alerts[] = [
                    'type' => 'warning',
                    'message' => 'Average request execution time is high: ' . $stats['request']['avg_execution_time'] . 'ms',
                    'metric' => 'execution_time',
                    'value' => $stats['request']['avg_execution_time'],
                    'threshold' => 1000
                ];
            }
            
            // Memory usage alert'i
            if ($stats['request']['avg_memory_usage'] > 10240) { // 10MB
                $alerts[] = [
                    'type' => 'warning',
                    'message' => 'Average memory usage is high: ' . $stats['request']['avg_memory_usage'] . 'KB',
                    'metric' => 'memory_usage',
                    'value' => $stats['request']['avg_memory_usage'],
                    'threshold' => 10240
                ];
            }
            
            // Failed requests alert'i
            $failureRate = $stats['request']['total_requests'] > 0 
                ? ($stats['request']['failed_requests'] / $stats['request']['total_requests']) * 100 
                : 0;
                
            if ($failureRate > 5) { // %5
                $alerts[] = [
                    'type' => 'warning',
                    'message' => 'High failure rate: ' . round($failureRate, 2) . '%',
                    'metric' => 'failure_rate',
                    'value' => $failureRate,
                    'threshold' => 5
                ];
            }
            
        } catch (\Exception $e) {
            Log::warning("Failed to check performance alerts: " . $e->getMessage());
        }
        
        return $alerts;
    }

    /**
     * Performance report'u oluşturur
     */
    public static function generatePerformanceReport(string $period = '24h'): array
    {
        $report = [
            'period' => $period,
            'generated_at' => now()->toISOString(),
            'summary' => [],
            'details' => [],
            'alerts' => [],
            'recommendations' => []
        ];
        
        try {
            $stats = self::getPerformanceStats(null, $period);
            $alerts = self::checkPerformanceAlerts();
            
            $report['summary'] = $stats;
            $report['alerts'] = $alerts;
            $report['recommendations'] = self::generateRecommendations($stats, $alerts);
            
        } catch (\Exception $e) {
            Log::warning("Failed to generate performance report: " . $e->getMessage());
        }
        
        return $report;
    }

    /**
     * Önerileri oluşturur
     */
    private static function generateRecommendations(array $stats, array $alerts): array
    {
        $recommendations = [];
        
        // Execution time önerileri
        if ($stats['request']['avg_execution_time'] > 500) {
            $recommendations[] = [
                'type' => 'performance',
                'title' => 'Optimize Database Queries',
                'description' => 'Consider adding database indexes or optimizing slow queries',
                'priority' => 'high'
            ];
        }
        
        // Memory usage önerileri
        if ($stats['request']['avg_memory_usage'] > 5120) {
            $recommendations[] = [
                'type' => 'memory',
                'title' => 'Reduce Memory Usage',
                'description' => 'Consider implementing pagination or lazy loading for large datasets',
                'priority' => 'medium'
            ];
        }
        
        // Cache önerileri
        if ($stats['cache']['total_requests'] > 0 && $stats['cache']['avg_execution_time'] > 10) {
            $recommendations[] = [
                'type' => 'cache',
                'title' => 'Improve Cache Strategy',
                'description' => 'Consider implementing more aggressive caching or using Redis',
                'priority' => 'medium'
            ];
        }
        
        return $recommendations;
    }
}
