<x-layouts.app :title="__('System Status')">
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-base-content flex items-center gap-3">
                    <x-heroicon-o-signal class="w-8 h-8 text-primary" />
                    System Status
                </h1>
                <p class="text-base-content/70 mt-1">Real-time system health monitoring</p>
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

        <!-- Overall Status -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        @if($overallStatus['status'] === 'healthy')
                            <div class="w-16 h-16 rounded-full bg-success flex items-center justify-center">
                                <x-heroicon-o-check-circle class="w-8 h-8 text-white" />
                            </div>
                        @elseif($overallStatus['status'] === 'warning')
                            <div class="w-16 h-16 rounded-full bg-warning flex items-center justify-center">
                                <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-white" />
                            </div>
                        @elseif($overallStatus['status'] === 'error')
                            <div class="w-16 h-16 rounded-full bg-error flex items-center justify-center">
                                <x-heroicon-o-x-circle class="w-8 h-8 text-white" />
                            </div>
                        @else
                            <div class="w-16 h-16 rounded-full bg-info flex items-center justify-center">
                                <span class="loading loading-spinner loading-lg text-white"></span>
                            </div>
                        @endif

                        <div>
                            <h2 class="text-2xl font-bold">{{ ucfirst($overallStatus['status']) }}</h2>
                            <p class="text-base-content/70">{{ $overallStatus['message'] }}</p>
                            <div class="flex gap-4 text-sm mt-2">
                                <span class="text-success">✓ {{ $overallStatus['healthy_count'] }} Healthy</span>
                                @if($overallStatus['warning_count'] > 0)
                                <span class="text-warning">⚠ {{ $overallStatus['warning_count'] }} Warnings</span>
                                @endif
                                @if($overallStatus['error_count'] > 0)
                                <span class="text-error">✗ {{ $overallStatus['error_count'] }} Errors</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-base-content/50">Last Updated</div>
                        <div class="font-mono">{{ now()->format('H:i:s') }}</div>
                        <div class="text-xs text-base-content/40">{{ now()->format('M j, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Status Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Database Status -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                                <x-heroicon-o-circle-stack class="w-5 h-5 text-primary" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Database</h3>
                                <div class="text-xs text-base-content/50">
                                    @if(isset($healthStatus['database']['latency_ms']))
                                        {{ $healthStatus['database']['latency_ms'] }}ms latency
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($healthStatus['database']['status'] === 'healthy')
                            <div class="badge badge-success">Online</div>
                        @elseif($healthStatus['database']['status'] === 'warning')
                            <div class="badge badge-warning">Warning</div>
                        @elseif($healthStatus['database']['status'] === 'error')
                            <div class="badge badge-error">Offline</div>
                        @else
                            <div class="badge badge-info">Checking</div>
                        @endif
                    </div>
                    <p class="text-sm text-base-content/70 mt-3">{{ $healthStatus['database']['message'] ?? 'Checking status...' }}</p>
                </div>
            </div>

            <!-- Queue Status -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center">
                                <x-heroicon-o-queue-list class="w-5 h-5 text-secondary" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Queue System</h3>
                                <div class="text-xs text-base-content/50">
                                    {{ $queueStats['pending_jobs'] }} pending, {{ $queueStats['failed_jobs'] }} failed
                                </div>
                            </div>
                        </div>
                        @if($healthStatus['queue']['status'] === 'healthy')
                            <div class="badge badge-success">Running</div>
                        @elseif($healthStatus['queue']['status'] === 'warning')
                            <div class="badge badge-warning">Issues</div>
                        @elseif($healthStatus['queue']['status'] === 'error')
                            <div class="badge badge-error">Failed</div>
                        @else
                            <div class="badge badge-info">Checking</div>
                        @endif
                    </div>
                    <p class="text-sm text-base-content/70 mt-3">{{ $healthStatus['queue']['message'] ?? 'Checking status...' }}</p>
                </div>
            </div>

            <!-- Cache Status -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                                <x-heroicon-o-bolt class="w-5 h-5 text-accent" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Cache</h3>
                                <div class="text-xs text-base-content/50">
                                    {{ $healthStatus['cache']['driver'] ?? config('cache.default') }} driver
                                </div>
                            </div>
                        </div>
                        @if($healthStatus['cache']['status'] === 'healthy')
                            <div class="badge badge-success">Working</div>
                        @elseif($healthStatus['cache']['status'] === 'warning')
                            <div class="badge badge-warning">Issues</div>
                        @elseif($healthStatus['cache']['status'] === 'error')
                            <div class="badge badge-error">Failed</div>
                        @else
                            <div class="badge badge-info">Checking</div>
                        @endif
                    </div>
                    <p class="text-sm text-base-content/70 mt-3">{{ $healthStatus['cache']['message'] ?? 'Checking status...' }}</p>
                </div>
            </div>

            <!-- Storage Status -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-info/10 flex items-center justify-center">
                                <x-heroicon-o-folder class="w-5 h-5 text-info" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Storage</h3>
                                <div class="text-xs text-base-content/50">
                                    @if(isset($healthStatus['storage']['disk_usage']['usage_percent']))
                                        {{ $healthStatus['storage']['disk_usage']['usage_percent'] }}% used
                                    @else
                                        File system access
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($healthStatus['storage']['status'] === 'healthy')
                            <div class="badge badge-success">Available</div>
                        @elseif($healthStatus['storage']['status'] === 'warning')
                            <div class="badge badge-warning">Low Space</div>
                        @elseif($healthStatus['storage']['status'] === 'error')
                            <div class="badge badge-error">Unavailable</div>
                        @else
                            <div class="badge badge-info">Checking</div>
                        @endif
                    </div>
                    <p class="text-sm text-base-content/70 mt-3">{{ $healthStatus['storage']['message'] ?? 'Checking status...' }}</p>
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
                                    {{ $healthStatus['memory']['usage_mb'] ?? 'Unknown' }}MB used
                                </div>
                            </div>
                        </div>
                        @if($healthStatus['memory']['status'] === 'healthy')
                            <div class="badge badge-success">Normal</div>
                        @elseif($healthStatus['memory']['status'] === 'warning')
                            <div class="badge badge-warning">High</div>
                        @elseif($healthStatus['memory']['status'] === 'error')
                            <div class="badge badge-error">Critical</div>
                        @else
                            <div class="badge badge-info">Checking</div>
                        @endif
                    </div>
                    <p class="text-sm text-base-content/70 mt-3">{{ $healthStatus['memory']['message'] ?? 'Checking status...' }}</p>
                </div>
            </div>

            <!-- Scheduler Status -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                                <x-heroicon-o-clock class="w-5 h-5 text-success" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Task Scheduler</h3>
                                <div class="text-xs text-base-content/50">
                                    @if(isset($healthStatus['scheduler']['last_run']))
                                        Last run: {{ \Carbon\Carbon::parse($healthStatus['scheduler']['last_run'])->diffForHumans() }}
                                    @else
                                        Automated tasks
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($healthStatus['scheduler']['status'] === 'healthy')
                            <div class="badge badge-success">Active</div>
                        @elseif($healthStatus['scheduler']['status'] === 'warning')
                            <div class="badge badge-warning">Delayed</div>
                        @elseif($healthStatus['scheduler']['status'] === 'error')
                            <div class="badge badge-error">Stopped</div>
                        @else
                            <div class="badge badge-info">Checking</div>
                        @endif
                    </div>
                    <p class="text-sm text-base-content/70 mt-3">{{ $healthStatus['scheduler']['message'] ?? 'Checking status...' }}</p>
                </div>
            </div>
        </div>

        <!-- Queue Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-chart-bar-square class="w-5 h-5" />
                        Queue Statistics
                    </h3>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div class="stat p-4 bg-base-200 rounded-lg">
                            <div class="stat-title text-xs">Pending Jobs</div>
                            <div class="stat-value text-2xl">{{ number_format($queueStats['pending_jobs']) }}</div>
                        </div>
                        <div class="stat p-4 bg-base-200 rounded-lg">
                            <div class="stat-title text-xs">Failed Jobs</div>
                            <div class="stat-value text-2xl">{{ number_format($queueStats['failed_jobs']) }}</div>
                        </div>
                        <div class="stat p-4 bg-base-200 rounded-lg">
                            <div class="stat-title text-xs">Processed (Est.)</div>
                            <div class="stat-value text-2xl">{{ number_format($queueStats['processed_jobs']) }}</div>
                        </div>
                        <div class="stat p-4 bg-base-200 rounded-lg">
                            <div class="stat-title text-xs">Success Rate</div>
                            <div class="stat-value text-2xl">
                                @php
                                    $total = $queueStats['processed_jobs'] + $queueStats['failed_jobs'];
                                    $successRate = $total > 0 ? round((($total - $queueStats['failed_jobs']) / $total) * 100, 1) : 100;
                                @endphp
                                {{ $successRate }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-information-circle class="w-5 h-5" />
                        System Information
                    </h3>
                    <div class="space-y-2 mt-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">PHP Version</span>
                            <span class="text-sm font-mono">{{ $systemInfo['php_version'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Laravel Version</span>
                            <span class="text-sm font-mono">{{ $systemInfo['laravel_version'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Environment</span>
                            <span class="badge badge-sm {{ $systemInfo['environment'] === 'production' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($systemInfo['environment']) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Debug Mode</span>
                            <span class="badge badge-sm {{ $systemInfo['debug_mode'] ? 'badge-warning' : 'badge-success' }}">
                                {{ $systemInfo['debug_mode'] ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Queue Driver</span>
                            <span class="text-sm font-mono">{{ $systemInfo['queue_driver'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Cache Driver</span>
                            <span class="text-sm font-mono">{{ $systemInfo['cache_driver'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Uptime</span>
                            <span class="text-sm font-mono">{{ $systemInfo['uptime'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Check Information -->
        <div class="alert alert-info">
            <x-heroicon-o-information-circle class="w-6 h-6" />
            <div>
                <h4 class="font-bold">Health Check Information</h4>
                <div class="text-sm">
                    Health checks run synchronously when you visit this page. Click refresh to update the status.
                    <br>
                    Queue monitoring helps ensure that background jobs (like medication reminders and notifications) are processing correctly.
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-refresh disabled for debugging -->
    <script>
        // Auto-refresh temporarily disabled
        console.log('Status page loaded successfully');
    </script>
</x-layouts.app>
