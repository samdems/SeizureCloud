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
                    <div class="form-control flex-1">
                        <label class="label">
                            <span class="label-text">Select Date</span>
                        </label>
                        <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="input input-bordered" max="{{ now()->format('Y-m-d') }}">
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
                    'morning' => ['🌅 Morning', $user->morning_time->format('g:i A')],
                    'afternoon' => ['☀️ Afternoon', $user->afternoon_time->format('g:i A')],
                    'evening' => ['🌆 Evening', $user->evening_time->format('g:i A')],
                    'bedtime' => ['🌙 Bedtime', $user->bedtime->format('g:i A')],
                    'as_needed' => ['💊 As Needed', '']
                ];
            @endphp

            @foreach(['morning', 'afternoon', 'evening', 'bedtime', 'as_needed'] as $period)
                @if(count($groupedSchedule[$period]) > 0)
                    <div class="divider text-lg font-bold">
                        {{ $periodLabels[$period][0] }}
                        @if($periodLabels[$period][1])
                            <span class="text-sm font-normal text-base-content/60">({{ $periodLabels[$period][1] }})</span>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @foreach($groupedSchedule[$period] as $item)
                    <div class="card bg-base-100 shadow-xl {{ $item['taken'] ? 'opacity-60' : '' }}">
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
                                            <span class="badge badge-success badge-lg">✓ Taken</span>
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
