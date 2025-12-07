<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                @if(session('viewing_as_trusted_contact') && session('original_user_id'))
                    @php
                        $originalUser = \App\Models\User::find(session('original_user_id'));
                    @endphp
                    <h1 class="text-3xl font-bold text-base-content">
                        Viewing {{ auth()->user()->name }}'s Account
                        <span class="badge badge-primary badge-sm ml-2">{{ ucfirst(auth()->user()->account_type) }}</span>
                    </h1>
                @else
                    <h1 class="text-3xl font-bold text-base-content">
                        Welcome back, {{ auth()->user()->name }}!
                        <span class="badge badge-primary badge-sm ml-2">{{ ucfirst(auth()->user()->account_type) }}</span>
                    </h1>
                @endif
                @if(session('viewing_as_trusted_contact'))
                    <p class="text-base-content/70 mt-1">
                        You are viewing this account as a trusted contact
                        @if(!auth()->user()->canTrackSeizures())
                            • Limited access account
                        @endif
                    </p>
                @else
                    <p class="text-base-content/70 mt-1">Here's your health overview for today</p>
                @endif
            </div>
            <div class="text-sm text-base-content/70">
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>

        <!-- Trusted Access Section -->
        @php
            $accessibleAccounts = auth()->user()->validAccessibleAccounts()->take(5)->get();
        @endphp

        @if(!session('viewing_as_trusted_contact') && $accessibleAccounts->count() > 0)
            <div class="card bg-base-100 shadow-xl border-l-4 border-l-info">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-info/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-info">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="card-title text-xl">Trusted Access</h2>
                                <p class="text-base-content/70 text-sm">Manage account access and switching</p>
                            </div>
                        </div>
                        <a href="{{ route('settings.trusted-contacts.index') }}" class="btn btn-info btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Manage
                        </a>
                    </div>

                    @if($accessibleAccounts->count() > 0)
                        <!-- Available accounts to access -->
                        <div>
                            <h3 class="font-semibold mb-3">Accounts You Can Access</h3>
                            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                                @foreach($accessibleAccounts as $account)
                                    <div class="card bg-base-200/50 border border-base-300/50">
                                        <div class="card-body p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <x-avatar :user="$account" size="sm" />
                                                    <div>
                                                        <h4 class="font-medium text-sm">{{ $account->name }}</h4>
                                                        @if($account->pivot->nickname)
                                                            <p class="text-xs text-base-content/70">{{ $account->pivot->nickname }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                @unless(session('viewing_as_trusted_contact') && session('trusted_user_id') == $account->id)
                                                    <form action="{{ route('trusted-access.switch-to', $account) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-primary">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            View
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="badge badge-success badge-xs">Current</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if(auth()->user()->validAccessibleAccounts()->count() > 5)
                                <div class="mt-3 text-center">
                                    <a href="{{ route('settings.trusted-contacts.index') }}" class="link link-primary text-sm">
                                        View all {{ auth()->user()->validAccessibleAccounts()->count() }} accessible accounts
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif



        @if(auth()->user()->canTrackSeizures())
            <!-- Quick Stats Cards - Patient Only -->
            <div class="grid auto-rows-min gap-6 md:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('seizures.index') }}" class="card bg-gradient-to-br from-primary to-secondary hover:shadow-xl transition-all duration-300 hover:opacity-95">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="card-title text-xl text-primary-content mb-2">Seizure Tracker</h3>
                                <p class="text-primary-content/80 text-sm">Track and manage seizure records</p>
                            </div>
                            <div class="text-3xl text-primary-content">📊</div>
                        </div>
                        <div class="mt-4 text-primary-content/90">
                            <div class="text-2xl font-bold">{{ auth()->user()->seizures()->count() }}</div>
                            <div class="text-xs opacity-75">Total records</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('medications.schedule') }}" class="card bg-gradient-to-br from-success to-accent hover:shadow-xl transition-all duration-300 hover:opacity-95">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="card-title text-xl text-success-content mb-2">Today's Schedule</h3>
                                <p class="text-success-content/80 text-sm">View medication schedule</p>
                            </div>
                            <div class="text-3xl text-success-content">📅</div>
                        </div>
                        <div class="mt-4 text-success-content/90">
                            <div class="text-2xl font-bold">{{ auth()->user()->medications()->where('active', true)->count() }}</div>
                            <div class="text-xs opacity-75">Active medications</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('medications.index') }}" class="card bg-gradient-to-br from-info to-primary hover:shadow-xl transition-all duration-300 hover:opacity-95">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="card-title text-xl text-info-content mb-2">Medications</h3>
                                <p class="text-info-content/80 text-sm">Manage your medications</p>
                            </div>
                            <div class="text-3xl text-info-content">💊</div>
                        </div>
                        <div class="mt-4 text-info-content/90">
                            <div class="text-2xl font-bold">{{ auth()->user()->medications()->count() }}</div>
                            <div class="text-xs opacity-75">Total medications</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('vitals.index') }}" class="card bg-gradient-to-br from-purple-500 to-purple-700 hover:shadow-xl transition-all duration-300 hover:opacity-95">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="card-title text-xl text-white mb-2">Vitals Tracker</h3>
                                <p class="text-white/80 text-sm">Monitor your health vitals</p>
                            </div>
                            <div class="text-3xl text-white">❤️</div>
                        </div>
                        <div class="mt-4 text-white/90">
                            <div class="text-2xl font-bold">{{ auth()->user()->vitals()->count() }}</div>
                            <div class="text-xs opacity-75">Vital records</div>
                        </div>
                    </div>
                </a>


            </div>
        @endif

        @if(auth()->user()->canTrackSeizures())
            <!-- Emergency Settings Quick Access - Patient Only -->
            @php
                $user = auth()->user();
                $hasEmergencyContacts = !empty($user->emergency_contact_info);
                $emergencyThresholdsSet = $user->status_epilepticus_duration_minutes && $user->emergency_seizure_count;
            @endphp

            <div class="card bg-base-100 shadow-xl border-l-4 border-l-error">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-error/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-error">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="card-title text-xl">Emergency Settings</h2>
                                <p class="text-base-content/70 text-sm">Your current emergency thresholds and contacts</p>
                            </div>
                        </div>
                        <a href="{{ route('settings.emergency-settings') }}" class="btn btn-primary btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Configure
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Status Epilepticus Threshold -->
                        <div class="stat bg-base-200 rounded-lg">
                            <div class="stat-figure text-error">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="stat-title">Status Epilepticus</div>
                            <div class="stat-value text-2xl">{{ $user->status_epilepticus_duration_minutes }}min</div>
                            <div class="stat-desc">Duration threshold</div>
                        </div>

                        <!-- Cluster Emergency Threshold -->
                        <div class="stat bg-base-200 rounded-lg">
                            <div class="stat-figure text-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.601a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                </svg>
                            </div>
                            <div class="stat-title">Cluster Emergency</div>
                            <div class="stat-value text-2xl">{{ $user->emergency_seizure_count }}</div>
                            <div class="stat-desc">Seizures in {{ $user->emergency_seizure_timeframe_hours }}hrs</div>
                        </div>

                        <!-- Emergency Contacts Status -->
                        <div class="stat bg-base-200 rounded-lg">
                            <div class="stat-figure {{ $hasEmergencyContacts ? 'text-success' : 'text-error' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                            </div>
                            <div class="stat-title">Emergency Contacts</div>
                            <div class="stat-value text-lg {{ $hasEmergencyContacts ? 'text-success' : 'text-error' }}">
                                {{ $hasEmergencyContacts ? 'Configured' : 'Not Set' }}
                            </div>
                            <div class="stat-desc {{ $hasEmergencyContacts ? 'text-success' : 'text-error' }}">
                                {{ $hasEmergencyContacts ? 'Emergency info ready' : 'Please configure' }}
                            </div>
                        </div>
                    </div>

                    @if(!$hasEmergencyContacts)
                        <div class="alert alert-warning mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <span>Your emergency contact information is not set up. Consider adding this important safety information.</span>
                            <a href="{{ route('settings.emergency-settings') }}" class="btn btn-warning btn-sm">
                                Set Up Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Emergency Live Tracker - Available to All Users -->
        <div class="card bg-error text-error-content shadow-xl border-l-4 border-l-error">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="text-6xl">🚨</div>
                        <div>
                            <h2 class="card-title text-2xl">Emergency Seizure Timer</h2>
                            <p class="text-error-content/80 mt-1">Live seizure tracking with emergency alerts</p>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('seizures.live-tracker') }}" class="btn btn-error emergency-pulse btn-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Start Live Timer
                        </a>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="alert alert-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm">
                            <span class="font-semibold">Emergency Tool:</span> Available to track seizures for any patient account you have access to. Automatically alerts when seizures exceed safe duration limits.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!auth()->user()->canTrackSeizures())
            <!-- Non-Patient Account Information -->
            <div class="card bg-base-100 shadow-xl border-l-4 border-l-info">
                <div class="card-body">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="text-4xl">🤝</div>
                        <div>
                            <h2 class="card-title text-xl">{{ ucfirst(auth()->user()->account_type) }} Account</h2>
                            <p class="text-base-content/70">Your account is set up for trusted access to patient records</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="card bg-base-200">
                            <div class="card-body p-4">
                                <h3 class="font-semibold mb-2">How it works</h3>
                                <ul class="text-sm space-y-1">
                                    <li>• Patients add you as a trusted contact</li>
                                    <li>• You receive access to their seizure data</li>
                                    <li>• Track seizures on their behalf</li>
                                    <li>• View their medical information</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card bg-base-200">
                            <div class="card-body p-4">
                                <h3 class="font-semibold mb-2">Getting started</h3>
                                <p class="text-sm mb-3">Ask a patient to invite you as a trusted contact from their settings.</p>
                                <a href="{{ route('settings.trusted-contacts.index') }}" class="btn btn-primary btn-sm">
                                    View Trusted Access
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(auth()->user()->canTrackSeizures())
        <!-- Recent Activity Section -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Recent Seizures -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="card-title text-xl">Recent Seizures</h2>
                        <a href="{{ route('seizures.index') }}" class="btn btn-ghost btn-sm">View All</a>
                    </div>
                    @php
                        // Get the effective user (trusted user when viewing as trusted contact)
                        $effectiveUser = auth()->user();
                        $recentSeizures = $effectiveUser->seizures()->latest()->take(3)->get();
                    @endphp
                    @if($recentSeizures->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentSeizures as $seizure)
                                <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                                    <div>
                                        <div class="font-medium">{{ $seizure->start_time->format('M j, Y') }}</div>
                                        <div class="text-sm text-base-content/70">Severity: {{ $seizure->severity }}/10</div>
                                    </div>
                                    <div class="text-sm text-base-content/70">
                                        {{ $seizure->start_time->format('g:i A') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-base-content/70">
                            <p class="mb-2">No seizures recorded yet</p>
                            <a href="{{ route('seizures.create') }}" class="link link-primary">Add your first seizure record</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Vitals -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="card-title text-xl">Recent Vitals</h2>
                        <a href="{{ route('vitals.index') }}" class="btn btn-ghost btn-sm">View All</a>
                    </div>
                    @php
                        // Get the effective user (trusted user when viewing as trusted contact)
                        $effectiveUser = auth()->user();
                        $recentVitals = $effectiveUser->vitals()->latest()->take(3)->get();
                    @endphp
                    @if($recentVitals->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentVitals as $vital)
                                <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                                    <div>
                                        <div class="font-medium">{{ $vital->type }}</div>
                                        <div class="text-sm text-base-content/70">{{ $vital->recorded_at->format('M j, Y g:i A') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-primary">{{ $vital->value }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-base-content/70">
                            <p class="mb-2">No vitals recorded yet</p>
                            <a href="{{ route('vitals.create') }}" class="link link-primary">Add your first vital</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</x-layouts.app>
