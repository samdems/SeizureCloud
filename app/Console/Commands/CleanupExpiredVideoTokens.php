<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VideoUploadService;

class CleanupExpiredVideoTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "video:cleanup-tokens";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Clean up expired video access tokens";

    protected VideoUploadService $videoUploadService;

    public function __construct(VideoUploadService $videoUploadService)
    {
        parent::__construct();
        $this->videoUploadService = $videoUploadService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Cleaning up expired video tokens...");

        $cleanedCount = $this->videoUploadService->cleanupExpiredTokens();

        if ($cleanedCount > 0) {
            $this->info("Cleaned up {$cleanedCount} expired video tokens.");
        } else {
            $this->info("No expired video tokens found.");
        }

        return Command::SUCCESS;
    }
}
