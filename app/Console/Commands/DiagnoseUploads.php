<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DiagnoseUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnose:uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose file upload configuration and permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== File Upload Diagnostics ===');
        $this->newLine();

        // PHP Configuration
        $this->info('📋 PHP Configuration:');
        $this->table(
            ['Setting', 'Value', 'Status'],
            [
                [
                    'upload_max_filesize',
                    ini_get('upload_max_filesize'),
                    $this->getUploadSizeStatus(ini_get('upload_max_filesize'))
                ],
                [
                    'post_max_size',
                    ini_get('post_max_size'),
                    $this->getUploadSizeStatus(ini_get('post_max_size'))
                ],
                [
                    'max_execution_time',
                    ini_get('max_execution_time') . 's',
                    ini_get('max_execution_time') >= 120 ? '✅ OK' : '⚠️  Too Low'
                ],
                [
                    'max_input_time',
                    ini_get('max_input_time') . 's',
                    ini_get('max_input_time') >= 120 ? '✅ OK' : '⚠️  Too Low'
                ],
                [
                    'memory_limit',
                    ini_get('memory_limit'),
                    $this->getMemoryStatus(ini_get('memory_limit'))
                ],
                [
                    'file_uploads',
                    ini_get('file_uploads') ? 'Enabled' : 'Disabled',
                    ini_get('file_uploads') ? '✅ OK' : '❌ DISABLED'
                ],
            ]
        );
        $this->newLine();

        // Storage Paths
        $this->info('📁 Storage Paths:');
        $storagePaths = [
            'storage/app/private' => storage_path('app/private'),
            'storage/app/private/documents' => storage_path('app/private/documents'),
            'storage/app/livewire-tmp' => storage_path('app/livewire-tmp'),
            'storage/framework/cache' => storage_path('framework/cache'),
            'storage/framework/sessions' => storage_path('framework/sessions'),
            'storage/logs' => storage_path('logs'),
        ];

        foreach ($storagePaths as $label => $path) {
            $exists = File::exists($path);
            $writable = $exists && File::isWritable($path);
            $permissions = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';

            $status = !$exists ? '❌ Missing' : ($writable ? '✅ Writable' : '⚠️  Not Writable');

            $this->line(sprintf(
                '  %-40s [%s] %s (Perms: %s)',
                $label,
                $status,
                $path,
                $permissions
            ));
        }
        $this->newLine();

        // Create missing directories
        $missingDirs = [];
        foreach ($storagePaths as $label => $path) {
            if (!File::exists($path)) {
                $missingDirs[] = $path;
            }
        }

        if (!empty($missingDirs)) {
            if ($this->confirm('Create missing directories?', true)) {
                foreach ($missingDirs as $dir) {
                    try {
                        File::makeDirectory($dir, 0755, true);
                        $this->info("  ✅ Created: $dir");
                    } catch (\Exception $e) {
                        $this->error("  ❌ Failed to create $dir: " . $e->getMessage());
                    }
                }
                $this->newLine();
            }
        }

        // Disk Configuration
        $this->info('💾 Filesystem Disks:');
        $disks = ['private', 'local', 'public'];
        foreach ($disks as $disk) {
            try {
                $driver = config("filesystems.disks.$disk.driver");
                $root = config("filesystems.disks.$disk.root");
                $exists = File::exists($root);
                $writable = $exists && File::isWritable($root);

                $status = !$exists ? '❌ Root Missing' : ($writable ? '✅ OK' : '⚠️  Not Writable');

                $this->line(sprintf(
                    '  %-15s [%s] Driver: %-8s Root: %s',
                    $disk,
                    $status,
                    $driver,
                    $root
                ));
            } catch (\Exception $e) {
                $this->error("  ❌ $disk: " . $e->getMessage());
            }
        }
        $this->newLine();

        // Livewire Configuration
        $this->info('⚡ Livewire Configuration:');
        $livewireTmpPath = storage_path('app/livewire-tmp');
        $livewireTmpExists = File::exists($livewireTmpPath);
        $livewireTmpWritable = $livewireTmpExists && File::isWritable($livewireTmpPath);

        $this->table(
            ['Setting', 'Value', 'Status'],
            [
                [
                    'Temporary Upload Path',
                    $livewireTmpPath,
                    !$livewireTmpExists ? '❌ Missing' : ($livewireTmpWritable ? '✅ Writable' : '⚠️  Not Writable')
                ],
                [
                    'Middleware',
                    'Livewire\\Features\\SupportFileUploads\\FileUploadConfiguration',
                    '✅ Built-in'
                ],
            ]
        );
        $this->newLine();

        // Environment Check
        $this->info('🌍 Environment:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['APP_ENV', config('app.env')],
                ['APP_DEBUG', config('app.debug') ? 'true' : 'false'],
                ['APP_URL', config('app.url')],
                ['FILESYSTEM_DISK', config('filesystems.default')],
                ['SESSION_DRIVER', config('session.driver')],
            ]
        );
        $this->newLine();

        // Test File Write
        $this->info('🧪 Testing File Write:');
        try {
            $testPath = 'documents/test_' . time() . '.txt';
            Storage::disk('private')->put($testPath, 'Test content');

            if (Storage::disk('private')->exists($testPath)) {
                $this->info('  ✅ File write successful');
                Storage::disk('private')->delete($testPath);
                $this->info('  ✅ File delete successful');
            } else {
                $this->error('  ❌ File write failed - file not found after write');
            }
        } catch (\Exception $e) {
            $this->error('  ❌ File write test failed: ' . $e->getMessage());
        }
        $this->newLine();

        // Recommendations
        $this->info('💡 Recommendations:');
        $recommendations = [];

        $uploadMax = $this->convertToBytes(ini_get('upload_max_filesize'));
        $postMax = $this->convertToBytes(ini_get('post_max_size'));

        if ($uploadMax < 10 * 1024 * 1024) {
            $recommendations[] = '⚠️  Increase upload_max_filesize to at least 10M in php.ini';
        }

        if ($postMax < 12 * 1024 * 1024) {
            $recommendations[] = '⚠️  Increase post_max_size to at least 12M in php.ini (should be larger than upload_max_filesize)';
        }

        if (ini_get('max_execution_time') < 120) {
            $recommendations[] = '⚠️  Increase max_execution_time to at least 120 seconds';
        }

        if (!$livewireTmpWritable) {
            $recommendations[] = '❌ Create and make writable: ' . $livewireTmpPath;
            $recommendations[] = '   Run: mkdir -p ' . $livewireTmpPath . ' && chmod 755 ' . $livewireTmpPath;
        }

        if (empty($recommendations)) {
            $this->info('  ✅ No issues found! Configuration looks good.');
        } else {
            foreach ($recommendations as $rec) {
                $this->line('  ' . $rec);
            }
        }
        $this->newLine();

        // Additional checks for production
        if (config('app.env') === 'production') {
            $this->info('🚀 Production-Specific Checks:');
            $checks = [];

            $checks[] = ['Web Server', 'Check nginx client_max_body_size or Apache LimitRequestBody'];
            $checks[] = ['Reverse Proxy', 'Check proxy timeout settings (nginx proxy_read_timeout)'];
            $checks[] = ['Load Balancer', 'Verify upload size limits and timeout settings'];
            $checks[] = ['CDN/CloudFlare', 'Check if file upload paths bypass CDN'];
            $checks[] = ['HTTPS', 'Ensure secure connection for large uploads'];
            $checks[] = ['PHP-FPM', 'Check request_terminate_timeout setting'];

            $this->table(['Component', 'Action Required'], $checks);
            $this->newLine();

            $this->warn('📝 Additional Steps for Production:');
            $this->line('  1. Check web server config: /etc/nginx/nginx.conf or /etc/apache2/apache2.conf');
            $this->line('  2. Verify PHP-FPM settings: /etc/php/8.x/fpm/pool.d/www.conf');
            $this->line('  3. Check application logs: ' . storage_path('logs/laravel.log'));
            $this->line('  4. Monitor browser console for JavaScript errors');
            $this->line('  5. Check network tab for failed upload requests');
            $this->newLine();
        }

        $this->info('✅ Diagnostics complete!');

        return Command::SUCCESS;
    }

    private function convertToBytes($value)
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    private function getUploadSizeStatus($value)
    {
        $bytes = $this->convertToBytes($value);
        $minRequired = 10 * 1024 * 1024; // 10MB

        if ($bytes >= $minRequired) {
            return '✅ OK';
        } elseif ($bytes >= 5 * 1024 * 1024) {
            return '⚠️  Low';
        } else {
            return '❌ Too Low';
        }
    }

    private function getMemoryStatus($value)
    {
        if ($value === '-1') {
            return '✅ Unlimited';
        }

        $bytes = $this->convertToBytes($value);
        $minRequired = 128 * 1024 * 1024; // 128MB

        return $bytes >= $minRequired ? '✅ OK' : '⚠️  Low';
    }
}
