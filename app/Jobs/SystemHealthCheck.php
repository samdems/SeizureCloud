<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SystemHealthCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $timestamp = now();
        $checks = [];

        // 1. Database Connection Check
        try {
            DB::connection()->getPdo();
            $dbLatency = $this->measureDatabaseLatency();
            $checks['database'] = [
                'status' => 'healthy',
                'message' => 'Database connection successful',
                'latency_ms' => $dbLatency,
                'checked_at' => $timestamp
            ];
        } catch (\Exception $e) {
            $checks['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage(),
                'latency_ms' => null,
                'checked_at' => $timestamp
            ];
        }

        // 2. Queue System Check
        try {
            $queueStatus = $this->checkQueueStatus();
            $checks['queue'] = [
                'status' => $queueStatus['status'],
                'message' => $queueStatus['message'],
                'pending_jobs' => $queueStatus['pending_jobs'],
                'failed_jobs' => $queueStatus['failed_jobs'],
                'checked_at' => $timestamp
            ];
        } catch (\Exception $e) {
            $checks['queue'] = [
                'status' => 'error',
                'message' => 'Queue check failed: ' . $e->getMessage(),
                'pending_jobs' => null,
                'failed_jobs' => null,
                'checked_at' => $timestamp
            ];
        }

        // 3. Cache System Check
        try {
            $cacheKey = 'health_check_' . time();
            $testValue = 'test_' . uniqid();

            Cache::put($cacheKey, $testValue, 60);
            $retrieved = Cache::get($cacheKey);
            Cache::forget($cacheKey);

            if ($retrieved === $testValue) {
                $checks['cache'] = [
                    'status' => 'healthy',
                    'message' => 'Cache system working properly',
                    'driver' => config('cache.default'),
                    'checked_at' => $timestamp
                ];
            } else {
                $checks['cache'] = [
                    'status' => 'warning',
                    'message' => 'Cache retrieval mismatch',
                    'driver' => config('cache.default'),
                    'checked_at' => $timestamp
                ];
            }
        } catch (\Exception $e) {
            $checks['cache'] = [
                'status' => 'error',
                'message' => 'Cache check failed: ' . $e->getMessage(),
                'driver' => config('cache.default'),
                'checked_at' => $timestamp
            ];
        }

        // 4. Storage Check
        try {
            $storageStatus = $this->checkStorageHealth();
            $checks['storage'] = [
                'status' => $storageStatus['status'],
                'message' => $storageStatus['message'],
                'disk_usage' => $storageStatus['disk_usage'],
                'checked_at' => $timestamp
            ];
        } catch (\Exception $e) {
            $checks['storage'] = [
                'status' => 'error',
                'message' => 'Storage check failed: ' . $e->getMessage(),
                'disk_usage' => null,
                'checked_at' => $timestamp
            ];
        }

        // 5. Memory Usage Check
        $checks['memory'] = [
            'status' => $this->getMemoryStatus(),
            'message' => $this->getMemoryMessage(),
            'usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'limit' => ini_get('memory_limit'),
            'checked_at' => $timestamp
        ];

        // 6. Scheduler Check (verify last run)
        $checks['scheduler'] = $this->checkSchedulerHealth();

        // Store results in cache
        Cache::put('system_health_status', $checks, now()->addMinutes(10));

        // Log critical issues
        foreach ($checks as $service => $check) {
            if ($check['status'] === 'error') {
                Log::error("System Health Check: {$service} is down", $check);
            }
        }
    }

    /**
     * Measure database latency
     */
    private function measureDatabaseLatency(): float
    {
        $start = microtime(true);
        DB::select('SELECT 1');
        $end = microtime(true);

        return round(($end - $start) * 1000, 2); // Convert to milliseconds
    }

    /**
     * Check queue status
     */
    private function checkQueueStatus(): array
    {
        try {
            // Count pending jobs
            $pendingJobs = DB::table('jobs')->count();

            // Count failed jobs
            $failedJobs = DB::table('failed_jobs')->count();

            // Determine status
            if ($failedJobs > 10) {
                $status = 'warning';
                $message = "High number of failed jobs: {$failedJobs}";
            } elseif ($pendingJobs > 100) {
                $status = 'warning';
                $message = "High number of pending jobs: {$pendingJobs}";
            } else {
                $status = 'healthy';
                $message = 'Queue system operating normally';
            }

            return [
                'status' => $status,
                'message' => $message,
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Unable to check queue status',
                'pending_jobs' => null,
                'failed_jobs' => null
            ];
        }
    }

    /**
     * Check storage health
     */
    private function checkStorageHealth(): array
    {
        try {
            $testFile = 'health_check_' . time() . '.txt';
            $testContent = 'Health check test file';

            // Try to write and read a file
            Storage::put($testFile, $testContent);
            $retrieved = Storage::get($testFile);
            Storage::delete($testFile);

            if ($retrieved === $testContent) {
                return [
                    'status' => 'healthy',
                    'message' => 'Storage read/write operations successful',
                    'disk_usage' => $this->getDiskUsage()
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Storage read/write test failed',
                    'disk_usage' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Storage operation failed: ' . $e->getMessage(),
                'disk_usage' => null
            ];
        }
    }

    /**
     * Get disk usage information
     */
    private function getDiskUsage(): ?array
    {
        try {
            $storagePath = storage_path();
            if (function_exists('disk_free_space') && function_exists('disk_total_space')) {
                $free = disk_free_space($storagePath);
                $total = disk_total_space($storagePath);
                $used = $total - $free;
                $usagePercent = round(($used / $total) * 100, 2);

                return [
                    'free_gb' => round($free / 1024 / 1024 / 1024, 2),
                    'total_gb' => round($total / 1024 / 1024 / 1024, 2),
                    'usage_percent' => $usagePercent
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Could not get disk usage: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get memory status based on usage
     */
    private function getMemoryStatus(): string
    {
        $usageMB = memory_get_usage(true) / 1024 / 1024;
        $limit = ini_get('memory_limit');

        if ($limit === '-1') {
            return 'healthy'; // No limit
        }

        $limitMB = $this->convertToMB($limit);
        if ($limitMB > 0) {
            $usagePercent = ($usageMB / $limitMB) * 100;

            if ($usagePercent > 90) {
                return 'error';
            } elseif ($usagePercent > 75) {
                return 'warning';
            }
        }

        return 'healthy';
    }

    /**
     * Get memory status message
     */
    private function getMemoryMessage(): string
    {
        $usageMB = round(memory_get_usage(true) / 1024 / 1024, 2);
        $limit = ini_get('memory_limit');

        if ($limit === '-1') {
            return "Memory usage: {$usageMB} MB (no limit)";
        }

        $limitMB = $this->convertToMB($limit);
        if ($limitMB > 0) {
            $usagePercent = round(($usageMB / $limitMB) * 100, 2);
            return "Memory usage: {$usageMB} MB / {$limitMB} MB ({$usagePercent}%)";
        }

        return "Memory usage: {$usageMB} MB";
    }

    /**
     * Convert memory limit string to MB
     */
    private function convertToMB(string $limit): float
    {
        $limit = trim($limit);
        $last = strtolower(substr($limit, -1));
        $value = (float) substr($limit, 0, -1);

        switch ($last) {
            case 'g':
                return $value * 1024;
            case 'm':
                return $value;
            case 'k':
                return $value / 1024;
            default:
                return $value / 1024 / 1024; // Bytes to MB
        }
    }

    /**
     * Check scheduler health
     */
    private function checkSchedulerHealth(): array
    {
        $lastRun = Cache::get('scheduler_last_run');
        $now = Carbon::now();

        if (!$lastRun) {
            return [
                'status' => 'warning',
                'message' => 'No scheduler run detected',
                'last_run' => null,
                'checked_at' => $now
            ];
        }

        $lastRunCarbon = Carbon::parse($lastRun);
        $minutesSinceLastRun = $now->diffInMinutes($lastRunCarbon);

        if ($minutesSinceLastRun > 5) {
            return [
                'status' => 'warning',
                'message' => "Scheduler hasn't run for {$minutesSinceLastRun} minutes",
                'last_run' => $lastRun,
                'checked_at' => $now
            ];
        }

        return [
            'status' => 'healthy',
            'message' => 'Scheduler running normally',
            'last_run' => $lastRun,
            'checked_at' => $now
        ];
    }
}
