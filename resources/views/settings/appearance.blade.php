<x-layouts.app :title="__('Appearance Settings')">
    <x-settings.layout>
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Appearance</h2>
            <p class="text-base-content/70 mt-2">Customize how the application looks on your device</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="max-w-4xl">
            <div class="alert alert-info mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Choose your preferred theme. System will automatically match your device settings.</span>
            </div>

            <form action="{{ route('settings.appearance.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold text-lg">Theme Preference</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Light Mode -->
                    <label class="cursor-pointer">
                        <input type="radio" name="appearance" value="light" class="sr-only" {{ session('appearance', 'system') == 'light' ? 'checked' : '' }}>
                        <div class="card bg-base-100 border-2 hover:border-primary transition-all theme-card {{ session('appearance', 'system') == 'light' ? 'border-primary ring-2 ring-primary ring-opacity-20' : 'border-base-300' }}">
                            <div class="card-body items-center text-center p-6">
                                <div class="rounded-full bg-warning p-4 mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                    </svg>
                                </div>
                                <h3 class="card-title text-lg">Light</h3>
                                <p class="text-sm opacity-70">Bright and clear theme</p>
                                @if(session('appearance', 'system') == 'light')
                                    <div class="badge badge-primary gap-2 mt-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        Active
                                    </div>
                                @endif
                            </div>
                        </div>
                    </label>

                    <!-- Dark Mode -->
                    <label class="cursor-pointer">
                        <input type="radio" name="appearance" value="dark" class="sr-only" {{ session('appearance', 'system') == 'dark' ? 'checked' : '' }}>
                        <div class="card bg-base-100 border-2 hover:border-primary transition-all theme-card {{ session('appearance', 'system') == 'dark' ? 'border-primary ring-2 ring-primary ring-opacity-20' : 'border-base-300' }}">
                            <div class="card-body items-center text-center p-6">
                                <div class="rounded-full bg-primary p-4 mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-primary-content">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                    </svg>
                                </div>
                                <h3 class="card-title text-lg">Dark</h3>
                                <p class="text-sm opacity-70">Easy on the eyes</p>
                                @if(session('appearance', 'system') == 'dark')
                                    <div class="badge badge-primary gap-2 mt-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        Active
                                    </div>
                                @endif
                            </div>
                        </div>
                    </label>

                    <!-- System Mode -->
                    <label class="cursor-pointer">
                        <input type="radio" name="appearance" value="system" class="sr-only" {{ session('appearance', 'system') == 'system' ? 'checked' : '' }}>
                        <div class="card bg-base-100 border-2 hover:border-primary transition-all theme-card {{ session('appearance', 'system') == 'system' ? 'border-primary ring-2 ring-primary ring-opacity-20' : 'border-base-300' }}">
                            <div class="card-body items-center text-center p-6">
                                <div class="rounded-full bg-info p-4 mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-info-content">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                    </svg>
                                </div>
                                <h3 class="card-title text-lg">System</h3>
                                <p class="text-sm opacity-70">Match device settings</p>
                                @if(session('appearance', 'system') == 'system')
                                    <div class="badge badge-primary gap-2 mt-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        Active
                                    </div>
                                @endif
                            </div>
                        </div>
                    </label>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="btn btn-primary" id="save-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span id="save-text">Save Appearance Settings</span>
                        <span id="loading-text" class="loading loading-spinner loading-sm hidden"></span>
                    </button>
                </div>
            </form>
        </div>

        <style>
            .theme-card:has(input:checked) {
                border-color: oklch(var(--p));
                box-shadow: 0 0 0 2px oklch(var(--p) / 0.2);
            }
        </style>

        <script>
            // Add click handlers to make the entire card clickable
            document.querySelectorAll('.theme-card').forEach(card => {
                card.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio && !radio.checked) {
                        radio.checked = true;

                        // Update visual states
                        document.querySelectorAll('.theme-card').forEach(c => {
                            c.classList.remove('border-primary');
                            c.classList.add('border-base-300');
                            c.style.boxShadow = '';
                        });

                        this.classList.remove('border-base-300');
                        this.classList.add('border-primary');
                        this.style.boxShadow = '0 0 0 2px oklch(var(--p) / 0.2)';

                        // Show loading state
                        const saveBtn = document.getElementById('save-btn');
                        const saveText = document.getElementById('save-text');
                        const loadingSpinner = document.getElementById('loading-text');

                        saveBtn.disabled = true;
                        saveBtn.classList.add('loading');
                        saveText.textContent = 'Applying...';
                        loadingSpinner.classList.remove('hidden');

                        // Auto-submit the form
                        this.closest('form').submit();
                    }
                });
            });

            // Handle form submission
            document.querySelector('form').addEventListener('submit', function() {
                const saveBtn = document.getElementById('save-btn');
                const saveText = document.getElementById('save-text');
                const loadingSpinner = document.getElementById('loading-text');

                saveBtn.disabled = true;
                saveBtn.classList.add('loading');
                saveText.textContent = 'Saving...';
                loadingSpinner.classList.remove('hidden');
            });
        </script>
    </x-settings.layout>
</x-layouts.app>
