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

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Time Taken</span>
                                    </label>
                                    <input type="datetime-local" name="taken_at" value="{{ now()->format('Y-m-d\TH:i') }}" class="input input-bordered" required>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Dosage (optional)</span>
                                    </label>
                                    <input type="text" name="dosage_taken" value="{{ $item['as_needed'] ? ($item['medication']->dosage . ' ' . $item['medication']->unit) : ($item['schedule']->getCalculatedDosageWithUnit() ?? ($item['medication']->dosage . ' ' . $item['medication']->unit)) }}" class="input input-bordered">
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Notes (optional)</span>
                                    </label>
                                    <textarea name="notes" class="textarea textarea-bordered" rows="2"></textarea>
                                </div>

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

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Reason for Skipping</span>
                                    </label>
                                    <select name="skip_reason" class="select select-bordered">
                                        <option value="">Select reason...</option>
                                        <option value="Forgot">Forgot</option>
                                        <option value="Side effects">Side effects</option>
                                        <option value="Ran out">Ran out</option>
                                        <option value="Felt better">Felt better</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Notes (optional)</span>
                                    </label>
                                    <textarea name="notes" class="textarea textarea-bordered" rows="2"></textarea>
                                </div>

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
