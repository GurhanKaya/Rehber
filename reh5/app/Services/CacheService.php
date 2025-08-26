<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Cache key'leri oluşturur
     */
    public static function generateKey(string $prefix, array $params = []): string
    {
        $key = $prefix;
        
        if (!empty($params)) {
            $key .= ':' . md5(serialize($params));
        }
        
        return $key;
    }

    /**
     * Model cache'i
     */
    public static function rememberModel(string $key, callable $callback, int $ttl = 3600)
    {
        return Cache::remember($key, $ttl, function () use ($callback) {
            try {
                return $callback();
            } catch (\Exception $e) {
                Log::error('Cache callback error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Collection cache'i
     */
    public static function rememberCollection(string $key, callable $callback, int $ttl = 1800)
    {
        return Cache::remember($key, $ttl, function () use ($callback) {
            try {
                return $callback();
            } catch (\Exception $e) {
                Log::error('Cache callback error: ' . $e->getMessage());
                return collect();
            }
        });
    }

    /**
     * Pagination cache'i
     */
    public static function rememberPaginated(string $key, callable $callback, int $ttl = 900)
    {
        return Cache::remember($key, $ttl, function () use ($callback) {
            try {
                return $callback();
            } catch (\Exception $e) {
                Log::error('Cache callback error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Cache'i temizler
     */
    public static function clearCache(string $pattern = null): void
    {
        if ($pattern) {
            // Pattern'e göre cache temizle
            $keys = Cache::get($pattern);
            if ($keys) {
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
            }
        } else {
            // Tüm cache'i temizle
            Cache::flush();
        }
    }

    /**
     * Cache tag'leri ile cache'i yönetir
     */
    public static function rememberWithTags(array $tags, string $key, callable $callback, int $ttl = 3600)
    {
        return Cache::tags($tags)->remember($key, $ttl, function () use ($callback) {
            try {
                return $callback();
            } catch (\Exception $e) {
                Log::error('Cache callback error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Cache tag'lerini temizler
     */
    public static function clearTags(array $tags): void
    {
        Cache::tags($tags)->flush();
    }

    /**
     * Cache hit/miss oranını ölçer
     */
    public static function getCacheStats(): array
    {
        $stats = [
            'hits' => 0,
            'misses' => 0,
            'keys' => 0,
            'memory' => 0
        ];

        try {
            // Cache store'a göre istatistikleri al
            $store = Cache::getStore();
            
            // getStats method'u yoksa varsayılan değerleri kullan
            if (method_exists($store, 'getStats')) {
                try {
                    $storeStats = $store->getStats();
                    $stats = array_merge($stats, $storeStats);
                } catch (\Exception $e) {
                    // getStats method'u var ama hata veriyor
                    Log::warning('getStats method error: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::warning('Cache stats not available: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Cache warmup
     */
    public static function warmupCache(): void
    {
        try {
            // Sık kullanılan verileri cache'e yükle
            self::warmupUserCache();
            self::warmupTaskCache();
            self::warmupAppointmentCache();
            
            Log::info('Cache warmup completed');
        } catch (\Exception $e) {
            Log::error('Cache warmup failed: ' . $e->getMessage());
        }
    }

    /**
     * User cache warmup
     */
    private static function warmupUserCache(): void
    {
        // Admin kullanıcıları cache'le
        Cache::remember('users:admins', 3600, function () {
            return \App\Models\User::where('role', 'admin')->get(['id', 'name', 'email']);
        });

        // Personel kullanıcıları cache'le
        Cache::remember('users:personnel', 3600, function () {
            return \App\Models\User::where('role', 'personel')->get(['id', 'name', 'email']);
        });
    }

    /**
     * Task cache warmup
     */
    private static function warmupTaskCache(): void
    {
        // Task durumları cache'le
        Cache::remember('tasks:statuses', 1800, function () {
            return \App\Models\Task::select('status')
                ->distinct()
                ->pluck('status')
                ->toArray();
        });

        // Task tipleri cache'le
        Cache::remember('tasks:types', 1800, function () {
            return \App\Models\Task::select('type')
                ->distinct()
                ->pluck('type')
                ->toArray();
        });
    }

    /**
     * Appointment cache warmup
     */
    private static function warmupAppointmentCache(): void
    {
        // Bugünkü randevular cache'le
        Cache::remember('appointments:today', 900, function () {
            return \App\Models\Appointment::whereDate('appointment_date', today())
                ->with(['user', 'appointmentSlot'])
                ->get();
        });
    }

    /**
     * Cache TTL'leri
     */
    public static function getTTL(string $type): int
    {
        $ttls = [
            'user' => 3600,        // 1 saat
            'task' => 1800,        // 30 dakika
            'appointment' => 900,  // 15 dakika
            'statistics' => 7200,  // 2 saat
            'settings' => 86400,   // 24 saat
            'default' => 1800      // 30 dakika
        ];

        return $ttls[$type] ?? $ttls['default'];
    }

    /**
     * Cache key patterns
     */
    public static function getKeyPatterns(): array
    {
        return [
            'users' => [
                'all' => 'users:all',
                'by_role' => 'users:role:{role}',
                'by_id' => 'users:id:{id}',
                'search' => 'users:search:{query}'
            ],
            'tasks' => [
                'all' => 'tasks:all',
                'by_status' => 'tasks:status:{status}',
                'by_user' => 'tasks:user:{user_id}',
                'by_type' => 'tasks:type:{type}',
                'search' => 'tasks:search:{query}'
            ],
            'appointments' => [
                'all' => 'appointments:all',
                'by_date' => 'appointments:date:{date}',
                'by_user' => 'appointments:user:{user_id}',
                'by_status' => 'appointments:status:{status}'
            ]
        ];
    }
}
