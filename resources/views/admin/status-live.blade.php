<x-layouts.app :title="__('System Status')">
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-base-content flex items-center gap-3">
                    <x-heroicon-o-signal class="w-8 h-8 text-primary" />
                    System Status
                    <div class="badge badge-info badge-sm">Live</div>
                </h1>
                <p class="text-base-content/70 mt-1">Real-time system health monitoring</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Livewire Status Monitor Component -->
        <livewire:system-status-monitor />

        <!-- System Information -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title flex items-center gap-2">
                    <x-heroicon-o-information-circle class="w-5 h-5" />
                    System Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                    <div class="space-y-2">
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
                    </div>
                    <div class="space-y-2">
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
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Timezone</span>
                            <span class="text-sm font-mono">{{ config('app.timezone') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Server Time</span>
                            <span class="text-sm font-mono">{{ now()->format('H:i:s T') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-base-content/70">Date</span>
                            <span class="text-sm font-mono">{{ now()->format('Y-m-d') }}</span>
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
                    Health checks run automatically every minute via scheduled jobs. The status updates in real-time using Livewire polling.
                    <br>
                    Queue monitoring ensures that background jobs (like medication reminders and notifications) are processing correctly.
                    Use the refresh button to trigger an immediate health check.
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title flex items-center gap-2">
                    <x-heroicon-o-bolt class="w-5 h-5" />
                    Quick Actions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-block justify-start">
                        <x-heroicon-o-home class="w-4 h-4" />
                        Admin Dashboard
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-block justify-start">
                        <x-heroicon-o-users class="w-4 h-4" />
                        Manage Users
                    </a>
                    <a href="{{ route('admin.logs') }}" class="btn btn-outline btn-block justify-start">
                        <x-heroicon-o-document-text class="w-4 h-4" />
                        View Logs
                    </a>
                    <a href="{{ route('admin.email-logs') }}" class="btn btn-outline btn-block justify-start">
                        <x-heroicon-o-envelope class="w-4 h-4" />
                        Email Logs
                    </a>
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
