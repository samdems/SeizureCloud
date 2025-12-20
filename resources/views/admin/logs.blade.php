<x-layouts.app :title="__('System Logs')">
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-base-content flex items-center gap-3">
                    <x-heroicon-o-document-text class="w-8 h-8 text-primary" />
                    System Logs
                </h1>
                <p class="text-base-content/70 mt-1">Monitor system activity and user actions</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Back to Dashboard
                </a>
                <button type="button" class="btn btn-success">
                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                    Refresh Logs
                </button>
            </div>
        </div>

        <!-- Log Filters -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex flex-wrap gap-4">
                    <div class="form-control">
                        <select class="select select-bordered">
                            <option value="">All Log Levels</option>
                            <option value="error">Error</option>
                            <option value="warning">Warning</option>
                            <option value="info">Info</option>
                            <option value="debug">Debug</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <select class="select select-bordered">
                            <option value="">All Categories</option>
                            <option value="auth">Authentication</option>
                            <option value="user">User Management</option>
                            <option value="system">System</option>
                            <option value="database">Database</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <input type="date" class="input input-bordered" />
                    </div>

                    <div class="flex gap-2">
                        <button type="button" class="btn btn-primary">
                            <x-heroicon-o-funnel class="w-4 h-4" />
                            Apply Filters
                        </button>
                        <button type="button" class="btn btn-outline">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title flex items-center gap-2 mb-4">
                    <x-heroicon-o-clock class="w-5 h-5" />
                    Recent User Activity
                </h3>

                <div class="space-y-4">
                    @forelse($recentRegistrations as $user)
                    <div class="alert">
                        <div class="avatar">
                            <div class="mask mask-circle w-8 h-8">
                                <img src="{{ $user->avatarUrl(32) }}" alt="{{ $user->name }}" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold">{{ $user->name }}</div>
                            <div class="text-sm opacity-70">{{ $user->email }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="badge badge-success badge-sm">User Registration</span>
                            <span class="text-xs opacity-50">{{ $user->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <div class="flex flex-col items-center gap-4 text-base-content/50">
                            <x-heroicon-o-document-text class="w-16 h-16" />
                            <div>
                                <h3 class="text-lg font-medium">No recent activity</h3>
                                <p class="text-sm">System activity will appear here when available.</p>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- System Logs Table -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-server class="w-5 h-5" />
                        System Logs
                    </h3>
                    <div class="flex gap-2">
                        <button type="button" class="btn btn-outline btn-sm">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                            Export
                        </button>
                        <button type="button" class="btn btn-error btn-outline btn-sm">
                            <x-heroicon-o-trash class="w-4 h-4" />
                            Clear Logs
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Level</th>
                                <th>Category</th>
                                <th>Message</th>
                                <th>User</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sample log entries -->
                            <tr class="hover">
                                <td>
                                    <span class="badge badge-info badge-sm">INFO</span>
                                </td>
                                <td>
                                    <span class="badge badge-outline badge-sm">Authentication</span>
                                </td>
                                <td>User logged in successfully</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="avatar">
                                            <div class="mask mask-circle w-6 h-6">
                                                <div class="bg-primary text-primary-content flex items-center justify-center text-xs">A</div>
                                            </div>
                                        </div>
                                        <span class="text-sm">admin@example.com</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">{{ now()->format('M j, Y') }}</div>
                                    <div class="text-xs opacity-50">{{ now()->format('g:i A') }}</div>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td>
                                    <span class="badge badge-info badge-sm">INFO</span>
                                </td>
                                <td>
                                    <span class="badge badge-outline badge-sm">User Management</span>
                                </td>
                                <td>Admin user promoted another user</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="avatar">
                                            <div class="mask mask-circle w-6 h-6">
                                                <div class="bg-primary text-primary-content flex items-center justify-center text-xs">A</div>
                                            </div>
                                        </div>
                                        <span class="text-sm">admin@example.com</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">{{ now()->subMinutes(15)->format('M j, Y') }}</div>
                                    <div class="text-xs opacity-50">{{ now()->subMinutes(15)->format('g:i A') }}</div>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td>
                                    <span class="badge badge-warning badge-sm">WARNING</span>
                                </td>
                                <td>
                                    <span class="badge badge-outline badge-sm">Authentication</span>
                                </td>
                                <td>Multiple failed login attempts detected</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="avatar">
                                            <div class="mask mask-circle w-6 h-6">
                                                <div class="bg-warning text-warning-content flex items-center justify-center text-xs">?</div>
                                            </div>
                                        </div>
                                        <span class="text-sm">unknown@example.com</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">{{ now()->subHour()->format('M j, Y') }}</div>
                                    <div class="text-xs opacity-50">{{ now()->subHour()->format('g:i A') }}</div>
                                </td>
                            </tr>
                            <tr class="hover">
                                <td>
                                    <span class="badge badge-error badge-sm">ERROR</span>
                                </td>
                                <td>
                                    <span class="badge badge-outline badge-sm">Database</span>
                                </td>
                                <td>Database connection timeout</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="avatar">
                                            <div class="mask mask-circle w-6 h-6">
                                                <div class="bg-error text-error-content flex items-center justify-center text-xs">S</div>
                                            </div>
                                        </div>
                                        <span class="text-sm">System</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">{{ now()->subHours(2)->format('M j, Y') }}</div>
                                    <div class="text-xs opacity-50">{{ now()->subHours(2)->format('g:i A') }}</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Log Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-info">
                    <x-heroicon-o-information-circle class="w-8 h-8" />
                </div>
                <div class="stat-title">Info Messages</div>
                <div class="stat-value text-info">156</div>
                <div class="stat-desc">Last 24 hours</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-warning">
                    <x-heroicon-o-exclamation-triangle class="w-8 h-8" />
                </div>
                <div class="stat-title">Warnings</div>
                <div class="stat-value text-warning">12</div>
                <div class="stat-desc">Last 24 hours</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-error">
                    <x-heroicon-o-x-circle class="w-8 h-8" />
                </div>
                <div class="stat-title">Errors</div>
                <div class="stat-value text-error">3</div>
                <div class="stat-desc">Last 24 hours</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-success">
                    <x-heroicon-o-check-circle class="w-8 h-8" />
                </div>
                <div class="stat-title">Uptime</div>
                <div class="stat-value text-success">99.9%</div>
                <div class="stat-desc">Last 30 days</div>
            </div>
        </div>

        <!-- Notice -->
        <div class="alert alert-info">
            <x-heroicon-o-information-circle class="w-5 h-5" />
            <div>
                <h3 class="font-bold">System Logs Information</h3>
                <div class="text-sm">This is a placeholder logs page. In a production environment, this would show actual system logs, user activities, and error messages from Laravel's logging system.</div>
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
