<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SystemHealthCheck;

class RunHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'health:check {--sync : Run synchronously instead of queuing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run system health checks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running system health check...');

        if ($this->option('sync')) {
            // Run synchronously for immediate results
            $job = new SystemHealthCheck();
            $job->handle();
            $this->info('Health check completed synchronously.');
        } else {
            // Queue the job
            SystemHealthCheck::dispatch();
            $this->info('Health check job queued.');
        }

        return 0;
    }
}
