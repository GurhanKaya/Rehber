<?php

namespace App\Services;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class QueueService
{
    /**
     * Email gönderimi için queue'ya ekler
     */
    public static function queueEmail(string $to, string $subject, string $view, array $data = []): void
    {
        try {
            Mail::queue($view, $data, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
            
            Log::info("Email queued successfully: {$to} - {$subject}");
        } catch (\Exception $e) {
            Log::error("Failed to queue email: " . $e->getMessage());
        }
    }

    /**
     * Notification gönderimi için queue'ya ekler
     */
    public static function queueNotification($notifiable, $notification): void
    {
        try {
            $notifiable->notify($notification);
            Log::info("Notification queued successfully for: " . get_class($notifiable));
        } catch (\Exception $e) {
            Log::error("Failed to queue notification: " . $e->getMessage());
        }
    }

    /**
     * Job'ı queue'ya ekler
     */
    public static function dispatchJob($job, string $queue = 'default'): void
    {
        try {
            if ($queue !== 'default') {
                $job->onQueue($queue);
            }
            
            dispatch($job);
            Log::info("Job dispatched successfully: " . get_class($job));
        } catch (\Exception $e) {
            Log::error("Failed to dispatch job: " . $e->getMessage());
        }
    }

    /**
     * Delayed job'ı queue'ya ekler
     */
    public static function dispatchDelayedJob($job, int $delay, string $queue = 'default'): void
    {
        try {
            if ($queue !== 'default') {
                $job->onQueue($queue);
            }
            
            dispatch($job)->delay(now()->addSeconds($delay));
            Log::info("Delayed job dispatched successfully: " . get_class($job) . " - Delay: {$delay}s");
        } catch (\Exception $e) {
            Log::error("Failed to dispatch delayed job: " . $e->getMessage());
        }
    }

    /**
     * Queue durumunu kontrol eder
     */
    public static function getQueueStatus(): array
    {
        $status = [
            'default' => 0,
            'emails' => 0,
            'notifications' => 0,
            'tasks' => 0
        ];

        try {
            // Queue size'ları al (Redis veya Database queue için)
            if (config('queue.default') === 'redis') {
                $status['default'] = Queue::size('default');
                $status['emails'] = Queue::size('emails');
                $status['notifications'] = Queue::size('notifications');
                $status['tasks'] = Queue::size('tasks');
            } elseif (config('queue.default') === 'database') {
                // Database queue için
                $status['default'] = DB::table('jobs')->where('queue', 'default')->count();
                $status['emails'] = DB::table('jobs')->where('queue', 'emails')->count();
                $status['notifications'] = DB::table('jobs')->where('queue', 'notifications')->count();
                $status['tasks'] = DB::table('jobs')->where('queue', 'tasks')->count();
            }
        } catch (\Exception $e) {
            Log::warning("Queue status not available: " . $e->getMessage());
        }

        return $status;
    }

    /**
     * Failed job'ları listeler
     */
    public static function getFailedJobs(): array
    {
        try {
            if (config('queue.default') === 'database') {
                return DB::table('failed_jobs')
                    ->orderBy('failed_at', 'desc')
                    ->limit(50)
                    ->get()
                    ->toArray();
            }
        } catch (\Exception $e) {
            Log::warning("Failed jobs not available: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Failed job'ı retry eder
     */
    public static function retryFailedJob(int $jobId): bool
    {
        try {
            if (config('queue.default') === 'database') {
                $failedJob = DB::table('failed_jobs')->find($jobId);
                
                if ($failedJob) {
                    // Job'ı tekrar queue'ya ekle
                    DB::table('jobs')->insert([
                        'queue' => $failedJob->queue ?? 'default',
                        'payload' => $failedJob->payload,
                        'attempts' => 0,
                        'reserved_at' => null,
                        'available_at' => now()->timestamp,
                        'created_at' => now()->timestamp
                    ]);
                    
                    // Failed job'ı sil
                    DB::table('failed_jobs')->where('id', $jobId)->delete();
                    
                    Log::info("Failed job retried successfully: {$jobId}");
                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to retry job: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Queue'yu temizler
     */
    public static function clearQueue(string $queue = 'default'): bool
    {
        try {
            if (config('queue.default') === 'database') {
                DB::table('jobs')->where('queue', $queue)->delete();
                Log::info("Queue cleared successfully: {$queue}");
                return true;
            }
        } catch (\Exception $e) {
            Log::error("Failed to clear queue: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Queue worker'ları başlatır
     */
    public static function startWorkers(): void
    {
        try {
            // Queue worker'ları başlat
            $commands = [
                'queue:work --queue=default,emails,notifications,tasks --sleep=3 --tries=3',
                'queue:work --queue=emails --sleep=3 --tries=3',
                'queue:work --queue=notifications --sleep=3 --tries=3',
                'queue:work --queue=tasks --sleep=3 --tries=3'
            ];

            foreach ($commands as $command) {
                // Background'da çalıştır
                exec("php artisan {$command} > /dev/null 2>&1 &");
            }

            Log::info("Queue workers started successfully");
        } catch (\Exception $e) {
            Log::error("Failed to start queue workers: " . $e->getMessage());
        }
    }

    /**
     * Queue worker'ları durdurur
     */
    public static function stopWorkers(): void
    {
        try {
            // Queue worker process'lerini bul ve durdur
            exec("pkill -f 'queue:work'");
            Log::info("Queue workers stopped successfully");
        } catch (\Exception $e) {
            Log::error("Failed to stop queue workers: " . $e->getMessage());
        }
    }
}
