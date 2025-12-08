<x-layouts.app :title="__('Medication Schedule')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Medication Schedule History</h1>
                <p class="text-base-content/60">{{ $date->format('l, F j, Y') }}</p>
            </div>
            <a href="{{ route('medications.schedule') }}" class="btn btn-ghost">
                Back to Today
            </a>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form method="GET" action="{{ route('medications.schedule.history') }}" class="flex gap-4 items-end">
                    <div class="flex-1">
                        <x-form-field
                            name="date"
                            label="Select Date"
                            type="date"
                            value="{{ $date->format('Y-m-d') }}"
                            max="{{ now()->format('Y-m-d') }}"
                            wrapperClass=""
                        />
                    </div>
                    <button type="submit" class="btn btn-primary">View Schedule</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(count($daySchedule) > 0)
            @php
                $periodLabels = [
                    'morning' => ['Morning', $user->morning_time->format('g:i A'), 'sun'],
                    'afternoon' => ['Afternoon', $user->afternoon_time->format('g:i A'), 'sun'],
                    'evening' => ['Evening', $user->evening_time->format('g:i A'), 'moon'],
                    'bedtime' => ['Bedtime', $user->bedtime->format('g:i A'), 'moon'],
                    'as_needed' => ['As Needed', '', 'heart']
                ];
            @endphp

            @foreach(['morning', 'afternoon', 'evening', 'bedtime', 'as_needed'] as $period)
                @if(count($groupedSchedule[$period]) > 0)
                    <div class="divider text-lg font-bold">
                        <div class="flex items-center gap-2">
                            @if($periodLabels[$period][2] === 'sun')
                                <x-heroicon-o-sun class="w-5 h-5" />
                            @elseif($periodLabels[$period][2] === 'moon')
                                <x-heroicon-o-moon class="w-5 h-5" />
                            @elseif($periodLabels[$period][2] === 'heart')
                                <x-heroicon-o-heart class="w-5 h-5" />
                            @endif
                            <span>{{ $periodLabels[$period][0] }}</span>
                            @if($periodLabels[$period][1])
                                <span class="text-sm font-normal text-base-content/60">({{ $periodLabels[$period][1] }})</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach($groupedSchedule[$period] as $item)
                    <div class="card bg-base-100 shadow-xl {{ $item['taken'] ? 'opacity-60' : '' }} {{ $item['taken_late'] ? 'border-error border-2 bg-error/10' : '' }} {{ $item['is_overdue'] && !$item['taken'] ? 'border-error border-2 bg-error/10' : '' }} {{ $item['is_due'] && !$item['taken'] && !$item['is_overdue'] ? 'border-warning border-2 bg-warning/10' : '' }}">
                        <div class="card-body">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        @if($item['as_needed'])
                                            <div class="text-xl font-bold text-secondary">
                                                As Needed
                                            </div>
                                        @else
                                            <div class="text-3xl font-bold text-primary">
                                                {{ Carbon\Carbon::parse($item['schedule']->scheduled_time)->format('g:i A') }}
                                            </div>
                                        @endif
                                        @if($item['taken'])
                                            @if($item['taken_late'])
                                                <span class="badge badge-error badge-lg"><x-heroicon-o-exclamation-triangle class="w-4 h-4 inline mr-1" />Taken Late</span>
                                            @else
                                                <span class="badge badge-success badge-lg"><x-heroicon-o-check class="w-4 h-4 inline mr-1" />Taken</span>
                                            @endif
                                        @elseif($item['is_overdue'])
                                            <span class="badge badge-error badge-lg"><x-heroicon-o-exclamation-triangle class="w-4 h-4 inline mr-1" />Overdue</span>
                                        @elseif($item['is_due'])
                                            <span class="badge badge-warning badge-lg"><x-heroicon-o-clock class="w-4 h-4 inline mr-1" />Due</span>
                                        @endif
                                    </div>
                                    <h3 class="text-xl font-bold mt-2">{{ $item['medication']->name }}</h3>
                                    @if($item['as_needed'] && $item['medication']->dosage)
                                        <p class="text-lg">{{ $item['medication']->dosage }} {{ $item['medication']->unit }}</p>
                                    @elseif(!$item['as_needed'] && $item['schedule']->getCalculatedDosageWithUnit())
                                        <p class="text-lg">
                                            {{ $item['schedule']->getCalculatedDosageWithUnit() }}
                                            @if($item['schedule']->dosage_multiplier != 1)
                                                <span class="text-sm text-base-content/60">({{ $item['schedule']->dosage_multiplier }}x)</span>
                                            @endif
                                        </p>
                                    @endif
                                    @if($item['medication']->description)
                                        <p class="text-sm text-base-content/70 mt-2">{{ $item['medication']->description }}</p>
                                    @endif
                                </div>


                            </div>
                        </div>
                    </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        @else
            <div class="card bg-base-200">
                <div class="card-body text-center">
                    <p class="text-base-content/60">No medications scheduled for today.</p>
                    <p class="text-sm text-base-content/50">Add schedules to your medications to see them here.</p>
                    <div class="card-actions justify-center mt-4">
                        <a href="{{ route('medications.index') }}" class="btn btn-primary">
                            Manage Medications
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
