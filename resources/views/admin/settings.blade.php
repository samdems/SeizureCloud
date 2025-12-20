<x-layouts.app :title="__('Admin Settings')">
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-base-content flex items-center gap-3">
                    <x-heroicon-o-cog-6-tooth class="w-8 h-8 text-primary" />
                    Admin Settings
                </h1>
                <p class="text-base-content/70 mt-1">System configuration and settings</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Settings Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- System Information -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-server class="w-5 h-5" />
                        System Information
                    </h3>
                    <div class="space-y-4 mt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium">Laravel Version</span>
                            <span class="badge badge-outline">{{ app()->version() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium">PHP Version</span>
                            <span class="badge badge-outline">{{ phpversion() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium">Environment</span>
                            <span class="badge
                                @if(app()->environment('production')) badge-error
                                @elseif(app()->environment('staging')) badge-warning
                                @else badge-info @endif">
                                {{ ucfirst(app()->environment()) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium">Debug Mode</span>
                            <span class="badge {{ config('app.debug') ? 'badge-warning' : 'badge-success' }}">
                                {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Settings -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-adjustments-horizontal class="w-5 h-5" />
                        Application Settings
                    </h3>
                    <div class="space-y-6 mt-4">
                        <div class="form-control">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <label class="label">
                                        <span class="label-text font-medium">User Registration</span>
                                    </label>
                                    <p class="text-sm text-base-content/70">Allow new users to register</p>
                                </div>
                                <input type="checkbox" class="toggle toggle-primary" checked />
                            </div>
                        </div>
                        <div class="form-control">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <label class="label">
                                        <span class="label-text font-medium">Email Verification</span>
                                    </label>
                                    <p class="text-sm text-base-content/70">Require email verification for new users</p>
                                </div>
                                <input type="checkbox" class="toggle toggle-primary" checked />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-shield-check class="w-5 h-5" />
                        Security Settings
                    </h3>
                    <div class="space-y-6 mt-4">
                        <div class="form-control">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <label class="label">
                                        <span class="label-text font-medium">Two-Factor Authentication</span>
                                    </label>
                                    <p class="text-sm text-base-content/70">Enable 2FA for all users</p>
                                </div>
                                <input type="checkbox" class="toggle toggle-secondary" />
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Session Timeout</span>
                            </label>
                            <select class="select select-bordered w-full">
                                <option>30 minutes</option>
                                <option>1 hour</option>
                                <option>2 hours</option>
                                <option>Never</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Backup & Maintenance -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-archive-box class="w-5 h-5" />
                        Backup & Maintenance
                    </h3>
                    <div class="space-y-3 mt-4">
                        <button type="button" class="btn btn-outline w-full justify-start">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                            Download Database Backup
                        </button>
                        <button type="button" class="btn btn-outline w-full justify-start">
                            <x-heroicon-o-arrow-path class="w-4 h-4" />
                            Clear Application Cache
                        </button>
                        <button type="button" class="btn btn-outline w-full justify-start">
                            <x-heroicon-o-document-text class="w-4 h-4" />
                            View System Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title flex items-center gap-2 mb-6">
                    <x-heroicon-o-envelope class="w-5 h-5" />
                    Email Configuration
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">SMTP Host</span>
                        </label>
                        <input type="text" placeholder="smtp.example.com" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">SMTP Port</span>
                        </label>
                        <input type="number" placeholder="587" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">SMTP Username</span>
                        </label>
                        <input type="text" placeholder="username@example.com" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">SMTP Password</span>
                        </label>
                        <input type="password" placeholder="••••••••" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">From Address</span>
                        </label>
                        <input type="email" placeholder="noreply@example.com" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">From Name</span>
                        </label>
                        <input type="text" placeholder="Epilepsy Diary" class="input input-bordered" />
                    </div>
                </div>
                <div class="card-actions justify-end mt-6">
                    <button type="button" class="btn btn-primary">
                        <x-heroicon-o-envelope class="w-4 h-4" />
                        Test Email Settings
                    </button>
                </div>
            </div>
        </div>

        <!-- Save Changes -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="card-actions justify-end">
                    <button type="button" class="btn btn-outline">
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <x-heroicon-o-check class="w-4 h-4" />
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        <!-- Notice -->
        <div class="alert alert-warning">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
            <div>
                <h3 class="font-bold">Settings Notice</h3>
                <div class="text-sm">This is a placeholder admin settings page. In a production environment, these settings would be functional and connected to the application configuration.</div>
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
