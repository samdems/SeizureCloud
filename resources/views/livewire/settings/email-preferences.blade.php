<?php

use Livewire\Volt\Component;

new class extends Component
{
    public $email_frequency_medication;

    public $email_frequency_seizures;

    public $email_digest_enabled;

    public $email_digest_frequency;

    public $email_digest_day;

    public $email_digest_time;

    public $email_include_charts;

    public $email_include_summaries;

    public $email_marketing_enabled;

    public $email_medication_reminders;

    public $email_appointment_reminders;

    public $email_system_updates;

    public function mount()
    {
        $user = auth()->user();
        $this->email_frequency_medication = $user->email_frequency_medication;
        $this->email_frequency_seizures = $user->email_frequency_seizures;
        $this->email_digest_enabled = $user->email_digest_enabled;
        $this->email_digest_frequency = $user->email_digest_frequency;
        $this->email_digest_day = $user->email_digest_day;
        $this->email_digest_time = $user->email_digest_time?->format('H:i') ?? '08:00';
        $this->email_include_charts = $user->email_include_charts;
        $this->email_include_summaries = $user->email_include_summaries;
        $this->email_marketing_enabled = $user->email_marketing_enabled;
        $this->email_medication_reminders = $user->email_medication_reminders;
        $this->email_appointment_reminders = $user->email_appointment_reminders;
        $this->email_system_updates = $user->email_system_updates;
    }

    public function saveEmailPreferences()
    {
        $this->validate([
            'email_frequency_medication' => 'required|in:immediate,daily,weekly,never',
            'email_frequency_seizures' => 'required|in:immediate,daily,weekly,never',
            'email_digest_enabled' => 'boolean',
            'email_digest_frequency' => 'required|in:daily,weekly',
            'email_digest_day' => 'required|integer|min:1|max:7',
            'email_digest_time' => 'required|date_format:H:i',
            'email_include_charts' => 'boolean',
            'email_include_summaries' => 'boolean',
            'email_marketing_enabled' => 'boolean',
            'email_medication_reminders' => 'boolean',
            'email_appointment_reminders' => 'boolean',
            'email_system_updates' => 'boolean',
        ]);

        auth()->user()->update([
            'email_frequency_medication' => $this->email_frequency_medication,
            'email_frequency_seizures' => $this->email_frequency_seizures,
            'email_digest_enabled' => $this->email_digest_enabled,
            'email_digest_frequency' => $this->email_digest_frequency,
            'email_digest_day' => $this->email_digest_day,
            'email_digest_time' => $this->email_digest_time,
            'email_include_charts' => $this->email_include_charts,
            'email_include_summaries' => $this->email_include_summaries,
            'email_marketing_enabled' => $this->email_marketing_enabled,
            'email_medication_reminders' => $this->email_medication_reminders,
            'email_appointment_reminders' => $this->email_appointment_reminders,
            'email_system_updates' => $this->email_system_updates,
        ]);

        session()->flash('success', 'Email preferences updated successfully!');
    }

    public function getDayName($day)
    {
        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];

        return $days[$day] ?? 'Monday';
    }
}; ?>

<div class="card bg-base-200 border border-base-300">
    <div class="card-body">
        <h3 class="card-title text-lg mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
            </svg>
            Email Preferences
        </h3>
        <p class="text-sm text-base-content/70 mb-6">Control how and when you receive emails about your health activities</p>

        <!-- Email Frequency Section -->
        <div class="space-y-6">
            <div>
                <h4 class="font-semibold text-sm mb-3 text-base-content/90">Email Frequency</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Medication Email Frequency -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Medication Notifications</span>
                        </label>
                        <select wire:model="email_frequency_medication" class="select select-bordered">
                            <option value="immediate">Immediately</option>
                            <option value="daily">Daily Summary</option>
                            <option value="weekly">Weekly Summary</option>
                            <option value="never">Never</option>
                        </select>
                    </div>

                    <!-- Seizure Email Frequency -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Seizure Notifications</span>
                        </label>
                        <select wire:model="email_frequency_seizures" class="select select-bordered">
                            <option value="immediate">Immediately</option>
                            <option value="daily">Daily Summary</option>
                            <option value="weekly">Weekly Summary</option>
                            <option value="never">Never</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Email Digest Section -->
            <div class="border-t border-base-300 pt-6">
                <div class="form-control">
                    <label class="label cursor-pointer justify-start">
                        <input 
                            type="checkbox" 
                            wire:model.live="email_digest_enabled" 
                            class="checkbox checkbox-primary mr-4"
                        />
                        <div class="flex flex-col">
                            <span class="label-text font-semibold">Enable Email Digest</span>
                            <span class="label-text-alt text-base-content/60">Receive regular summaries of your health activities</span>
                        </div>
                    </label>
                </div>

                @if($email_digest_enabled)
                    <div class="ml-8 mt-4 space-y-4 border-l-2 border-primary/20 pl-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Digest Frequency -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Frequency</span>
                                </label>
                                <select wire:model="email_digest_frequency" class="select select-bordered">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                </select>
                            </div>

                            <!-- Digest Day (for weekly) -->
                            @if($email_digest_frequency === 'weekly')
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Day of Week</span>
                                    </label>
                                    <select wire:model="email_digest_day" class="select select-bordered">
                                        <option value="1">Monday</option>
                                        <option value="2">Tuesday</option>
                                        <option value="3">Wednesday</option>
                                        <option value="4">Thursday</option>
                                        <option value="5">Friday</option>
                                        <option value="6">Saturday</option>
                                        <option value="7">Sunday</option>
                                    </select>
                                </div>
                            @endif

                            <!-- Digest Time -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Time</span>
                                </label>
                                <input 
                                    type="time" 
                                    wire:model="email_digest_time" 
                                    class="input input-bordered"
                                />
                            </div>
                        </div>

                        <!-- Digest Content Options -->
                        <div>
                            <h5 class="font-medium text-sm mb-3">Digest Content</h5>
                            <div class="space-y-2">
                                <div class="form-control">
                                    <label class="label cursor-pointer justify-start">
                                        <input 
                                            type="checkbox" 
                                            wire:model="email_include_charts" 
                                            class="checkbox checkbox-primary mr-3"
                                        />
                                        <span class="label-text">Include charts and graphs</span>
                                    </label>
                                </div>
                                <div class="form-control">
                                    <label class="label cursor-pointer justify-start">
                                        <input 
                                            type="checkbox" 
                                            wire:model="email_include_summaries" 
                                            class="checkbox checkbox-primary mr-3"
                                        />
                                        <span class="label-text">Include detailed summaries</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Additional Email Settings -->
            <div class="border-t border-base-300 pt-6">
                <h4 class="font-semibold text-sm mb-4 text-base-content/90">Additional Email Settings</h4>
                <div class="space-y-3">
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start">
                            <input 
                                type="checkbox" 
                                wire:model="email_medication_reminders" 
                                class="checkbox checkbox-primary mr-4"
                            />
                            <div class="flex flex-col">
                                <span class="label-text font-medium">Medication Reminders</span>
                                <span class="label-text-alt text-base-content/60">Receive email reminders for scheduled medications</span>
                            </div>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start">
                            <input 
                                type="checkbox" 
                                wire:model="email_appointment_reminders" 
                                class="checkbox checkbox-primary mr-4"
                            />
                            <div class="flex flex-col">
                                <span class="label-text font-medium">Appointment Reminders</span>
                                <span class="label-text-alt text-base-content/60">Receive email reminders for upcoming appointments</span>
                            </div>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start">
                            <input 
                                type="checkbox" 
                                wire:model="email_system_updates" 
                                class="checkbox checkbox-primary mr-4"
                            />
                            <div class="flex flex-col">
                                <span class="label-text font-medium">System Updates</span>
                                <span class="label-text-alt text-base-content/60">Receive emails about new features and important updates</span>
                            </div>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start">
                            <input 
                                type="checkbox" 
                                wire:model="email_marketing_enabled" 
                                class="checkbox checkbox-secondary mr-4"
                            />
                            <div class="flex flex-col">
                                <span class="label-text font-medium">Marketing Communications</span>
                                <span class="label-text-alt text-base-content/60">Receive emails about health tips and product updates</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="border-t border-base-300 pt-6">
                <button 
                    wire:click="saveEmailPreferences" 
                    wire:loading.attr="disabled"
                    class="btn btn-primary"
                >
                    <span wire:loading.remove>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Save Email Preferences
                    </span>
                    <span wire:loading class="loading loading-spinner loading-sm"></span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </div>
    </div>
</div>
