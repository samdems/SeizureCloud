<x-layouts.app :title="__('Emergency Settings')">
    <x-settings.layout>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-error">Emergency Settings</h2>
            <p class="text-base-content/70 mt-2">Configure your emergency thresholds and contact information</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="max-w-6xl">
            <form action="{{ route('settings.emergency-settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Emergency Thresholds Section -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">Emergency Thresholds</h3>

                        <div class="space-y-6 lg:space-y-0 lg:grid lg:grid-cols-3 lg:gap-6">
                            <!-- Status Epilepticus Duration -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold text-sm">Status Epilepticus Duration</span>
                                    <span class="label-text-alt text-xs">Minutes</span>
                                </label>
                                <input
                                    type="number"
                                    name="status_epilepticus_duration_minutes"
                                    value="{{ old('status_epilepticus_duration_minutes', $user->status_epilepticus_duration_minutes) }}"
                                    class="input input-bordered w-full @error('status_epilepticus_duration_minutes') input-error @enderror"
                                    min="1"
                                    max="60"
                                    required
                                />
                                <label class="label">
                                    <span class="label-text-alt text-base-content/60 text-xs">
                                        Seizures lasting this long or more are medical emergencies
                                    </span>
                                </label>
                                @error('status_epilepticus_duration_minutes')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Emergency Seizure Count -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold text-sm">Emergency Seizure Count</span>
                                    <span class="label-text-alt text-xs">Seizures</span>
                                </label>
                                <input
                                    type="number"
                                    name="emergency_seizure_count"
                                    value="{{ old('emergency_seizure_count', $user->emergency_seizure_count) }}"
                                    class="input input-bordered w-full @error('emergency_seizure_count') input-error @enderror"
                                    min="2"
                                    max="10"
                                    required
                                />
                                <label class="label">
                                    <span class="label-text-alt text-base-content/60 text-xs">
                                        This many seizures within timeframe triggers alert
                                    </span>
                                </label>
                                @error('emergency_seizure_count')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Emergency Timeframe -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold text-sm">Emergency Timeframe</span>
                                    <span class="label-text-alt text-xs">Hours</span>
                                </label>
                                <input
                                    type="number"
                                    name="emergency_seizure_timeframe_hours"
                                    value="{{ old('emergency_seizure_timeframe_hours', $user->emergency_seizure_timeframe_hours) }}"
                                    class="input input-bordered w-full @error('emergency_seizure_timeframe_hours') input-error @enderror"
                                    min="1"
                                    max="24"
                                    required
                                />
                                <label class="label">
                                    <span class="label-text-alt text-base-content/60 text-xs">
                                        Time window for counting cluster seizures
                                    </span>
                                </label>
                                @error('emergency_seizure_timeframe_hours')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>

                        <div class="text-xs text-base-content/60 mt-6 p-4 bg-base-200 rounded">
                            <p class="mb-2 font-semibold">Medical Guidelines:</p>
                            <div class="space-y-1">
                                <p>• Status epilepticus: 5+ minute seizures require immediate medical attention</p>
                                <p>• Seizure clusters: 3+ seizures in 2 hours often require medical intervention</p>
                                <p>• Review these settings with your healthcare provider</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact Information -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">Emergency Contact Information</h3>

                        <div class="form-control w-full max-w-4xl">
                            <label class="label">
                                <span class="label-text font-semibold">Emergency Contacts</span>
                                <span class="label-text-alt">Optional</span>
                            </label>
                            <textarea
                                name="emergency_contact_info"
                                class="textarea textarea-bordered h-28 @error('emergency_contact_info') textarea-error @enderror"
                                placeholder="Emergency Contacts:

Dr. Sarah Johnson (Neurologist) - 01234 567890
Jane Doe (Partner) - 07890 123456
Medical ID: 12345"
                            >{{ old('emergency_contact_info', $user->emergency_contact_info) }}</textarea>
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">
                                    Contact names, phone numbers, and any relevant medical information
                                </span>
                            </label>
                            @error('emergency_contact_info')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Current Settings Preview -->
                <div class="card bg-primary text-primary-content shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">Current Settings</h3>
                        <div class="space-y-4 sm:space-y-0 sm:grid sm:grid-cols-3 sm:gap-4 text-sm">
                            <div class="stat">
                                <div class="stat-title text-primary-content/70">Status Epilepticus</div>
                                <div class="stat-value text-lg">{{ $user->status_epilepticus_duration_minutes }}min</div>
                                <div class="stat-desc text-primary-content/70">Duration threshold</div>
                            </div>
                            <div class="stat">
                                <div class="stat-title text-primary-content/70">Cluster Emergency</div>
                                <div class="stat-value text-lg">{{ $user->emergency_seizure_count }} seizures</div>
                                <div class="stat-desc text-primary-content/70">Count threshold</div>
                            </div>
                            <div class="stat">
                                <div class="stat-title text-primary-content/70">Timeframe</div>
                                <div class="stat-value text-lg">{{ $user->emergency_seizure_timeframe_hours }}hrs</div>
                                <div class="stat-desc text-primary-content/70">Cluster window</div>
                            </div>
                        </div>
                        @if($user->emergency_contact_info)
                            <div class="mt-4 p-3 bg-primary-content/10 rounded">
                                <p class="text-sm font-semibold">✓ Emergency contacts configured</p>
                            </div>
                        @else
                            <div class="mt-4 p-3 bg-warning/20 rounded">
                                <p class="text-sm font-semibold">⚠️ No emergency contacts configured</p>
                                <p class="text-xs">Consider adding emergency contact information for safety</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex flex-col sm:flex-row items-center gap-4 pt-6">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Save Emergency Settings
                    </button>
                    <a href="{{ route('settings.profile') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </x-settings.layout>
</x-layouts.app>
