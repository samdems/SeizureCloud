<x-layouts.app :title="__('Admin Dashboard')">
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-base-content flex items-center gap-3">
                    <x-heroicon-o-shield-check class="w-8 h-8 text-primary" />
                    Admin Dashboard
                </h1>
                <p class="text-base-content/70 mt-1">System overview and management</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                    <x-heroicon-o-users class="w-4 h-4" />
                    Manage Users
                </a>
                <a href="{{ route('admin.settings') }}" class="btn btn-outline">
                    <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                    Settings
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Users -->
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-primary">
                    <x-heroicon-o-users class="w-8 h-8" />
                </div>
                <div class="stat-title">Total Users</div>
                <div class="stat-value text-primary">{{ $stats['total_users'] }}</div>
                <div class="stat-desc">All registered accounts</div>
            </div>

            <!-- Admin Users -->
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-secondary">
                    <x-heroicon-o-shield-check class="w-8 h-8" />
                </div>
                <div class="stat-title">Admin Users</div>
                <div class="stat-value text-secondary">{{ $stats['admin_users'] }}</div>
                <div class="stat-desc">System administrators</div>
            </div>

            <!-- Patient Accounts -->
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-success">
                    <x-heroicon-o-heart class="w-8 h-8" />
                </div>
                <div class="stat-title">Patient Accounts</div>
                <div class="stat-value text-success">{{ $stats['patient_accounts'] }}</div>
                <div class="stat-desc">Health tracking accounts</div>
            </div>

            <!-- Total Seizures -->
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-warning">
                    <x-heroicon-o-chart-bar class="w-8 h-8" />
                </div>
                <div class="stat-title">Total Seizures</div>
                <div class="stat-value text-warning">{{ $stats['total_seizures'] }}</div>
                <div class="stat-desc">Tracked seizure events</div>
            </div>
        </div>

        <!-- Account Type Breakdown and Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Account Types -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-user-group class="w-5 h-5" />
                        Account Types
                    </h3>
                    <div class="space-y-3 mt-4">
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-2">
                                <div class="badge badge-success badge-sm"></div>
                                Patients
                            </span>
                            <span class="font-mono">{{ $stats['patient_accounts'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-2">
                                <div class="badge badge-info badge-sm"></div>
                                Carers
                            </span>
                            <span class="font-mono">{{ $stats['carer_accounts'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-2">
                                <div class="badge badge-accent badge-sm"></div>
                                Medical
                            </span>
                            <span class="font-mono">{{ $stats['medical_accounts'] }}</span>
                        </div>
                        <div class="divider my-2"></div>
                        <div class="flex justify-between items-center font-semibold">
                            <span>Total</span>
                            <span class="font-mono">{{ $stats['total_users'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Activity -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-signal class="w-5 h-5" />
                        System Activity
                    </h3>
                    <div class="space-y-3 mt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Active Trusted Contacts</span>
                            <span class="badge badge-outline">{{ $stats['active_trusted_contacts'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Pending Invitations</span>
                            <span class="badge badge-outline">{{ $stats['pending_invitations'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Total Medications</span>
                            <span class="badge badge-outline">{{ $stats['total_medications'] }}</span>
                        </div>
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
                    <div class="space-y-3 mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-block justify-start">
                            <x-heroicon-o-users class="w-4 h-4" />
                            View All Users
                        </a>
                        <a href="{{ route('admin.export.users') }}" class="btn btn-outline btn-block justify-start">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                            Export User Data
                        </a>
                        <a href="{{ route('admin.logs') }}" class="btn btn-outline btn-block justify-start">
                            <x-heroicon-o-document-text class="w-4 h-4" />
                            View System Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        @if($recentUsers && $recentUsers->count() > 0)
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-clock class="w-5 h-5" />
                        Recent Users
                    </h3>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-ghost">
                        View all
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Account Type</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $user)
                            <tr class="hover">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="mask mask-circle w-10 h-10">
                                                <img src="{{ $user->avatarUrl(40) }}" alt="{{ $user->name }}" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold">{{ $user->name }}</div>
                                            <div class="text-sm opacity-50">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->account_type === 'patient')
                                        <span class="badge badge-success badge-sm">Patient</span>
                                    @elseif($user->account_type === 'carer')
                                        <span class="badge badge-info badge-sm">Carer</span>
                                    @else
                                        <span class="badge badge-accent badge-sm">Medical</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex flex-col gap-1">
                                        @if($user->isAdmin())
                                        <span class="badge badge-error badge-xs">Admin</span>
                                        @endif
                                        @if($user->hasVerifiedEmail())
                                        <span class="badge badge-success badge-xs">Verified</span>
                                        @else
                                        <span class="badge badge-warning badge-xs">Unverified</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">{{ $user->created_at->format('M j, Y') }}</div>
                                    <div class="text-xs opacity-50">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <div class="dropdown dropdown-end">
                                        <label tabindex="0" class="btn btn-ghost btn-xs">
                                            <x-heroicon-o-ellipsis-horizontal class="w-4 h-4" />
                                        </label>
                                        <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-52 z-10">
                                            <li><a href="{{ route('admin.users.show', $user) }}">View Details</a></li>
                                            <li><a href="{{ route('admin.users.edit', $user) }}">Edit User</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Seizures -->
        @if($recentSeizures && $recentSeizures->count() > 0)
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="card-title flex items-center gap-2">
                        <x-heroicon-o-chart-bar class="w-5 h-5" />
                        Recent Seizure Activity
                    </h3>
                </div>

                <div class="space-y-3">
                    @foreach($recentSeizures->take(5) as $seizure)
                    <div class="alert">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                        <div class="flex-1">
                            <div class="font-semibold">{{ $seizure->user->name }}</div>
                            <div class="text-sm opacity-70">
                                {{ $seizure->seizure_type ?? 'Unknown' }} seizure on {{ $seizure->start_time->format('M j, Y \a\t g:i A') }}
                                @if($seizure->calculated_duration)
                                    ({{ $seizure->calculated_duration }}min)
                                @endif
                            </div>
                        </div>
                        <div class="text-xs opacity-50">{{ $seizure->start_time->diffForHumans() }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
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
