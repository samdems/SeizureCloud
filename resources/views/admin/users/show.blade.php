<x-layouts.app :title="__('User Details - ' . $user->name)">
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-base-content flex items-center gap-3">
                    <x-heroicon-o-user class="w-8 h-8 text-primary" />
                    User Details
                </h1>
                <p class="text-base-content/70 mt-1">{{ $user->name }} - {{ $user->email }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Back to Users
                </a>
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                    <x-heroicon-o-pencil class="w-4 h-4" />
                    Edit User
                </a>
            </div>
        </div>

        <!-- User Profile Card -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex items-start gap-6">
                    <div class="avatar">
                        <div class="mask mask-circle w-24 h-24">
                            <img src="{{ $user->avatarUrl(96) }}" alt="{{ $user->name }}" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="card-title text-2xl flex items-center gap-3">
                            {{ $user->name }}
                            @if($user->isAdmin())
                                <span class="badge badge-error">Admin</span>
                            @endif
                            @if($user->hasVerifiedEmail())
                                <span class="badge badge-success">Verified</span>
                            @else
                                <span class="badge badge-warning">Unverified</span>
                            @endif
                        </h3>
                        <p class="text-lg text-base-content/70 mb-4">{{ $user->email }}</p>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="stat">
                                <div class="stat-title">Account Type</div>
                                <div class="stat-value text-lg">
                                    @if($user->account_type === 'patient')
                                        <span class="badge badge-success">Patient</span>
                                    @elseif($user->account_type === 'carer')
                                        <span class="badge badge-info">Carer</span>
                                    @else
                                        <span class="badge badge-accent">Medical</span>
                                    @endif
                                </div>
                            </div>

                            <div class="stat">
                                <div class="stat-title">Member Since</div>
                                <div class="stat-value text-lg">{{ $user->created_at->format('M Y') }}</div>
                                <div class="stat-desc">{{ $user->created_at->diffForHumans() }}</div>
                            </div>

                            <div class="stat">
                                <div class="stat-title">Last Activity</div>
                                <div class="stat-value text-lg">{{ $user->updated_at->format('M d') }}</div>
                                <div class="stat-desc">{{ $user->updated_at->diffForHumans() }}</div>
                            </div>

                            <div class="stat">
                                <div class="stat-title">Registration</div>
                                <div class="stat-value text-lg">
                                    @if($user->created_via_invitation)
                                        <span class="badge badge-info badge-sm">Invited</span>
                                    @else
                                        <span class="badge badge-success badge-sm">Direct</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-primary">
                    <x-heroicon-o-chart-bar class="w-8 h-8" />
                </div>
                <div class="stat-title">Seizures Tracked</div>
                <div class="stat-value text-primary">{{ $stats['seizure_count'] }}</div>
                <div class="stat-desc">Total seizure records</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-secondary">
                    <x-heroicon-o-beaker class="w-8 h-8" />
                </div>
                <div class="stat-title">Medications</div>
                <div class="stat-value text-secondary">{{ $stats['medication_count'] }}</div>
                <div class="stat-desc">Total medications</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-success">
                    <x-heroicon-o-users class="w-8 h-8" />
                </div>
                <div class="stat-title">Trusted Contacts</div>
                <div class="stat-value text-success">{{ $stats['trusted_contacts_count'] }}</div>
                <div class="stat-desc">Active connections</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-warning">
                    <x-heroicon-o-user-group class="w-8 h-8" />
                </div>
                <div class="stat-title">Trusted Accounts</div>
                <div class="stat-value text-warning">{{ $stats['trusted_accounts_count'] }}</div>
                <div class="stat-desc">Has access to</div>
            </div>
        </div>

        <!-- User Details and Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Account Information -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2 mb-4">
                        <x-heroicon-o-identification class="w-5 h-5" />
                        Account Information
                    </h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="font-medium">User ID</span>
                            <span class="font-mono">{{ $user->id }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="font-medium">Email Status</span>
                            @if($user->hasVerifiedEmail())
                                <span class="badge badge-success badge-sm">Verified {{ $user->email_verified_at->format('M j, Y') }}</span>
                            @else
                                <span class="badge badge-warning badge-sm">Unverified</span>
                            @endif
                        </div>

                        @if($user->canTrackSeizures())
                        <div class="flex justify-between items-center">
                            <span class="font-medium">Time Preferences</span>
                            <div class="text-right text-sm">
                                <div>Morning: {{ $user->morning_time->format('g:i A') }}</div>
                                <div>Afternoon: {{ $user->afternoon_time->format('g:i A') }}</div>
                                <div>Evening: {{ $user->evening_time->format('g:i A') }}</div>
                                <div>Bedtime: {{ $user->bedtime->format('g:i A') }}</div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="font-medium">Emergency Settings</span>
                            <div class="text-right text-sm">
                                <div>SE Duration: {{ $user->status_epilepticus_duration_minutes }}min</div>
                                <div>Cluster Count: {{ $user->emergency_seizure_count }}</div>
                                <div>Timeframe: {{ $user->emergency_seizure_timeframe_hours }}hrs</div>
                            </div>
                        </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <span class="font-medium">Avatar Style</span>
                            <span class="badge badge-outline badge-sm">{{ ucfirst($user->avatar_style ?? 'initials') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2 mb-4">
                        <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                        Admin Actions
                    </h3>

                    <div class="space-y-3">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline w-full justify-start">
                            <x-heroicon-o-pencil class="w-4 h-4" />
                            Edit User Details
                        </a>

                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="w-full">
                            @csrf
                            <button type="submit"
                                    class="btn {{ $user->isAdmin() ? 'btn-warning' : 'btn-info' }} w-full justify-start"
                                    onclick="return confirm('Are you sure you want to {{ $user->isAdmin() ? 'demote this user from admin' : 'promote this user to admin' }}?')">
                                @if($user->isAdmin())
                                    <x-heroicon-o-arrow-down class="w-4 h-4" />
                                    Demote from Admin
                                @else
                                    <x-heroicon-o-arrow-up class="w-4 h-4" />
                                    Promote to Admin
                                @endif
                            </button>
                        </form>

                        @if(!$user->hasVerifiedEmail())
                        <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="w-full">
                            @csrf
                            <button type="submit" class="btn btn-success w-full justify-start">
                                <x-heroicon-o-check-circle class="w-4 h-4" />
                                Activate Account
                            </button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="w-full">
                            @csrf
                            <button type="submit"
                                    class="btn btn-warning w-full justify-start"
                                    onclick="return confirm('Are you sure you want to deactivate this user?')">
                                <x-heroicon-o-x-circle class="w-4 h-4" />
                                Deactivate Account
                            </button>
                        </form>
                        @endif

                        <div class="divider"></div>

                        <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-error w-full justify-start"
                                    onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone and will delete all associated data.')">
                                <x-heroicon-o-trash class="w-4 h-4" />
                                Delete User
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        @if($user->canTrackSeizures() && $user->seizures && $user->seizures->count() > 0)
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title flex items-center gap-2 mb-4">
                    <x-heroicon-o-chart-bar class="w-5 h-5" />
                    Recent Seizures
                </h3>

                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->seizures->take(5) as $seizure)
                            <tr>
                                <td>
                                    <div>{{ $seizure->start_time->format('M j, Y') }}</div>
                                    <div class="text-xs opacity-70">{{ $seizure->start_time->format('g:i A') }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-outline badge-sm">{{ $seizure->seizure_type ?? 'Unknown' }}</span>
                                </td>
                                <td>
                                    @if($seizure->calculated_duration)
                                        {{ $seizure->calculated_duration }} min
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="max-w-xs truncate">
                                        {{ $seizure->notes ?? '-' }}
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

        <!-- Medications -->
        @if($user->canTrackSeizures() && $user->medications && $user->medications->count() > 0)
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title flex items-center gap-2 mb-4">
                    <x-heroicon-o-beaker class="w-5 h-5" />
                    Medications
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($user->medications->take(6) as $medication)
                    <div class="card bg-base-200 shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="card-title text-lg">{{ $medication->name }}</h4>
                            <div class="text-sm space-y-1">
                                <div><strong>Dosage:</strong> {{ $medication->dosage ?? 'Not specified' }}</div>
                                <div><strong>Frequency:</strong> {{ $medication->frequency ?? 'Not specified' }}</div>
                                @if($medication->as_needed)
                                    <span class="badge badge-info badge-sm">As Needed</span>
                                @endif
                                @if($medication->active)
                                    <span class="badge badge-success badge-sm">Active</span>
                                @else
                                    <span class="badge badge-outline badge-sm">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Trusted Contacts -->
        @if($user->trustedContacts && $user->trustedContacts->count() > 0)
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title flex items-center gap-2 mb-4">
                    <x-heroicon-o-users class="w-5 h-5" />
                    Trusted Contacts
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($user->trustedContacts as $contact)
                    <div class="card bg-base-200 shadow-sm">
                        <div class="card-body p-4">
                            <div class="flex items-center gap-3">
                                <div class="avatar">
                                    <div class="mask mask-circle w-10 h-10">
                                        <img src="{{ $contact->trustedUser->avatarUrl(40) }}" alt="{{ $contact->trustedUser->name }}" />
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">{{ $contact->trustedUser->name }}</div>
                                    <div class="text-sm opacity-70">{{ $contact->trustedUser->email }}</div>
                                    @if($contact->nickname)
                                        <div class="text-xs opacity-60">{{ $contact->nickname }}</div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-success badge-xs">Active</span>
                                    <div class="text-xs opacity-60">Since {{ $contact->granted_at->format('M Y') }}</div>
                                </div>
                            </div>
                        </div>
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
