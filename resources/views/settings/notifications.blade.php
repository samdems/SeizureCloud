<x-layouts.app :title="__('Notification Settings')">
    <x-settings.layout>
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Notification Settings</h2>
            <p class="text-base-content/70 mt-2">Manage when and how you receive notifications about medications and seizures</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="max-w-2xl">
            <form action="{{ route('settings.notifications.update') }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Personal Notifications Section -->
                <div class="card bg-base-200 border border-base-300">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            Personal Notifications
                        </h3>
                        <p class="text-sm text-base-content/70 mb-6">Notifications sent to you about your own activities</p>

                        <div class="space-y-4">
                            <!-- Medication Notifications -->
                            <div class="form-control">
                                <label class="label cursor-pointer justify-start">
                                    <input
                                        type="checkbox"
                                        name="notify_medication_taken"
                                        value="1"
                                        class="checkbox checkbox-primary mr-4"
                                        {{ $user->notify_medication_taken ? 'checked' : '' }}
                                    />
                                    <div class="flex flex-col">
                                        <span class="label-text font-semibold">Medication Taken Confirmations</span>
                                        <span class="label-text-alt text-base-content/60">Receive notifications when you take your medications</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Seizure Notifications -->
                            <div class="form-control">
                                <label class="label cursor-pointer justify-start">
                                    <input
                                        type="checkbox"
                                        name="notify_seizure_added"
                                        value="1"
                                        class="checkbox checkbox-primary mr-4"
                                        {{ $user->notify_seizure_added ? 'checked' : '' }}
                                    />
                                    <div class="flex flex-col">
                                        <span class="label-text font-semibold">Seizure Recording Confirmations</span>
                                        <span class="label-text-alt text-base-content/60">Receive notifications when seizures are recorded</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trusted Contact Notifications Section -->
                <div class="card bg-base-200 border border-base-300">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Trusted Contact Notifications
                        </h3>
                        <p class="text-sm text-base-content/70 mb-6">Notifications sent to your trusted contacts about your activities</p>

                        <div class="space-y-4">
                            <!-- Medication Notifications to Trusted Contacts -->
                            <div class="form-control">
                                <label class="label cursor-pointer justify-start">
                                    <input
                                        type="checkbox"
                                        name="notify_trusted_contacts_medication"
                                        value="1"
                                        class="checkbox checkbox-secondary mr-4"
                                        {{ $user->notify_trusted_contacts_medication ? 'checked' : '' }}
                                    />
                                    <div class="flex flex-col">
                                        <span class="label-text font-semibold">Notify Trusted Contacts - Medications</span>
                                        <span class="label-text-alt text-base-content/60">Send medication notifications to your trusted contacts</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Seizure Notifications to Trusted Contacts -->
                            <div class="form-control">
                                <label class="label cursor-pointer justify-start">
                                    <input
                                        type="checkbox"
                                        name="notify_trusted_contacts_seizures"
                                        value="1"
                                        class="checkbox checkbox-secondary mr-4"
                                        {{ $user->notify_trusted_contacts_seizures ? 'checked' : '' }}
                                    />
                                    <div class="flex flex-col">
                                        <span class="label-text font-semibold">Notify Trusted Contacts - Seizures</span>
                                        <span class="label-text-alt text-base-content/60">Send seizure notifications to your trusted contacts</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        @if($user->trustedUsers()->count() > 0)
                            <div class="mt-6 p-4 bg-base-300/50 rounded-lg">
                                <h4 class="font-semibold text-sm mb-2">Your Trusted Contacts:</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($user->trustedUsers as $trustedUser)
                                        <div class="badge badge-outline">
                                            {{ $trustedUser->pivot->nickname ?? $trustedUser->name }}
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-base-content/60 mt-2">
                                    Notifications will be sent to these contacts when enabled above.
                                </p>
                            </div>
                        @else
                            <div class="mt-6 p-4 bg-warning/10 border border-warning/30 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-warning">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    <span class="font-semibold text-sm">No Trusted Contacts</span>
                                </div>
                                <p class="text-xs text-base-content/70 mb-3">
                                    You haven't set up any trusted contacts yet. Trusted contact notifications will only work when you have trusted contacts configured.
                                </p>
                                <a href="{{ route('settings.trusted-contacts.index') }}" class="btn btn-warning btn-sm">
                                    Set Up Trusted Contacts
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="card bg-info/10 border border-info/30">
                    <div class="card-body">
                        <h3 class="card-title text-info text-lg mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                            Important Information
                        </h3>
                        <div class="space-y-2 text-sm">
                            <p>• Notifications are sent via email and stored in your account</p>
                            <p>• Emergency seizures will always trigger notifications regardless of settings</p>
                            <p>• Trusted contacts will only receive notifications if they have valid, active access</p>
                            <p>• You can change these settings at any time</p>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Save Notification Settings
                    </button>
                </div>
            </form>
        </div>
    </x-settings.layout>
</x-layouts.app>
