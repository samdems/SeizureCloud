<x-layouts.app :title="__('Medication Schedule')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Today's Medication</h1>
                <p class="text-base-content/60">{{ now()->format('l, F j, Y') }}</p>
                @php
                    $totalOverdueCount = collect($todaySchedule)->filter(fn($item) => $item['is_overdue'] && !$item['taken'])->count();
                    $totalDueCount = collect($todaySchedule)->filter(fn($item) => $item['is_due'] && !$item['taken'] && !$item['is_overdue'])->count();
                @endphp
                @if($totalOverdueCount > 0)
                    <div class="badge badge-error badge-lg mt-2">
                        <x-heroicon-o-exclamation-triangle class="h-4 w-4 mr-1" />
                        {{ $totalOverdueCount }} Medication{{ $totalOverdueCount > 1 ? 's' : '' }} Overdue
                    </div>
                @elseif($totalDueCount > 0)
                    <div class="badge badge-warning badge-lg mt-2">
                        <x-heroicon-o-clock class="h-4 w-4 mr-1" />
                        {{ $totalDueCount }} Medication{{ $totalDueCount > 1 ? 's' : '' }} Due
                    </div>
                @endif
            </div>
            <div class="flex flex-row gap-2">
                <a href="{{ route('medications.schedule.history') }}" class="btn btn-secondary">
                    View Past Days
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="alert alert-info alert-outline">
            <x-heroicon-o-information-circle class="stroke-current shrink-0 w-6 h-6" />
            <div class="text-sm">
                <div><strong>Due Medications:</strong> <span class="badge badge-warning badge-sm"><x-heroicon-o-clock class="w-3 h-3 inline mr-1" />Due</span> Yellow warnings show medications that are past their scheduled time.</div>
                <div class="mt-1"><strong>Overdue Medications:</strong> <span class="badge badge-error badge-sm"><x-heroicon-o-exclamation-triangle class="w-3 h-3 inline mr-1" />Overdue</span> Red warnings show medications more than 30 minutes overdue.</div>
                <div class="mt-1"><strong>Late Taken:</strong> <span class="badge badge-error badge-sm"><x-heroicon-o-exclamation-triangle class="w-3 h-3 inline mr-1" />Taken Late</span> Red badges for medications that were taken late.</div>
            </div>
        </div>

        @if(count($todaySchedule) > 0)
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
                    <div class="flex items-center justify-between">
                        @php
                            $allTaken = collect($groupedSchedule[$period])->every(fn($item) => $item['taken']);
                            $hasMedications = count($groupedSchedule[$period]) > 0;
                            $hasLateMedications = collect($groupedSchedule[$period])->some(fn($item) => $item['taken_late']);
                        @endphp
                        <div class="divider text-lg font-bold flex-1">
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
                        @if($period !== 'as_needed')
                            @if($hasMedications && !$allTaken)
                                <button class="btn btn-success btn-sm ml-4" onclick="markAllTaken{{ ucfirst($period) }}.showModal()">
                                    <x-heroicon-o-check class="h-4 w-4" />
                                    Mark All Taken
                                </button>
                            @elseif($allTaken)
                                @if($hasLateMedications)
                                    <span class="badge badge-warning badge-sm ml-4">All Taken (Some Late)</span>
                                @else
                                    <span class="badge badge-success badge-sm ml-4">All Taken</span>
                                @endif
                            @endif
                        @endif
                    </div>

                    <div class="space-y-4">
                        @foreach($groupedSchedule[$period] as $key => $item)
                    <div class="card bg-base-100 shadow-xl {{ $item['taken'] ? 'opacity-60' : '' }} {{ $item['taken_late'] ? 'border-error border-2 bg-error/10' : '' }} {{ $item['is_overdue'] && !$item['taken'] ? 'border-error border-2 bg-error/10' : '' }} {{ $item['is_due'] && !$item['taken'] && !$item['is_overdue'] ? 'border-warning border-2 bg-warning/10' : '' }}">
                        <div class="card-body">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        @if($item['as_needed'])
                                            <div class="text-xl font-bold text-secondary">
                                                @if($item['taken'] && isset($item['log']))
                                                    {{ $item['log']->taken_at->format('g:i A') }}
                                                @else
                                                    As Needed
                                                @endif
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

                                    @if($item['taken'] && isset($item['log']))
                                        <div class="mt-3 p-2 bg-base-200 rounded-lg">
                                            <x-medication-log-timing :log="$item['log']" />
                                            @if($item['log']->notes)
                                                <div class="mt-2 text-sm text-base-content/70">
                                                    <span class="font-medium">Notes:</span> {{ $item['log']->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                @if(!$item['taken'])
                                    <div class="flex gap-2">
                                        <button class="btn btn-success" onclick="logTaken{{ $item['medication']->id }}_{{ $item['as_needed'] ? 'asneeded_' . $key : $item['schedule']->id }}.showModal()">
                                            <x-heroicon-o-check class="h-5 w-5" />
                                            Mark Taken
                                        </button>
                                        <button class="btn btn-outline btn-error" onclick="logSkipped{{ $item['medication']->id }}_{{ $item['as_needed'] ? 'asneeded_' . $key : $item['schedule']->id }}.showModal()">
                                            <x-heroicon-o-x-mark class="h-5 w-5" />
                                            Skip
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Mark Taken Modal -->
                    <dialog id="logTaken{{ $item['medication']->id }}_{{ $item['as_needed'] ? 'asneeded_' . $key : $item['schedule']->id }}" class="modal">
                        <div class="modal-box">
                            <h3 class="font-bold text-lg">Log Medication Taken</h3>
                            <form method="POST" action="{{ route('medications.log-taken') }}" class="space-y-4 mt-4">
                                @csrf
                                <input type="hidden" name="medication_id" value="{{ $item['medication']->id }}">
                                @if(!$item['as_needed'])
                                    <input type="hidden" name="medication_schedule_id" value="{{ $item['schedule']->id }}">
                                @endif

                                <x-form-field
                                    name="taken_at"
                                    label="Time Taken"
                                    type="datetime-local"
                                    value="{{ now()->format('Y-m-d\TH:i') }}"
                                    required
                                />

                                @if($item['as_needed'])
                                <x-form-field
                                    name="intended_time"
                                    label="When was it needed? (Optional)"
                                    type="datetime-local"
                                    value="{{ now()->format('Y-m-d\TH:i') }}"
                                    optional
                                />
                                @else
                                <input type="hidden" name="intended_time" value="{{ now()->format('Y-m-d') }}T{{ $item['schedule']->scheduled_time->format('H:i') }}">
                                @endif

                                <x-form-field
                                    name="dosage_taken"
                                    label="Dosage"
                                    type="text"
                                    value="{{ $item['as_needed'] ? ($item['medication']->dosage . ' ' . $item['medication']->unit) : ($item['schedule']->getCalculatedDosageWithUnit() ?? ($item['medication']->dosage . ' ' . $item['medication']->unit)) }}"
                                    optional
                                />

                                <x-form-field
                                    name="notes"
                                    label="Notes"
                                    type="textarea"
                                    rows="2"
                                    optional
                                />

                                <div class="modal-action">
                                    <button type="button" class="btn btn-outline" onclick="logTaken{{ $item['medication']->id }}_{{ $item['as_needed'] ? 'asneeded_' . $key : $item['schedule']->id }}.close()">Cancel</button>
                                    <button type="submit" class="btn btn-success">
                                        <x-heroicon-o-check class="h-4 w-4" />
                                        Log Taken
                                    </button>
                                </div>
                            </form>
                        </div>
                        <form method="dialog" class="modal-backdrop">
                            <button>close</button>
                        </form>
                    </dialog>

                    <!-- Skip Modal -->
                    <dialog id="logSkipped{{ $item['medication']->id }}_{{ $item['as_needed'] ? 'asneeded_' . $key : $item['schedule']->id }}" class="modal">
                        <div class="modal-box">
                            <h3 class="font-bold text-lg">Log Skipped Dose</h3>
                            <form method="POST" action="{{ route('medications.log-skipped') }}" class="space-y-4 mt-4">
                                @csrf
                                <input type="hidden" name="medication_id" value="{{ $item['medication']->id }}">
                                @if(!$item['as_needed'])
                                    <input type="hidden" name="medication_schedule_id" value="{{ $item['schedule']->id }}">
                                    <input type="hidden" name="intended_time" value="{{ now()->format('Y-m-d') }}T{{ $item['schedule']->scheduled_time->format('H:i') }}">
                                @else
                                    <x-form-field
                                        name="intended_time"
                                        label="When was it needed? (Optional)"
                                        type="datetime-local"
                                        value="{{ now()->format('Y-m-d\TH:i') }}"
                                        optional
                                    />
                                @endif

                                <x-form-field
                                    name="skip_reason"
                                    label="Reason for Skipping"
                                    type="select"
                                    placeholder="Select reason..."
                                    :options="[
                                        'Forgot' => 'Forgot',
                                        'Side effects' => 'Side effects',
                                        'Ran out' => 'Ran out',
                                        'Felt better' => 'Felt better',
                                        'Other' => 'Other'
                                    ]"
                                />

                                <x-form-field
                                    name="notes"
                                    label="Notes"
                                    type="textarea"
                                    rows="2"
                                    optional
                                />

                                <div class="modal-action">
                                    <button type="button" class="btn btn-outline" onclick="logSkipped{{ $item['medication']->id }}_{{ $item['as_needed'] ? 'asneeded_' . $key : $item['schedule']->id }}.close()">Cancel</button>
                                    <button type="submit" class="btn btn-error">
                                        <x-heroicon-o-x-mark class="h-4 w-4" />
                                        Log Skipped
                                    </button>
                                </div>
                            </form>
                        </div>
                        <form method="dialog" class="modal-backdrop">
                            <button>close</button>
                        </form>
                    </dialog>
                        @endforeach
                    </div>

                    @if($period !== 'as_needed')
                        <!-- Mark All Taken Modal -->
                        <dialog id="markAllTaken{{ ucfirst($period) }}" class="modal">
                            <div class="modal-box">
                                <h3 class="font-bold text-lg">Mark All {{ ucfirst($period) }} Medications as Taken</h3>
                                <form method="POST" action="{{ route('medications.log-bulk-taken') }}" class="space-y-4 mt-4">
                                    @csrf
                                    <input type="hidden" name="period" value="{{ $period }}">

                                    <x-form-field
                                        name="taken_at"
                                        label="Time Taken"
                                        type="datetime-local"
                                        value="{{ now()->format('Y-m-d\TH:i') }}"
                                        required
                                    />

                                    <x-form-field
                                        name="notes"
                                        label="Notes for all medications"
                                        type="textarea"
                                        rows="3"
                                        placeholder="Add notes that will apply to all medications in this time slot..."
                                        optional
                                    />

                                    <div class="bg-base-200 p-4 rounded-lg">
                                        <h4 class="font-semibold mb-2">Medications to be marked as taken:</h4>
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($groupedSchedule[$period] as $item)
                                                @if(!$item['taken'])
                                                    @php
                                                        $scheduledTime = $item['schedule']->scheduled_time;
                                                        $now = now();
                                                        $willBeLate = $now->greaterThan($scheduledTime->copy()->addMinutes(30));
                                                        $isDue = $item['is_due'];
                                                        $isOverdue = $item['is_overdue'];
                                                    @endphp
                                                    <li class="text-sm {{ $willBeLate ? 'text-error font-medium' : ($isOverdue ? 'text-error font-medium' : ($isDue ? 'text-warning font-medium' : '')) }}">
                                                        {{ $item['medication']->name }}
                                                        @if($item['schedule']->getCalculatedDosageWithUnit())
                                                            - {{ $item['schedule']->getCalculatedDosageWithUnit() }}
                                                        @endif
                                                        @if($willBeLate)
                                                            <span class="text-xs text-error">(Will be marked as late)</span>
                                                        @elseif($isOverdue)
                                                            <span class="text-xs text-error">(Overdue)</span>
                                                        @elseif($isDue)
                                                            <span class="text-xs text-warning">(Due)</span>
                                                        @endif
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                        @php
                                            $untakenItems = collect($groupedSchedule[$period])->filter(fn($item) => !$item['taken']);
                                            $hasLateItems = $untakenItems->some(function($item) {
                                                $scheduledTime = $item['schedule']->scheduled_time;
                                                return now()->greaterThan($scheduledTime->copy()->addMinutes(30));
                                            });
                                            $hasDueItems = $untakenItems->some(fn($item) => $item['is_due'] && !$item['is_overdue']);
                                            $hasOverdueItems = $untakenItems->some(fn($item) => $item['is_overdue']);
                                        @endphp

                                        @if($hasOverdueItems)
                                            <div class="alert alert-error mt-3">
                                                <x-heroicon-o-exclamation-triangle class="stroke-current shrink-0 w-6 h-6" />
                                                <span class="text-sm">Some medications are overdue (more than 30 minutes past scheduled time).</span>
                                            </div>
                                        @elseif($hasDueItems && !$hasLateItems)
                                            <div class="alert alert-warning mt-3">
                                                <x-heroicon-o-clock class="stroke-current shrink-0 w-6 h-6" />
                                                <span class="text-sm">Some medications are currently due for their scheduled time.</span>
                                            </div>
                                        @elseif($hasLateItems)
                                            <div class="alert alert-error mt-3">
                                                <x-heroicon-o-exclamation-triangle class="stroke-current shrink-0 w-6 h-6" />
                                                <span class="text-sm">Some medications will be marked as late (taken more than 30 minutes after scheduled time).</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="modal-action">
                                        <button type="button" class="btn btn-outline" onclick="markAllTaken{{ ucfirst($period) }}.close()">Cancel</button>
                                        <button type="submit" class="btn btn-success">
                                            <x-heroicon-o-check class="h-4 w-4" />
                                            Mark All as Taken
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <form method="dialog" class="modal-backdrop">
                                <button>close</button>
                            </form>
                        </dialog>
                    @endif
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
