<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixStoragePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:fix-permissions 
                            {--force : Force permission changes even in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix storage and cache permissions for Laravel application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (app()->isProduction() && ! $this->option('force')) {
            $this->error('This command should not be run in production without --force flag.');

            return 1;
        }

        $this->info('🔧 Fixing storage permissions...');

        try {
            // Clear Laravel caches first
            $this->line('Clearing Laravel caches...');
            $this->call('view:clear');
            $this->call('config:clear');
            $this->call('cache:clear');

            // Fix directory permissions
            $storagePath = storage_path();
            $bootstrapCachePath = base_path('bootstrap/cache');

            $directories = [
                $storagePath,
                $storagePath.'/app',
                $storagePath.'/framework',
                $storagePath.'/framework/cache',
                $storagePath.'/framework/sessions',
                $storagePath.'/framework/testing',
                $storagePath.'/framework/views',
                $storagePath.'/logs',
                $bootstrapCachePath,
            ];

            $this->line('Setting directory permissions...');
            foreach ($directories as $directory) {
                if (file_exists($directory)) {
                    chmod($directory, 0775);
                    $this->line("✓ Fixed permissions for: {$directory}");
                }
            }

            // Fix file permissions recursively for key directories
            $this->line('Setting file permissions recursively...');
            $this->setPermissionsRecursively($storagePath.'/framework/views', 0664);
            $this->setPermissionsRecursively($storagePath.'/framework/cache', 0664);
            $this->setPermissionsRecursively($storagePath.'/logs', 0664);

            $this->newLine();
            $this->info('✅ Storage permissions fixed successfully!');

            $this->newLine();
            $this->line('💡 To prevent permission issues in the future:');
            $this->line('   • Always use: vendor/bin/sail artisan [command]');
            $this->line('   • Always use: vendor/bin/sail artisan test');
            $this->line('   • Avoid running Laravel commands directly on host');

            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to fix permissions: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Set permissions recursively for files in a directory
     */
    private function setPermissionsRecursively(string $path, int $permission): void
    {
        if (! file_exists($path)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                chmod($file->getPathname(), $permission);
            }
        }
    }
}
