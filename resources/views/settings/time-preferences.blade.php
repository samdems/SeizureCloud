<x-layouts.app :title="__('Time Preferences')">
    <x-settings.layout>
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Time Preferences</h2>
            <p class="text-base-content/70 mt-2">Set your preferred times for different parts of the day</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="max-w-2xl">
            <form action="{{ route('settings.time-preferences.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>These times will be used to organize your medication schedule throughout the day.</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-control">
                    <label for="morning_time" class="label">
                        <span class="label-text font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                            Morning Time
                        </span>
                    </label>
                    <input type="time" id="morning_time" name="morning_time"
                        value="{{ old('morning_time', $user->morning_time?->format('H:i') ?? '08:00') }}"
                        required class="input input-bordered @error('morning_time') input-error @enderror">
                    @error('morning_time')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">When your morning starts</span>
                    </label>
                </div>

                <div class="form-control">
                    <label for="afternoon_time" class="label">
                        <span class="label-text font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                            Afternoon Time
                        </span>
                    </label>
                    <input type="time" id="afternoon_time" name="afternoon_time"
                        value="{{ old('afternoon_time', $user->afternoon_time?->format('H:i') ?? '12:00') }}"
                        required class="input input-bordered @error('afternoon_time') input-error @enderror">
                    @error('afternoon_time')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">When your afternoon starts</span>
                    </label>
                </div>

                <div class="form-control">
                    <label for="evening_time" class="label">
                        <span class="label-text font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                            </svg>
                            Evening Time
                        </span>
                    </label>
                    <input type="time" id="evening_time" name="evening_time"
                        value="{{ old('evening_time', $user->evening_time?->format('H:i') ?? '18:00') }}"
                        required class="input input-bordered @error('evening_time') input-error @enderror">
                    @error('evening_time')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">When your evening starts</span>
                    </label>
                </div>

                <div class="form-control">
                    <label for="bedtime" class="label">
                        <span class="label-text font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                            </svg>
                            Bedtime
                        </span>
                    </label>
                    <input type="time" id="bedtime" name="bedtime"
                        value="{{ old('bedtime', $user->bedtime?->format('H:i') ?? '22:00') }}"
                        required class="input input-bordered @error('bedtime') input-error @enderror">
                    @error('bedtime')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">When you typically go to bed</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Save Time Preferences
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
            </form>
        </div>
    </x-settings.layout>
</x-layouts.app>
