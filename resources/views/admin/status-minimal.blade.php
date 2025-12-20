<x-layouts.app :title="__('System Status - Minimal')">
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-base-content flex items-center gap-3">
                    <x-heroicon-o-signal class="w-8 h-8 text-primary" />
                    System Status (Minimal)
                </h1>
                <p class="text-base-content/70 mt-1">Basic system health check</p>
            </div>
            <div class="flex gap-3">
                <button onclick="location.reload()" class="btn btn-outline">
                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                    Refresh
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Simple Status Checks -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- PHP Status -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                                <x-heroicon-o-code-bracket class="w-5 h-5 text-primary" />
                            </div>
                            <div>
                                <h3 class="font-semibold">PHP</h3>
                                <div class="text-xs text-base-content/50">{{ PHP_VERSION }}</div>
                            </div>
                        </div>
                        <div class="badge badge-success">Running</div>
                    </div>
                    <p class="text-sm text-base-content/70 mt-3">PHP is running normally</p>
                </div>
            </div>

            <!-- Database Status -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center">
                                <x-heroicon-o-circle-stack class="w-5 h-5 text-secondary" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Database</h3>
                                <div class="text-xs text-base-content/50">Connection test</div>
                            </div>
                        </div>
                        @php
                            $dbStatus = 'error';
                            $dbMessage = 'Connection failed';
                            try {
                                DB::connection()->getPdo();
                                $dbStatus = 'success';
                                $dbMessage = 'Connected successfully';
                            } catch (Exception $e) {
                                $dbMessage = 'Error: ' . $e->getMessage();
                            }
                        @endphp
                        <div class="badge {{ $dbStatus === 'success' ? 'badge-success' : 'badge-error' }}">
                            {{ $dbStatus === 'success' ? 'Online' : 'Offline' }}
                        </div>
                    </div>
                    <p class="text-sm text-base-content/70 mt-3">{{ $dbMessage }}</p>
                </div>
            </div>

            <!-- Laravel Status -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                                <x-heroicon-o-bolt class="w-5 h-5 text-accent" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Laravel</h3>
                                <div class="text-xs text-base-content/50">{{ app()->version() }}</div>
                            </div>
                        </div>
                        <div class="badge badge-success">Running</div>
                    </div>
                    <p class="text-sm text-base-content/70 mt-3">Framework is operational</p>
                </div>
            </div>

            <!-- Cache Status -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-info/10 flex items-center justify-center">
                                <x-heroicon-o-bolt class="w-5 h-5 text-info" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Cache</h3>
                                <div class="text-xs text-base-content/50">{{ config('cache.default') }} driver</div>
                            </div>
                        </div>
                        @php
                            $cacheStatus = 'error';
                            $cacheMessage = 'Cache test failed';
                            try {
                                $testKey = 'health_test_' . time();
                                Cache::put($testKey, 'test', 60);
                                $retrieved = Cache::get($testKey);
                                Cache::forget($testKey);
                                if ($retrieved === 'test') {
                                    $cacheStatus = 'success';
                                    $cacheMessage = 'Cache is working';
                                }
                            } catch (Exception $e) {
                                $cacheMessage = 'Error: ' . $e->getMessage();
                            }
                        @endphp
                        <div class="badge {{ $cacheStatus === 'success' ? 'badge-success' : 'badge-error' }}">
                            {{ $cacheStatus === 'success' ? 'Working' : 'Error' }}
                        </div>
                    </div>
                    <p class="text-sm text-base-content/70 mt-3">{{ $cacheMessage }}</p>
                </div>
            </div>

            <!-- Memory Status -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                                <x-heroicon-o-cpu-chip class="w-5 h-5 text-warning" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Memory</h3>
                                <div class="text-xs text-base-content/50">
                                    {{ round(memory_get_usage(true) / 1024 / 1024, 2) }}MB used
                                </div>
                            </div>
                        </div>
                        <div class="badge badge-success">Normal</div>
                    </div>
                    <p class="text-sm text-base-content/70 mt-3">
                        Peak: {{ round(memory_get_peak_usage(true) / 1024 / 1024, 2) }}MB
                    </p>
                </div>
            </div>

            <!-- Queue Status (Basic) -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                                <x-heroicon-o-queue-list class="w-5 h-5 text-success" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Queue System</h3>
                                <div class="text-xs text-base-content/50">{{ config('queue.default') }} driver</div>
                            </div>
                        </div>
                        @php
                            $queueStatus = 'warning';
                            $queueMessage = 'Queue tables not found';
                            $pendingJobs = 0;
                            $failedJobs = 0;

                            try {
                                $pendingJobs = DB::table('jobs')->count();
                                $failedJobs = DB::table('failed_jobs')->count();
                                $queueStatus = 'success';
                                $queueMessage = "Pending: {$pendingJobs}, Failed: {$failedJobs}";
                            } catch (Exception $e) {
                                $queueMessage = 'Queue tables need to be created';
                            }
                        @endphp
                        <div class="badge {{ $queueStatus === 'success' ? 'badge-success' : 'badge-warning' }}">
                            {{ $queueStatus === 'success' ? 'Available' : 'Setup Needed' }}
                        </div>
                    </div>
                    <p class="text-sm text-base-content/70 mt-3">{{ $queueMessage }}</p>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-information-circle class="w-5 h-5" />
                        System Information
                    </h3>
                    <div class="space-y-2 mt-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">PHP Version</span>
                            <span class="text-sm font-mono">{{ PHP_VERSION }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Laravel Version</span>
                            <span class="text-sm font-mono">{{ app()->version() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Environment</span>
                            <span class="badge badge-sm {{ app()->environment() === 'production' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst(app()->environment()) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Debug Mode</span>
                            <span class="badge badge-sm {{ config('app.debug') ? 'badge-warning' : 'badge-success' }}">
                                {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Queue Driver</span>
                            <span class="text-sm font-mono">{{ config('queue.default') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Cache Driver</span>
                            <span class="text-sm font-mono">{{ config('cache.default') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Server Time</span>
                            <span class="text-sm font-mono">{{ now()->format('H:i:s T') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-bolt class="w-5 h-5" />
                        Quick Actions
                    </h3>
                    <div class="space-y-3 mt-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-block justify-start">
                            <x-heroicon-o-home class="w-4 h-4" />
                            Admin Dashboard
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-block justify-start">
                            <x-heroicon-o-users class="w-4 h-4" />
                            Manage Users
                        </a>
                        <button onclick="location.reload()" class="btn btn-outline btn-block justify-start">
                            <x-heroicon-o-arrow-path class="w-4 h-4" />
                            Refresh Status
                        </button>
                        @if(config('queue.default') !== 'sync')
                        <div class="alert alert-info text-xs">
                            <x-heroicon-o-information-circle class="w-4 h-4" />
                            <div>
                                <strong>Queue Setup:</strong> Run migrations to create queue tables:
                                <code class="text-xs bg-base-200 px-1 rounded">php artisan migrate</code>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Information -->
        <div class="alert alert-info">
            <x-heroicon-o-information-circle class="w-6 h-6" />
            <div>
                <h4 class="font-bold">Status Page Information</h4>
                <div class="text-sm">
                    This is a minimal status page that shows basic system health without complex health checks.
                    <br>
                    • <strong>Green badges</strong> indicate systems are working properly
                    <br>
                    • <strong>Yellow badges</strong> indicate systems need setup or attention
                    <br>
                    • <strong>Red badges</strong> indicate systems have errors
                    <br>
                    Use the refresh button to update the status manually.
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="toast toast-end">
        <div class="alert alert-success">
            <x-heroicon-o-check-circle class="w-6 h-6" />
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="toast toast-end">
        <div class="alert alert-error">
            <x-heroicon-o-x-circle class="w-6 h-6" />
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif
</x-layouts.app>
