<x-layouts.app :title="__('Medication Schedule')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Today's Medication</h1>
                <p class="text-base-content/60">{{ now()->format('l, F j, Y') }}</p>
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

        @if(count($todaySchedule) > 0)
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
                    <div class="flex items-center justify-between">
                        <div class="divider text-lg font-bold flex-1">
                            {{ $periodLabels[$period][0] }}
                            @if($periodLabels[$period][1])
                                <span class="text-sm font-normal text-base-content/60">({{ $periodLabels[$period][1] }})</span>
                            @endif
                        </div>
                        @if($period !== 'as_needed')
                            @php
                                $allTaken = collect($groupedSchedule[$period])->every(fn($item) => $item['taken']);
                                $hasMedications = count($groupedSchedule[$period]) > 0;
                            @endphp
                            @if($hasMedications && !$allTaken)
                                <button class="btn btn-success btn-sm ml-4" onclick="markAllTaken{{ ucfirst($period) }}.showModal()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Mark All Taken
                                </button>
                            @elseif($allTaken)
                                <span class="badge badge-success badge-sm ml-4">All Taken</span>
                            @endif
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

                                @if(!$item['taken'])
                                    <div class="flex gap-2">
                                        <button class="btn btn-success" onclick="logTaken{{ $item['medication']->id }}_{{ $item['as_needed'] ? 'asneeded' : $item['schedule']->id }}.showModal()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Mark Taken
                                        </button>
                                        <button class="btn btn-outline btn-error" onclick="logSkipped{{ $item['medication']->id }}_{{ $item['as_needed'] ? 'asneeded' : $item['schedule']->id }}.showModal()">
                                            Skip
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Mark Taken Modal -->
                    <dialog id="logTaken{{ $item['medication']->id }}_{{ $item['as_needed'] ? 'asneeded' : $item['schedule']->id }}" class="modal">
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
                                    <button type="button" class="btn btn-outline" onclick="logTaken{{ $item['medication']->id }}_{{ $item['as_needed'] ? 'asneeded' : $item['schedule']->id }}.close()">Cancel</button>
                                    <button type="submit" class="btn btn-success">Log Taken</button>
                                </div>
                            </form>
                        </div>
                        <form method="dialog" class="modal-backdrop">
                            <button>close</button>
                        </form>
                    </dialog>

                    <!-- Skip Modal -->
                    <dialog id="logSkipped{{ $item['medication']->id }}_{{ $item['as_needed'] ? 'asneeded' : $item['schedule']->id }}" class="modal">
                        <div class="modal-box">
                            <h3 class="font-bold text-lg">Log Skipped Dose</h3>
                            <form method="POST" action="{{ route('medications.log-skipped') }}" class="space-y-4 mt-4">
                                @csrf
                                <input type="hidden" name="medication_id" value="{{ $item['medication']->id }}">
                                @if(!$item['as_needed'])
                                    <input type="hidden" name="medication_schedule_id" value="{{ $item['schedule']->id }}">
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
                                    <button type="button" class="btn btn-outline" onclick="logSkipped{{ $item['medication']->id }}_{{ $item['as_needed'] ? 'asneeded' : $item['schedule']->id }}.close()">Cancel</button>
                                    <button type="submit" class="btn btn-error">Log Skipped</button>
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
                                                    <li class="text-sm">
                                                        {{ $item['medication']->name }}
                                                        @if($item['schedule']->getCalculatedDosageWithUnit())
                                                            - {{ $item['schedule']->getCalculatedDosageWithUnit() }}
                                                        @endif
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="modal-action">
                                        <button type="button" class="btn btn-outline" onclick="markAllTaken{{ ucfirst($period) }}.close()">Cancel</button>
                                        <button type="submit" class="btn btn-success">Mark All as Taken</button>
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
