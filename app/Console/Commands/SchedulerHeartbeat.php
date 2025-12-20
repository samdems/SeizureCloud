<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SchedulerHeartbeat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:heartbeat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update scheduler last run timestamp for health monitoring';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Update the scheduler heartbeat timestamp
        Cache::put(
            'scheduler_last_run',
            now(),
            now()->addMinutes(10)
        );

        $this->info('Scheduler heartbeat updated: ' . now()->format('Y-m-d H:i:s'));

        return 0;
    }
}
