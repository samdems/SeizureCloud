<x-layouts.app :title="__('Medication Schedule')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Medication Schedule History</h1>
                <p class="text-base-content/60">{{ $startDate->format('M j') }} - {{ $endDate->format('M j, Y') }}</p>
            </div>
            <a href="{{ route('medications.schedule') }}" class="btn btn-ghost">
                Back to Today
            </a>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form method="GET" action="{{ route('medications.schedule.history') }}" class="flex gap-4 items-end flex-wrap">
                    <div class="flex-1 min-w-[200px]">
                        <x-form-field
                            name="date"
                            label="Week Ending"
                            type="date"
                            value="{{ $endDate->format('Y-m-d') }}"
                            max="{{ now()->format('Y-m-d') }}"
                            wrapperClass=""
                        />
                    </div>
                    <button type="submit" class="btn btn-primary">View Week</button>
                    <div class="flex gap-2">
                        <a href="{{ route('medications.schedule.history', ['date' => $endDate->copy()->subDays(7)->format('Y-m-d')]) }}" class="btn btn-outline btn-sm">
                            <x-heroicon-o-chevron-left class="w-4 h-4" />
                            Previous Week
                        </a>
                        @if($endDate->copy()->addDays(7)->lte(now()))
                            <a href="{{ route('medications.schedule.history', ['date' => $endDate->copy()->addDays(7)->format('Y-m-d')]) }}" class="btn btn-outline btn-sm">
                                Next Week
                                <x-heroicon-o-chevron-right class="w-4 h-4" />
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @php
            $periodLabels = [
                'morning' => ['Morning', $user->morning_time->format('g:i A'), 'sun'],
                'afternoon' => ['Afternoon', $user->afternoon_time->format('g:i A'), 'sun'],
                'evening' => ['Evening', $user->evening_time->format('g:i A'), 'moon'],
                'bedtime' => ['Bedtime', $user->bedtime->format('g:i A'), 'moon'],
                'as_needed' => ['As Needed', '', 'heart']
            ];
        @endphp

        @foreach($weekSchedule as $dateKey => $dayData)
            @php
                $date = $dayData['date'];
                $daySchedule = $dayData['daySchedule'];
                $groupedSchedule = $dayData['groupedSchedule'];
                $isToday = $date->isToday();
            @endphp

            <div class="card bg-base-100 shadow-xl {{ $isToday ? 'ring-2 ring-primary' : '' }}">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-bold flex items-center gap-2">
                                {{ $date->format('l, F j, Y') }}
                                @if($isToday)
                                    <span class="badge badge-primary">Today</span>
                                @endif
                            </h2>
                        </div>
                        @php
                            $totalScheduled = count(array_filter($daySchedule, fn($item) => !$item['as_needed']));
                            $totalTaken = count(array_filter($daySchedule, fn($item) => $item['taken']));
                            $percentComplete = $totalScheduled > 0 ? round(($totalTaken / $totalScheduled) * 100) : 0;
                        @endphp
                        @if($totalScheduled > 0)
                            <div class="text-right">
                                <div class="text-sm text-base-content/60">Completion</div>
                                <div class="flex items-center gap-2">
                                    <progress class="progress progress-primary w-24" value="{{ $percentComplete }}" max="100"></progress>
                                    <span class="text-lg font-bold">{{ $percentComplete }}%</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(count($daySchedule) > 0)
                        @foreach(['morning', 'afternoon', 'evening', 'bedtime', 'as_needed'] as $period)
                            @if(count($groupedSchedule[$period]) > 0)
                                <div class="divider text-sm font-bold">
                                    <div class="flex items-center gap-2">
                                        @if($periodLabels[$period][2] === 'sun')
                                            <x-heroicon-o-sun class="w-4 h-4" />
                                        @elseif($periodLabels[$period][2] === 'moon')
                                            <x-heroicon-o-moon class="w-4 h-4" />
                                        @elseif($periodLabels[$period][2] === 'heart')
                                            <x-heroicon-o-heart class="w-4 h-4" />
                                        @endif
                                        <span>{{ $periodLabels[$period][0] }}</span>
                                        @if($periodLabels[$period][1])
                                            <span class="text-xs font-normal text-base-content/60">({{ $periodLabels[$period][1] }})</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
                                    @foreach($groupedSchedule[$period] as $item)
                                        <div class="card bg-base-200 shadow {{ $item['taken'] ? 'opacity-70' : '' }} {{ $item['taken_late'] ? 'border-error border-2' : '' }} {{ $item['is_overdue'] && !$item['taken'] ? 'border-error border-2' : '' }}">
                                            <div class="card-body p-4">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="flex-1 min-w-0">
                                                        @if($item['as_needed'])
                                                            <div class="text-sm font-bold text-secondary">
                                                                @if($item['taken'] && isset($item['log']))
                                                                    {{ $item['log']->taken_at->format('g:i A') }}
                                                                @else
                                                                    As Needed
                                                                @endif
                                                            </div>
                                                        @else
                                                            <div class="text-lg font-bold text-primary">
                                                                {{ Carbon\Carbon::parse($item['schedule']->scheduled_time)->format('g:i A') }}
                                                            </div>
                                                        @endif

                                                        <h3 class="font-bold text-base truncate mt-1" title="{{ $item['medication']->name }}">
                                                            {{ $item['medication']->name }}
                                                        </h3>

                                                        @if($item['as_needed'] && $item['medication']->dosage)
                                                            <p class="text-sm text-base-content/70">{{ $item['medication']->dosage }} {{ $item['medication']->unit }}</p>
                                                        @elseif(!$item['as_needed'] && $item['schedule']->getCalculatedDosageWithUnit())
                                                            <p class="text-sm text-base-content/70">
                                                                {{ $item['schedule']->getCalculatedDosageWithUnit() }}
                                                            </p>
                                                        @endif
                                                    </div>

                                                    <div class="flex-shrink-0">
                                                        @if($item['taken'])
                                                            @if($item['taken_late'])
                                                                <div class="badge badge-error badge-sm gap-1">
                                                                    <x-heroicon-o-exclamation-triangle class="w-3 h-3" />
                                                                    Late
                                                                </div>
                                                            @else
                                                                <div class="badge badge-success badge-sm gap-1">
                                                                    <x-heroicon-o-check class="w-3 h-3" />
                                                                    Taken
                                                                </div>
                                                            @endif
                                                        @elseif($item['is_overdue'])
                                                            <div class="badge badge-error badge-sm gap-1">
                                                                <x-heroicon-o-exclamation-triangle class="w-3 h-3" />
                                                                Overdue
                                                            </div>
                                                        @elseif($item['is_due'])
                                                            <div class="badge badge-warning badge-sm gap-1">
                                                                <x-heroicon-o-clock class="w-3 h-3" />
                                                                Due
                                                            </div>
                                                        @else
                                                            <div class="badge badge-ghost badge-sm">
                                                                Scheduled
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($item['taken'] && isset($item['log']))
                                                    <div class="mt-2 pt-2 border-t border-base-300">
                                                        <x-medication-log-timing :log="$item['log']" />
                                                        @if($item['log']->notes)
                                                            <div class="mt-1 text-xs text-base-content/70 truncate" title="{{ $item['log']->notes }}">
                                                                <span class="font-medium">Notes:</span> {{ $item['log']->notes }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-8 text-base-content/60">
                            <p>No medications scheduled for this day.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        @if(count($weekSchedule) === 0)
            <div class="card bg-base-200">
                <div class="card-body text-center">
                    <p class="text-base-content/60">No medications scheduled for this week.</p>
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
