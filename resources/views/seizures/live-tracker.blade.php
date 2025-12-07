<x-layouts.app>
    <script src="//unpkg.com/dayjs@1.11.10/dayjs.min.js"></script>
    <script>
        // Check if Alpine is already loading or loaded
        if (typeof window.Alpine === 'undefined' && !document.querySelector('script[src*="alpinejs"]')) {
            const script = document.createElement('script');
            script.src = '//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js';
            script.defer = true;
            document.head.appendChild(script);
        }
    </script>


    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.3s ease-out;
        }

        .emergency-pulse {
            animation: pulse 1s infinite;
        }

        /* Ensure scrolling is not blocked by CSS */
        html, body {
            overflow: auto !important;
            position: static !important;
        }

        .container {
            overflow: visible !important;
        }
    </style>

    <div class="container mx-auto max-w-4xl p-4 space-y-6"
         x-data="seizureTracker()"
         x-init="init()">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold">Live Seizure Tracker</h1>
            <div class="flex gap-2">
                <button onclick="history.back()" class="btn btn-ghost btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </button>
                <a href="{{ route('seizures.index') }}" class="btn btn-primary btn-sm">
                    View All Seizures
                </a>
            </div>
        </div>

        <!-- Emergency Information -->
        @if(auth()->user()->emergency_contact_info || auth()->user()->status_epilepticus_duration_minutes)
        <div class="card bg-base-100 shadow-xl border border-warning">
            <div class="card-body">
                <div class="flex items-center gap-3 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <h2 class="card-title text-warning">Emergency Settings</h2>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="alert alert-error">
                        <div class="flex-col items-start">
                            <h3 class="font-semibold mb-1">⏱️ Emergency Threshold</h3>
                            <p class="text-2xl font-bold" x-text="emergencySettings.statusEpilepticusDuration + ' minutes'"></p>
                            <p class="text-sm opacity-70 mt-1">Status epilepticus duration</p>
                        </div>
                    </div>

                    <div class="alert alert-success" x-show="emergencySettings.emergencyContactInfo">
                        <div class="flex-col items-start w-full">
                            <h3 class="font-semibold mb-1">📞 Emergency Contact</h3>
                            <div class="mockup-code text-xs w-full">
                                <pre><code x-text="emergencySettings.emergencyContactInfo"></code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning" x-show="!emergencySettings.emergencyContactInfo">
                        <div class="flex-col items-start">
                            <h3 class="font-semibold mb-1">⚠️ No Emergency Contact</h3>
                            <p class="text-sm">
                                <template x-if="selectedUser.id === {{ auth()->id() }}">
                                    <a href="{{ route('settings.emergency-settings') }}" class="link">Set emergency contact info</a>
                                </template>
                                <template x-if="selectedUser.id !== {{ auth()->id() }}">
                                    <span>No emergency contact set for this user</span>
                                </template>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- User Selection -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">Select User to Track</h2>

                @if($users->count() > 0)
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">User</span>
                    </label>
                    <select x-model="selectedUserId" @change="updateUserSelection()" class="select select-bordered w-full">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ ucfirst($user->account_type) }}){{ $user->id === auth()->id() ? ' - You' : ' - Trusted Access' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <div class="alert alert-warning mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">No Patient Accounts Available</h3>
                        <div class="text-sm">Seizure tracking is only available for patient accounts. You need patient-level access to use this feature.</div>
                    </div>
                </div>
                @endif

                <div class="flex items-center gap-3 p-4 bg-base-200 rounded-lg">
                    <div class="avatar">
                        <div class="w-12 rounded-full">
                            <img :src="selectedUser.avatar_url" alt="User Avatar">
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="font-semibold" x-text="selectedUser.name"></p>
                            <div class="badge badge-primary badge-sm" x-text="selectedUser.account_type.charAt(0).toUpperCase() + selectedUser.account_type.slice(1)"></div>
                        </div>
                        <p class="text-sm opacity-70" x-text="selectedUser.email"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timer Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body text-center">
                <h2 class="card-title justify-center mb-6">Seizure Timer</h2>

                <div class="mb-8">
                    <div class="text-6xl font-mono font-bold mb-4"
                         :class="{
                             'text-primary': !isRunning && !hasEnded,
                             'text-error': isRunning && !isEmergency,
                             'text-red-600 animate-pulse': isEmergency,
                             'text-warning': hasEnded
                         }"
                         x-text="timerDisplay"></div>

                    <div class="text-lg opacity-70" x-text="timerStatus"></div>

                    <div class="mt-2 badge badge-sm badge-success gap-2" x-show="isRunning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Auto-saving timer
                    </div>

                    <div class="mt-4 alert alert-warning" x-show="showWarning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <span class="font-semibold">Approaching emergency threshold</span>
                    </div>
                </div>

                <div class="mb-4 text-center">
                    <div class="badge badge-error badge-lg" x-text="`🚨 Emergency at ${emergencySettings.statusEpilepticusDuration} minutes`"></div>
                </div>



                <div class="flex gap-4 justify-center flex-wrap">
                    <button x-on:click.prevent="startTimer()"
                            x-show="!isRunning && !hasEnded"
                            type="button"
                            class="btn btn-success btn-lg">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Start Tracking
                    </button>

                    <button x-on:click.prevent="stopTimer()"
                            x-show="isRunning"
                            type="button"
                            class="btn btn-error btn-lg">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6v4H9z"></path>
                        </svg>
                        Stop Tracking
                    </button>

                    <button x-on:click.prevent="resetTimer()"
                            type="button"
                            class="btn btn-outline btn-lg btn-warning">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset Timer
                    </button>


                </div>
            </div>
        </div>

        <!-- Emergency Modal -->
        <div x-show="showEmergencyModal"
             x-transition.opacity
             class="modal modal-open">
            <div class="modal-backdrop"></div>
            <div class="modal-box max-w-lg border-4 border-error shadow-2xl">
                <div class="text-center">
                    <div class="text-7xl mb-4 animate-bounce">🚨</div>
                    <h2 class="text-3xl font-bold text-error mb-4 uppercase tracking-wide">MEDICAL EMERGENCY</h2>

                    <div class="alert alert-error mb-4">
                        <div class="w-full">
                            <p class="text-xl font-bold mb-2">STATUS EPILEPTICUS</p>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="text-center">
                                    <p class="font-semibold mb-1">Started:</p>
                                    <p class="text-lg font-mono" x-text="formatTime(startTime) || '--:--:--'"></p>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold mb-1">Duration:</p>
                                    <p class="text-2xl font-bold font-mono animate-pulse" x-text="emergencyDurationDisplay"></p>
                                    <div class="text-xs mt-1">🔴 LIVE</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-error text-error-content p-6 rounded-lg mb-6 text-center">
                        <div class="text-3xl font-bold mb-2 animate-pulse">📞 CALL 999 NOW</div>
                        <div class="text-lg font-semibold">Emergency Services Required</div>
                    </div>

                    <div class="alert alert-warning mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div>
                            <p class="font-bold mb-1">⚠️ PROLONGED SEIZURE EMERGENCY</p>
                            <p class="text-sm">This seizure has exceeded safe duration limits and requires immediate medical intervention.</p>
                        </div>
                    </div>

                    <div x-show="emergencySettings.emergencyContactInfo" class="alert alert-info mb-4">
                        <div class="w-full">
                            <h4 class="font-semibold mb-2">📋 Emergency Contact Info:</h4>
                            <div class="bg-base-300 p-3 rounded font-mono text-sm" x-text="emergencySettings.emergencyContactInfo"></div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4">
                        <a href="tel:999" class="btn btn-error emergency-pulse btn-lg text-xl shadow-lg hover:shadow-xl hover:opacity-95 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            📞 CALL 999 EMERGENCY
                        </a>

                        <div class="text-center text-sm opacity-70 mb-2">
                            <p class="font-semibold">Tell them: "Status epilepticus - prolonged seizure emergency"</p>
                        </div>

                        <button @click="acknowledgeEmergency()" class="btn btn-outline btn-sm">
                            ✓ Emergency services called - Continue timing
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification -->
        <div x-show="notification.show"
             x-transition:enter="animate-fade-in-up"
             x-transition:leave="opacity-0 transform translate-y-[-20px]"
             class="fixed top-4 right-4 w-auto max-w-md z-50 shadow-lg"
             :class="notification.class">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="notification.icon" />
                </svg>
                <div>
                    <p class="font-semibold" x-text="notification.title"></p>
                    <p class="text-sm opacity-80" x-text="notification.message"></p>
                </div>
            </div>
        </div>

        <!-- Seizure Form -->
        <div id="seizure-form-section" class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-6">Seizure Record</h2>

                <x-seizure.record-form
                    action="{{ route('seizures.store') }}"
                    :users="$users"
                    :show-user-selection="false"
                    :time-fields-editable="true"
                    :hide-time-fields-initially="true"
                    :hide-submit-initially="true"
                    form-id="live_seizure_form"
                    submit-text="Save Seizure Record"
                />
            </div>
        </div>
    </div>

    <script>
        function seizureTracker() {
            return {
                // Timer state
                isRunning: false,
                hasEnded: false,
                startTime: null,
                elapsedSeconds: 0,
                timerInterval: null,
                emergencyAlertShown: false,

                // UI state
                showEmergencyModal: false,
                showWarning: false,
                hasAutoScrolled: false,

                // User data
                selectedUserId: {{ auth()->id() }},
                selectedUser: {},
                usersData: @json($usersData),

                // Emergency settings
                emergencySettings: {
                    statusEpilepticusDuration: {{ auth()->user()->status_epilepticus_duration_minutes ?? 5 }},
                    emergencyContactInfo: @json(auth()->user()->emergency_contact_info)
                },

                // Notification system
                notification: {
                    show: false,
                    title: '',
                    message: '',
                    class: 'alert alert-info',
                    icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                },

                // Storage keys
                TIMER_STORAGE_KEY: 'seizure_timer_state',
                FORM_DATA_STORAGE_KEY: 'seizure_form_data',

                // Computed properties
                get timerDisplay() {
                    const hours = Math.floor(this.elapsedSeconds / 3600);
                    const minutes = Math.floor((this.elapsedSeconds % 3600) / 60);
                    const seconds = this.elapsedSeconds % 60;

                    return [
                        hours.toString().padStart(2, '0'),
                        minutes.toString().padStart(2, '0'),
                        seconds.toString().padStart(2, '0')
                    ].join(':');
                },

                get timerStatus() {
                    if (this.isRunning) return 'Seizure in progress...';
                    if (this.hasEnded) return 'Seizure ended - Complete the form below';
                    return 'Ready to start tracking';
                },

                get elapsedMinutes() {
                    return Math.floor(this.elapsedSeconds / 60);
                },

                get isEmergency() {
                    return this.isRunning && this.elapsedMinutes >= this.emergencySettings.statusEpilepticusDuration;
                },

                get emergencyDurationDisplay() {
                    if (!this.isRunning || !this.startTime || this.elapsedSeconds < 0) return '--:--:--';

                    const currentMinutes = this.elapsedMinutes;
                    const currentSeconds = this.elapsedSeconds % 60;

                    if (currentMinutes >= 60) {
                        const hours = Math.floor(currentMinutes / 60);
                        const mins = currentMinutes % 60;
                        return `${hours}h ${mins}m ${currentSeconds.toString().padStart(2, '0')}s`;
                    }
                    return `${currentMinutes}m ${currentSeconds.toString().padStart(2, '0')}s`;
                },

                // Initialize
                init() {
                    this.updateUserSelection();
                    this.loadTimerState();

                    // Save state on page unload
                    window.addEventListener('beforeunload', () => {
                        if (this.isRunning) this.saveTimerState();
                    });

                    // Handle visibility changes
                    document.addEventListener('visibilitychange', () => {
                        if (this.isRunning) {
                            if (document.hidden) {
                                this.saveTimerState();
                            } else {
                                this.updateTimerDisplay();
                                this.loadFormData();
                            }
                        }
                    });



                    // Watch for form changes to auto-save
                    this.$watch('isRunning', () => {
                        if (this.isRunning) {
                            this.startAutoSave();
                        } else {
                            this.stopAutoSave();
                        }
                    });
                },

                // Timer functions
                startTimer() {
                    if (this.isRunning) return;

                    // Safety check: Ensure selected user is a patient
                    if (this.selectedUser.account_type !== 'patient') {
                        alert('Seizure tracking is only available for patient accounts.');
                        return;
                    }

                    this.startTime = dayjs().toDate();
                    this.isRunning = true;
                    this.hasEnded = false;
                    this.elapsedSeconds = 0;
                    this.emergencyAlertShown = false;

                    // Save timer state to local storage
                    this.saveTimerState();
                    this.startTimerInterval();
                },

                stopTimer() {
                    if (!this.isRunning) return;

                    this.isRunning = false;
                    this.hasEnded = true;
                    this.hasAutoScrolled = false;

                    this.clearTimerState();
                    this.stopTimerInterval();
                    this.showSeizureForm();
                },

                resetTimer() {
                    // Show confirmation dialog
                    const confirmMessage = this.isRunning
                        ? 'Are you sure you want to reset the timer? This will stop the current seizure tracking and clear all data.'
                        : 'Are you sure you want to reset? This will clear all form data.';

                    if (!confirm(confirmMessage)) {
                        return; // User cancelled
                    }

                    this.isRunning = false;
                    this.hasEnded = false;
                    this.startTime = null;
                    this.elapsedSeconds = 0;
                    this.emergencyAlertShown = false;
                    this.showEmergencyModal = false;
                    this.showWarning = false;
                    this.hasAutoScrolled = false;

                    // Clear timer state from local storage
                    this.clearTimerState();
                    this.stopTimerInterval();
                    this.resetForm();
                    this.hideTimeFieldsAndSubmit();

                    this.showNotification('success', 'Timer Reset', 'All timer and form data cleared.', 3000);
                },

                startTimerInterval() {
                    if (this.timerInterval) clearInterval(this.timerInterval);
                    this.timerInterval = setInterval(() => {
                        this.updateTimerDisplay();
                    }, 1000);
                    this.updateTimerDisplay();
                },

                stopTimerInterval() {
                    if (this.timerInterval) {
                        clearInterval(this.timerInterval);
                        this.timerInterval = null;
                    }
                },

                updateTimerDisplay() {
                    if (!this.startTime || !this.isRunning) return;

                    const now = dayjs().toDate();
                    this.elapsedSeconds = dayjs(now).diff(dayjs(this.startTime), 'second');

                    // Show warning 1 minute before emergency
                    const warningThreshold = this.emergencySettings.statusEpilepticusDuration - 1;
                    this.showWarning = this.elapsedMinutes >= warningThreshold &&
                                      this.elapsedMinutes < this.emergencySettings.statusEpilepticusDuration;

                    // Check for emergency
                    if (!this.emergencyAlertShown && this.isEmergency) {
                        this.showEmergencyAlert();
                        this.emergencyAlertShown = true;
                        this.showWarning = false;
                    }

                    // Save state periodically
                    if (this.isRunning) {
                        this.saveTimerState();
                    }
                },

                // Emergency handling
                showEmergencyAlert() {
                    this.showEmergencyModal = true;
                    this.playEmergencyAlerts();
                },

                acknowledgeEmergency() {
                    this.showEmergencyModal = false;
                },

                playEmergencyAlerts() {
                    const playBeep = () => {
                        try {
                            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                            const oscillator = audioContext.createOscillator();
                            const gainNode = audioContext.createGain();

                            oscillator.connect(gainNode);
                            gainNode.connect(audioContext.destination);

                            oscillator.frequency.value = 800;
                            oscillator.type = 'sine';

                            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);

                            oscillator.start(audioContext.currentTime);
                            oscillator.stop(audioContext.currentTime + 0.5);
                        } catch (e) {
                            console.log('Audio context not available');
                        }
                    };

                    playBeep();
                    setTimeout(playBeep, 600);
                    setTimeout(playBeep, 1200);
                },

                // User management
                updateUserSelection() {
                    const user = this.usersData.find(u => u.id === parseInt(this.selectedUserId));
                    if (user) {
                        this.selectedUser = user;

                        // Update emergency settings
                        this.emergencySettings = {
                            statusEpilepticusDuration: user.status_epilepticus_duration_minutes || 5,
                            emergencyContactInfo: user.emergency_contact_info || null
                        };

                        // Update form user id
                        const formUserIdField = document.getElementById('form_user_id');
                        if (formUserIdField) {
                            formUserIdField.value = user.id;
                        }

                        // Reset emergency alert state
                        this.emergencyAlertShown = false;

                        // Reset timer if running for different user
                        if (this.isRunning) {
                            this.resetTimer();
                        }
                    }
                },

                // Storage functions
                saveTimerState() {
                    if (!this.isRunning || !this.startTime) return;

                    try {
                        const timerState = {
                            isRunning: this.isRunning,
                            startTime: this.startTime ? dayjs(this.startTime).toISOString() : null,
                            userId: parseInt(this.selectedUserId),
                            emergencyAlertShown: this.emergencyAlertShown,
                            emergencySettings: this.emergencySettings,
                            savedAt: dayjs().toISOString()
                        };

                        localStorage.setItem(this.TIMER_STORAGE_KEY, JSON.stringify(timerState));
                        this.saveFormData();
                    } catch (e) {
                        console.error('Failed to save timer state:', e);
                    }
                },

                loadTimerState() {
                    try {
                        const savedState = localStorage.getItem(this.TIMER_STORAGE_KEY);
                        if (!savedState) return false;

                        const state = JSON.parse(savedState);
                        const currentUserId = parseInt(this.selectedUserId);

                        if (!state.isRunning || !state.startTime || state.userId !== currentUserId) {
                            this.clearTimerState();
                            this.showNotification('warning', 'Timer Reset', 'User mismatch or invalid state', 7000);
                            return false;
                        }

                        try {
                            const savedStartTime = dayjs(state.startTime);
                            const now = dayjs();

                            if (!savedStartTime.isValid()) {
                                this.clearTimerState();
                                this.showNotification('warning', 'Timer Reset', 'Invalid start time data', 7000);
                                return false;
                            }

                            const hoursSinceStart = now.diff(savedStartTime, 'hour', true);

                            if (hoursSinceStart > 24 || savedStartTime.isAfter(now)) {
                                this.clearTimerState();
                                this.showNotification('warning', 'Timer Reset', 'Timer data is too old or invalid', 7000);
                                return false;
                            }

                            // Restore state
                            this.startTime = savedStartTime.toDate();
                        this.isRunning = state.isRunning;
                        this.emergencyAlertShown = state.emergencyAlertShown || false;

                        if (state.emergencySettings) {
                            this.emergencySettings = state.emergencySettings;
                        }

                            this.elapsedSeconds = now.diff(savedStartTime, 'second');

                            if (this.elapsedSeconds < 0) {
                                this.clearTimerState();
                                this.showNotification('warning', 'Timer Reset', 'Invalid timer calculation', 7000);
                                return false;
                            }
                        } catch (e) {
                            console.error('Error parsing saved start time:', e);
                            this.clearTimerState();
                            this.showNotification('warning', 'Timer Reset', 'Failed to restore timer data', 7000);
                            return false;
                        }

                        if (this.isRunning) {
                            this.startTimerInterval();
                            this.loadFormData();

                            // Check if emergency alert should be shown
                            if (!this.emergencyAlertShown && this.isEmergency) {
                                this.showEmergencyAlert();
                                this.emergencyAlertShown = true;
                            }

                            this.showNotification('info', 'Timer Restored', `Seizure timer restored (${this.formatDuration(this.elapsedSeconds)} elapsed).`, 5000);
                        }

                        return true;
                    } catch (e) {
                        console.error('Failed to load timer state:', e);
                        this.clearTimerState();
                        this.showNotification('warning', 'Timer Reset', 'Failed to restore timer data', 7000);
                        return false;
                    }
                },

                clearTimerState() {
                    try {
                        localStorage.removeItem(this.TIMER_STORAGE_KEY);
                        this.clearFormData();
                    } catch (e) {
                        console.error('Failed to clear timer state:', e);
                    }
                },

                saveFormData() {
                    try {
                        const formData = {
                            severity: this.getCurrentSeverity(),
                            onPeriod: document.getElementById('on_period')?.checked || false,
                            ambulanceCalled: document.getElementById('ambulance_called')?.checked || false,
                            sleptAfter: document.getElementById('slept_after')?.checked || false,
                            nhsContact: document.getElementById('nhs_contact')?.value || '',
                            postictalEndTime: document.getElementById('postictal_end_time')?.value || '',
                            notes: document.getElementById('notes')?.value || '',
                            startTimeInput: document.getElementById('start_time_input')?.value || '',
                            endTimeInput: document.getElementById('end_time_input')?.value || '',
                            durationInput: document.getElementById('duration_input')?.value || ''
                        };

                        localStorage.setItem(this.FORM_DATA_STORAGE_KEY, JSON.stringify(formData));
                    } catch (e) {
                        console.error('Failed to save form data:', e);
                    }
                },

                loadFormData() {
                    try {
                        const savedFormData = localStorage.getItem(this.FORM_DATA_STORAGE_KEY);
                        if (!savedFormData) return;

                        const formData = JSON.parse(savedFormData);

                        // Restore form field values
                        if (formData.severity !== undefined) {
                            this.setSeverity(formData.severity);
                        }

                        if (document.getElementById('on_period')) {
                            document.getElementById('on_period').checked = formData.onPeriod;
                        }

                        if (document.getElementById('ambulance_called')) {
                            document.getElementById('ambulance_called').checked = formData.ambulanceCalled;
                        }

                        if (document.getElementById('slept_after')) {
                            document.getElementById('slept_after').checked = formData.sleptAfter;
                        }

                        if (document.getElementById('nhs_contact')) {
                            document.getElementById('nhs_contact').value = formData.nhsContact;
                        }

                        if (document.getElementById('postictal_end_time')) {
                            document.getElementById('postictal_end_time').value = formData.postictalEndTime;
                        }

                        if (document.getElementById('notes')) {
                            document.getElementById('notes').value = formData.notes;
                        }

                        // Restore time inputs
                        if (document.getElementById('start_time_input')) {
                            document.getElementById('start_time_input').value = formData.startTimeInput;
                        }

                        if (document.getElementById('end_time_input')) {
                            document.getElementById('end_time_input').value = formData.endTimeInput;
                        }

                        if (document.getElementById('duration_input')) {
                            document.getElementById('duration_input').value = formData.durationInput;
                        }

                    } catch (e) {
                        console.error('Failed to load form data:', e);
                    }
                },

                getCurrentSeverity() {
                    const severityInput = document.getElementById('severity_input');
                    return severityInput ? parseInt(severityInput.value) || 5 : 5;
                },

                setSeverity(severity) {
                    const severityInput = document.getElementById('severity_input');
                    if (severityInput) {
                        severityInput.value = severity;
                    }
                },

                clearFormData() {
                    try {
                        localStorage.removeItem(this.FORM_DATA_STORAGE_KEY);
                    } catch (e) {
                        console.error('Failed to clear form data:', e);
                    }
                },

                // Form functions
                showSeizureForm() {
                    const endTime = dayjs().toDate();
                    const durationMinutes = Math.floor(this.elapsedSeconds / 60);

                    const formatForInput = (date) => {
                        if (!date) return '';
                        try {
                            return dayjs(date).format('YYYY-MM-DDTHH:mm');
                        } catch (e) {
                            console.error('Error formatting date for input:', e);
                            return '';
                        }
                    };

                    // Update form fields
                    const startTimeInput = document.getElementById('start_time_input');
                    const endTimeInput = document.getElementById('end_time_input');
                    const durationInput = document.getElementById('duration_input');

                    if (startTimeInput) startTimeInput.value = formatForInput(this.startTime);
                    if (endTimeInput) endTimeInput.value = formatForInput(endTime);
                    if (durationInput) durationInput.value = durationMinutes;

                    this.showTimeFieldsAndSubmit();

                    // Only scroll once when form is first shown
                    if (!this.hasAutoScrolled) {
                        this.scrollToForm();
                        this.hasAutoScrolled = true;
                    }
                },

                resetForm() {
                    try {
                        // Reset form completely
                        const form = document.getElementById('live_seizure_form');
                        if (form) form.reset();

                        // Clear specific inputs
                        const timeInputs = ['start_time_input', 'end_time_input', 'duration_input'];
                        timeInputs.forEach(id => {
                            const input = document.getElementById(id);
                            if (input) input.value = '';
                        });

                        // Set severity back to default
                        this.setSeverity(5);

                        // Clear all checkboxes
                        ['on_period', 'ambulance_called', 'slept_after'].forEach(id => {
                            const checkbox = document.getElementById(id);
                            if (checkbox) checkbox.checked = false;
                        });

                        // Clear text areas and selects
                        const textFields = ['notes', 'nhs_contact', 'postictal_end_time'];
                        textFields.forEach(id => {
                            const field = document.getElementById(id);
                            if (field) field.value = '';
                        });

                    } catch (error) {
                        console.error('Error resetting form:', error);
                    }
                },

                showTimeFieldsAndSubmit() {
                    const timeContainer = document.getElementById('time-fields-container');
                    const submitButton = document.getElementById('submit-button');

                    if (timeContainer) timeContainer.style.display = 'grid';
                    if (submitButton) {
                        submitButton.style.display = 'inline-flex';
                        submitButton.disabled = false;
                    }
                },

                hideTimeFieldsAndSubmit() {
                    const timeContainer = document.getElementById('time-fields-container');
                    const submitButton = document.getElementById('submit-button');

                    if (timeContainer) timeContainer.style.display = 'none';
                    if (submitButton) {
                        submitButton.style.display = 'none';
                        submitButton.disabled = true;
                    }
                },

                // Auto-save functionality
                startAutoSave() {
                    // Add event listeners for form changes when timer is running
                    document.addEventListener('change', this.handleFormChange);
                    document.addEventListener('input', this.handleFormInput);
                },

                stopAutoSave() {
                    document.removeEventListener('change', this.handleFormChange);
                    document.removeEventListener('input', this.handleFormInput);
                },

                handleFormChange(e) {
                    if (this.isRunning && e.target.closest('#live_seizure_form')) {
                        this.saveFormData();
                    }
                },

                handleFormInput(e) {
                    if (this.isRunning && e.target.closest('#live_seizure_form') && e.target.type === 'textarea') {
                        clearTimeout(this.formSaveTimeout);
                        this.formSaveTimeout = setTimeout(() => {
                            this.saveFormData();
                        }, 500);
                    }
                },

                // Notification system
                showNotification(type, title, message, duration = 3000) {
                    const classes = {
                        success: 'alert alert-success',
                        info: 'alert alert-info',
                        warning: 'alert alert-warning',
                        error: 'alert alert-error'
                    };

                    const icons = {
                        success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z',
                        error: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
                    };

                    this.notification = {
                        show: true,
                        title,
                        message,
                        class: classes[type] || classes.info,
                        icon: icons[type] || icons.info
                    };

                    setTimeout(() => {
                        this.notification.show = false;
                    }, duration);
                },

                // Utility functions
                formatTime(date) {
                    if (!date) return '';
                    try {
                        return dayjs(date).format('HH:mm:ss');
                    } catch (e) {
                        console.error('Error formatting time:', e);
                        return '--:--:--';
                    }
                },

                formatDuration(seconds) {
                    if (seconds < 0 || !Number.isFinite(seconds)) return '0m';

                    const minutes = Math.floor(seconds / 60);
                    const hours = Math.floor(minutes / 60);

                    if (hours > 0) {
                        return `${hours}h ${minutes % 60}m`;
                    }
                    return `${minutes}m`;
                },



                // Extracted scroll function for reuse
                scrollToForm() {
                    setTimeout(() => {
                        let scrollTarget = null;

                        // Try to find the actual form element
                        scrollTarget = document.getElementById('live_seizure_form');

                        // Fallback 1: Look for seizure form section
                        if (!scrollTarget) {
                            scrollTarget = document.getElementById('seizure-form-section');
                        }

                        // Fallback 2: Look for the card containing "Seizure Record"
                        if (!scrollTarget) {
                            const headings = document.querySelectorAll('h2');
                            for (const heading of headings) {
                                if (heading.textContent.includes('Seizure Record')) {
                                    scrollTarget = heading.closest('.card');
                                    break;
                                }
                            }
                        }

                        // Fallback 3: Look for any form on the page
                        if (!scrollTarget) {
                            scrollTarget = document.querySelector('form');
                        }

                        // Execute scroll with error handling
                        if (scrollTarget) {
                            try {
                                scrollTarget.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start',
                                    inline: 'nearest'
                                });
                            } catch (e) {
                                // Fallback: simple scroll
                                scrollTarget.scrollIntoView();
                            }
                        }
                    }, 100);
                }
            }
        }
    </script>
</x-layouts.app>
