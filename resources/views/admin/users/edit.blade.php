<x-layouts.app :title="__('Edit User - ' . $user->name)">
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-base-content flex items-center gap-3">
                    <x-heroicon-o-pencil class="w-8 h-8 text-primary" />
                    Edit User
                </h1>
                <p class="text-base-content/70 mt-1">{{ $user->name }} - {{ $user->email }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Back to Details
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                    <x-heroicon-o-users class="w-4 h-4" />
                    All Users
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h3 class="card-title flex items-center gap-2 mb-4">
                            <x-heroicon-o-user class="w-5 h-5" />
                            Basic Information
                        </h3>

                        <div class="space-y-4">
                            <!-- Name -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Full Name</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       value="{{ old('name', $user->name) }}"
                                       class="input input-bordered @error('name') input-error @enderror"
                                       required />
                                @error('name')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Email Address</span>
                                </label>
                                <input type="email"
                                       name="email"
                                       value="{{ old('email', $user->email) }}"
                                       class="input input-bordered @error('email') input-error @enderror"
                                       required />
                                @error('email')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <!-- Account Type -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Account Type</span>
                                </label>
                                <select name="account_type" class="select select-bordered @error('account_type') select-error @enderror" required>
                                    <option value="patient" {{ old('account_type', $user->account_type) === 'patient' ? 'selected' : '' }}>
                                        Patient - Can track seizures and manage own data
                                    </option>
                                    <option value="carer" {{ old('account_type', $user->account_type) === 'carer' ? 'selected' : '' }}>
                                        Carer - Trusted access to patient accounts only
                                    </option>
                                    <option value="medical" {{ old('account_type', $user->account_type) === 'medical' ? 'selected' : '' }}>
                                        Medical Professional - Healthcare provider access
                                    </option>
                                </select>
                                @error('account_type')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <!-- Admin Status -->
                            <div class="form-control">
                                <label class="cursor-pointer label">
                                    <span class="label-text font-medium">Administrator Privileges</span>
                                    <input type="checkbox"
                                           name="is_admin"
                                           value="1"
                                           class="toggle toggle-error"
                                           {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                                           @if($user->id === auth()->id()) disabled @endif />
                                </label>
                                @if($user->id === auth()->id())
                                <label class="label">
                                    <span class="label-text-alt text-warning">You cannot change your own admin status</span>
                                </label>
                                @endif
                                @error('is_admin')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <!-- Avatar Style -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Avatar Style</span>
                                </label>
                                <select name="avatar_style" class="select select-bordered">
                                    @foreach(\App\Models\User::getAvailableAvatarStyles() as $style => $description)
                                    <option value="{{ $style }}" {{ old('avatar_style', $user->avatar_style) === $style ? 'selected' : '' }}>
                                        {{ $description }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Status & Security -->
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h3 class="card-title flex items-center gap-2 mb-4">
                            <x-heroicon-o-shield-check class="w-5 h-5" />
                            Account Status & Security
                        </h3>

                        <div class="space-y-4">
                            <!-- Current Status -->
                            <div class="alert">
                                <x-heroicon-o-information-circle class="w-5 h-5" />
                                <div>
                                    <h4 class="font-bold">Current Status</h4>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @if($user->hasVerifiedEmail())
                                            <span class="badge badge-success">Email Verified</span>
                                        @else
                                            <span class="badge badge-warning">Email Unverified</span>
                                        @endif

                                        @if($user->isAdmin())
                                            <span class="badge badge-error">Administrator</span>
                                        @endif

                                        @if($user->created_via_invitation)
                                            <span class="badge badge-info">Invited User</span>
                                        @endif

                                        <span class="badge badge-outline">{{ ucfirst($user->account_type) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Change -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">New Password</span>
                                    <span class="label-text-alt">Leave blank to keep current password</span>
                                </label>
                                <input type="password"
                                       name="password"
                                       class="input input-bordered @error('password') input-error @enderror"
                                       placeholder="Enter new password (optional)" />
                                @error('password')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Confirm New Password</span>
                                </label>
                                <input type="password"
                                       name="password_confirmation"
                                       class="input input-bordered"
                                       placeholder="Confirm new password" />
                            </div>

                            <!-- Account Dates -->
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium">Account Created</span>
                                    <span class="text-sm">{{ $user->created_at->format('M j, Y g:i A') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium">Last Updated</span>
                                    <span class="text-sm">{{ $user->updated_at->format('M j, Y g:i A') }}</span>
                                </div>
                                @if($user->email_verified_at)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium">Email Verified</span>
                                    <span class="text-sm">{{ $user->email_verified_at->format('M j, Y g:i A') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient-Specific Settings -->
            @if($user->canTrackSeizures())
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title flex items-center gap-2 mb-4">
                        <x-heroicon-o-clock class="w-5 h-5" />
                        Patient Settings
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Time Preferences -->
                        <div class="space-y-4">
                            <h4 class="font-semibold">Time Preferences</h4>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Morning Time</span>
                                </label>
                                <input type="time"
                                       name="morning_time"
                                       value="{{ old('morning_time', $user->morning_time->format('H:i')) }}"
                                       class="input input-bordered" />
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Afternoon Time</span>
                                </label>
                                <input type="time"
                                       name="afternoon_time"
                                       value="{{ old('afternoon_time', $user->afternoon_time->format('H:i')) }}"
                                       class="input input-bordered" />
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Evening Time</span>
                                </label>
                                <input type="time"
                                       name="evening_time"
                                       value="{{ old('evening_time', $user->evening_time->format('H:i')) }}"
                                       class="input input-bordered" />
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Bedtime</span>
                                </label>
                                <input type="time"
                                       name="bedtime"
                                       value="{{ old('bedtime', $user->bedtime->format('H:i')) }}"
                                       class="input input-bordered" />
                            </div>
                        </div>

                        <!-- Emergency Settings -->
                        <div class="space-y-4">
                            <h4 class="font-semibold">Emergency Settings</h4>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Status Epilepticus Duration (minutes)</span>
                                </label>
                                <input type="number"
                                       name="status_epilepticus_duration_minutes"
                                       value="{{ old('status_epilepticus_duration_minutes', $user->status_epilepticus_duration_minutes) }}"
                                       class="input input-bordered"
                                       min="1" />
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Emergency Seizure Count</span>
                                </label>
                                <input type="number"
                                       name="emergency_seizure_count"
                                       value="{{ old('emergency_seizure_count', $user->emergency_seizure_count) }}"
                                       class="input input-bordered"
                                       min="1" />
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Emergency Timeframe (hours)</span>
                                </label>
                                <input type="number"
                                       name="emergency_seizure_timeframe_hours"
                                       value="{{ old('emergency_seizure_timeframe_hours', $user->emergency_seizure_timeframe_hours) }}"
                                       class="input input-bordered"
                                       min="1" />
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Emergency Contact Info</span>
                                </label>
                                <textarea name="emergency_contact_info"
                                          class="textarea textarea-bordered"
                                          rows="4"
                                          placeholder="Emergency contact information...">{{ old('emergency_contact_info', $user->emergency_contact_info) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="card-actions justify-end gap-3">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <x-heroicon-o-check class="w-4 h-4" />
                            Update User
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Danger Zone -->
        @if($user->id !== auth()->id())
        <div class="card bg-error/10 border border-error/20 shadow">
            <div class="card-body">
                <h3 class="card-title text-error flex items-center gap-2 mb-4">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                    Danger Zone
                </h3>

                <div class="space-y-3">
                    @if(!$user->hasVerifiedEmail())
                    <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <x-heroicon-o-check-circle class="w-4 h-4" />
                            Activate Account
                        </button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="btn btn-warning btn-sm"
                                onclick="return confirm('Are you sure you want to deactivate this user?')">
                            <x-heroicon-o-x-circle class="w-4 h-4" />
                            Deactivate Account
                        </button>
                    </form>
                    @endif

                    <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-error btn-sm"
                                onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone and will delete all associated data.')">
                            <x-heroicon-o-trash class="w-4 h-4" />
                            Delete User
                        </button>
                    </form>
                </div>

                <div class="alert alert-warning mt-4">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                    <div>
                        <h4 class="font-bold">Warning</h4>
                        <p class="text-sm">These actions are irreversible. Please proceed with caution.</p>
                    </div>
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
