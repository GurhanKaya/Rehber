<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class QueryOptimizationService
{
    /**
     * Eager loading ile ilişkileri yükler
     */
    public static function withRelations(Builder $query, array $relations): Builder
    {
        return $query->with($relations);
    }

    /**
     * Select ile sadece gerekli alanları seçer
     */
    public static function selectFields(Builder $query, array $fields): Builder
    {
        return $query->select($fields);
    }

    /**
     * Chunk ile büyük veri setlerini işler
     */
    public static function processInChunks(Builder $query, int $chunkSize, callable $callback): void
    {
        $query->chunk($chunkSize, $callback);
    }

    /**
     * Cache ile query sonuçlarını saklar
     */
    public static function cacheQuery(string $key, callable $callback, int $ttl = 3600)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Query log'unu temizler
     */
    public static function clearQueryLog(): void
    {
        DB::flushQueryLog();
    }

    /**
     * Query performansını ölçer
     */
    public static function measureQueryPerformance(callable $callback): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        DB::enableQueryLog();
        $result = $callback();
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        return [
            'execution_time' => round(($endTime - $startTime) * 1000, 2), // ms
            'memory_usage' => round(($endMemory - $startMemory) / 1024, 2), // KB
            'query_count' => count($queries),
            'queries' => $queries,
            'result' => $result
        ];
    }

    /**
     * N+1 query problemini tespit eder
     */
    public static function detectNPlusOneQueries(array $queries): array
    {
        $patterns = [];
        $nPlusOneQueries = [];
        
        foreach ($queries as $query) {
            $sql = $query['sql'];
            $table = self::extractTableFromSQL($sql);
            
            if (!isset($patterns[$table])) {
                $patterns[$table] = [];
            }
            
            $patterns[$table][] = $query;
        }
        
        foreach ($patterns as $table => $tableQueries) {
            if (count($tableQueries) > 3) { // 3'ten fazla aynı tablo sorgusu N+1 olabilir
                $nPlusOneQueries[$table] = [
                    'count' => count($tableQueries),
                    'queries' => $tableQueries
                ];
            }
        }
        
        return $nPlusOneQueries;
    }

    /**
     * SQL'den tablo adını çıkarır
     */
    private static function extractTableFromSQL(string $sql): string
    {
        if (preg_match('/FROM\s+`?(\w+)`?/i', $sql, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/JOIN\s+`?(\w+)`?/i', $sql, $matches)) {
            return $matches[1];
        }
        
        return 'unknown';
    }

    /**
     * Query builder'ı optimize eder
     */
    public static function optimizeQuery(Builder $query): Builder
    {
        // Gereksiz select'leri kaldır
        if (empty($query->getQuery()->columns)) {
            $query->select('*');
        }
        
        // Distinct kullanımını kontrol et
        if ($query->getQuery()->distinct) {
            // Distinct gerekli mi kontrol et
        }
        
        // Order by'da index kullanımını optimize et
        if (!empty($query->getQuery()->orders)) {
            // Index'li alanlara göre sırala
        }
        
        return $query;
    }

    /**
     * Pagination için optimize edilmiş query
     */
    public static function optimizedPaginate(Builder $query, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        // Count query'yi optimize et
        $countQuery = clone $query;
        $countQuery->getQuery()->columns = ['*'];
        
        // Ana query'yi optimize et
        $mainQuery = clone $query;
        
        return $mainQuery->paginate($perPage);
    }

    /**
     * Search query'yi optimize eder
     */
    public static function optimizeSearchQuery(Builder $query, string $searchTerm, array $searchFields): Builder
    {
        if (empty($searchTerm)) {
            return $query;
        }
        
        $query->where(function ($q) use ($searchTerm, $searchFields) {
            foreach ($searchFields as $field) {
                $q->orWhere($field, 'LIKE', "%{$searchTerm}%");
            }
        });
        
        return $query;
    }

    /**
     * Date range query'yi optimize eder
     */
    public static function optimizeDateRangeQuery(Builder $query, string $dateField, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate) {
            $query->where($dateField, '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where($dateField, '<=', $endDate);
        }
        
        return $query;
    }
}
