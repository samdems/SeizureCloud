{{-- Reusable single seizure content partial --}}
@if($showHeader ?? true)
    <div class="header">
        <h1>
            @if(isset($recordNumber))
                Seizure Record #{{ $recordNumber }}
            @else
                Seizure Record Details
            @endif
        </h1>
        <p><strong>{{ $seizure->start_time->format('l, F j, Y \a\t g:i A') }}</strong></p>
        @if(!isset($recordNumber))
            <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        @endif
    </div>
@endif

@if(!isset($recordNumber))
    <div class="patient-info">
        <h3>Patient Information</h3>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Account Type:</strong> {{ ucfirst($user->account_type) }}</p>
    </div>
@endif

@if($emergencyStatus['is_emergency'])
    <div class="emergency-alert">
        <h3>WARNING: MEDICAL EMERGENCY DETECTED</h3>
        @if($emergencyStatus['status_epilepticus'])
            <p><strong>Possible Status Epilepticus:</strong> Seizure duration ({{ $seizure->calculated_duration }} min) exceeds emergency threshold ({{ $emergencyStatus['duration_threshold'] }} min)</p>
        @endif
        @if($emergencyStatus['cluster_emergency'])
            <p><strong>Seizure Cluster:</strong> {{ $emergencyStatus['cluster_count'] }} seizures detected within {{ $emergencyStatus['timeframe_hours'] }} hours</p>
        @endif
        <p style="font-size: 11px; margin-top: 10px;">This seizure met emergency criteria. Review with your healthcare provider.</p>
    </div>
@endif

<div class="section-header">Basic Information</div>
<table class="seizure-details">
    <tr>
        <td class="label">Start Time</td>
        <td>{{ $seizure->start_time->format('M d, Y H:i') }}</td>
    </tr>
    <tr>
        <td class="label">End Time</td>
        <td>{{ $seizure->end_time ? $seizure->end_time->format('M d, Y H:i') : 'Not recorded' }}</td>
    </tr>
    <tr>
        <td class="label">Duration</td>
        <td>
            @if($seizure->calculated_duration)
                {{ $seizure->calculated_duration }} minutes
                @if($emergencyStatus['status_epilepticus'])
                    <strong style="color: #ef4444;">(Emergency Duration)</strong>
                @endif
            @else
                Not recorded
            @endif
        </td>
    </tr>
    <tr>
        <td class="label">Severity</td>
        <td>
            @if($seizure->severity)
                <div class="severity-display">
                    <strong>{{ $seizure->severity }}/10</strong>
                    @if($seizure->severity <= 3)
                        <span style="color: #10b981; margin-left: 8px;">(MILD)</span>
                    @elseif($seizure->severity <= 6)
                        <span style="color: #f59e0b; margin-left: 8px;">(MODERATE)</span>
                    @else
                        <span style="color: #ef4444; margin-left: 8px;">(SEVERE)</span>
                    @endif
                </div>
            @else
                Not recorded
            @endif
        </td>
    </tr>
    <tr>
        <td class="label">Postictal State End</td>
        <td>{{ $seizure->postictal_state_end ? $seizure->postictal_state_end->format('M d, Y H:i') : 'Not recorded' }}</td>
    </tr>
    <tr>
        <td class="label">Seizure Type</td>
        <td>{{ $seizure->seizure_type ? Str::headline($seizure->seizure_type) : 'Not recorded' }}</td>
    </tr>
</table>

<div class="section-header">Triggers</div>
<div class="list-section">
    @if($seizure->triggers && count($seizure->triggers) > 0)
        @foreach($seizure->triggers as $trigger)
            <div class="list-item">{{ $trigger }}</div>
        @endforeach
    @else
        <p style="color: #666; font-style: italic;">No triggers recorded.</p>
    @endif

    @if($seizure->other_triggers)
        <h4>Other Triggers:</h4>
        <p>{{ $seizure->other_triggers }}</p>
    @endif
</div>

<div class="section-header">Pre-Ictal Symptoms</div>
<div class="list-section">
    @if($seizure->pre_ictal_symptoms && count($seizure->pre_ictal_symptoms) > 0)
        @foreach($seizure->pre_ictal_symptoms as $symptom)
            <div class="list-item">{{ $symptom }}</div>
        @endforeach
    @else
        <p style="color: #666; font-style: italic;">No pre-ictal symptoms recorded.</p>
    @endif
</div>

<div class="section-header">Additional Information</div>
<div class="list-section">
    <div class="checkbox-item">
        <span class="checkbox {{ $seizure->on_period ? 'checked' : 'unchecked' }}">{{ $seizure->on_period ? 'YES' : 'NO' }}</span>
        <span>On Period @if($seizure->days_since_period)({{ $seizure->days_since_period }} days since last period)@endif</span>
    </div>
    <div class="checkbox-item">
        <span class="checkbox {{ $seizure->ambulance_called ? 'checked' : 'unchecked' }}">{{ $seizure->ambulance_called ? 'YES' : 'NO' }}</span>
        <span>Ambulance Called</span>
    </div>
    <div class="checkbox-item">
        <span class="checkbox {{ $seizure->slept_after ? 'checked' : 'unchecked' }}">{{ $seizure->slept_after ? 'YES' : 'NO' }}</span>
        <span>Slept After</span>
    </div>
    <div class="checkbox-item">
        <span class="checkbox {{ $seizure->nhs_contact_type ? 'checked' : 'unchecked' }}">{{ $seizure->nhs_contact_type ? 'YES' : 'NO' }}</span>
        <span>NHS Contacted @if($seizure->nhs_contact_type)({{ $seizure->nhs_contact_type }})@endif</span>
    </div>
    <div class="checkbox-item">
        <span class="checkbox {{ $seizure->has_video_evidence ? 'checked' : 'unchecked' }}">{{ $seizure->has_video_evidence ? 'YES' : 'NO' }}</span>
        <span>Video Evidence</span>
    </div>
</div>

@if($seizure->has_video_evidence && $seizure->video_notes)
    <div class="notes-section">
        <h4>Video Notes</h4>
        <p>{{ $seizure->video_notes }}</p>
    </div>
@endif

@if($seizure->notes)
    <div class="notes-section">
        <h4>General Notes</h4>
        <p>{{ $seizure->notes }}</p>
    </div>
@endif

<div class="section-header">Wellbeing</div>
<table class="seizure-details">
    <tr>
        <td class="label">Wellbeing Rating</td>
        <td>{{ $seizure->wellbeing_rating ? Str::headline($seizure->wellbeing_rating) : 'Not recorded' }}</td>
    </tr>
    @if($seizure->wellbeing_notes)
    <tr>
        <td class="label">Wellbeing Notes</td>
        <td>{{ $seizure->wellbeing_notes }}</td>
    </tr>
    @endif
</table>

@if(!$medications->isEmpty())
    @if(!isset($recordNumber))
        <div class="page-break"></div>
    @endif
    <div class="section-header">Medication Adherence Before Seizure</div>
    @foreach($medications as $medication)
        @php
            $adherence = $medication->adherence ?? [];
            $wasNeeded = $adherence['was_needed'] ?? false;
            $allTaken = $adherence['all_taken'] ?? false;
        @endphp

        <div class="keep-together" style="margin-bottom: 20px;">
            <h4 style="margin: 15px 0 10px 0; padding: 8px; background: #f8f9fa; border: 1px solid #e2e8f0;">
                {{ $medication->name }} - {{ $medication->dosage }} {{ $medication->unit }}
                @if(!$wasNeeded)
                    <span style="color: #666; font-weight: normal;">(Not Scheduled)</span>
                @elseif($allTaken)
                    <span style="color: #10b981; font-weight: normal;">(All Taken)</span>
                @else
                    <span style="color: #ef4444; font-weight: normal;">(Missed Doses)</span>
                @endif
            </h4>

            @if($wasNeeded && count($adherence['scheduled_doses'] ?? []) > 0)
                <table class="medication-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Scheduled Time</th>
                            <th>Actual Time</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adherence['scheduled_doses'] as $dose)
                            @php
                                $wasTaken = $dose['log'] && $dose['log']->taken_at;
                                $wasLate = false;
                                if($wasTaken) {
                                    $scheduledTime = $dose['schedule']->scheduled_time;
                                    $actualTime = $dose['log']->taken_at;
                                    $diffMinutes = $scheduledTime->diffInMinutes($actualTime, false);
                                    $wasLate = $diffMinutes > 30;
                                }
                            @endphp
                            <tr>
                                <td class="{{ $wasTaken ? ($wasLate ? 'status-late' : 'status-taken') : 'status-missed' }}">
                                    @if($wasTaken)
                                        {{ $wasLate ? 'Taken Late' : 'Taken' }}
                                    @else
                                        Missed
                                    @endif
                                </td>
                                <td>{{ $dose['schedule']->scheduled_time->format('g:i A') }}</td>
                                <td>
                                    @if($wasTaken)
                                        {{ $dose['log']->taken_at->format('g:i A') }}
                                        @if(abs($diffMinutes) > 30)
                                            <br><small style="color: #f59e0b;">
                                                @if($diffMinutes > 0)
                                                    {{ $diffMinutes }}min late
                                                @else
                                                    {{ abs($diffMinutes) }}min early
                                                @endif
                                            </small>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($dose['log'] && $dose['log']->notes)
                                        {{ $dose['log']->notes }}
                                    @elseif(!$wasTaken && $dose['log'] && $dose['log']->skip_reason)
                                        {{ $dose['log']->skip_reason }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif(!$wasNeeded)
                <p style="color: #666; font-style: italic; padding: 10px;">No doses were scheduled before the seizure occurred (Seizure at {{ $seizure->start_time->format('g:i A') }})</p>
            @endif
        </div>
    @endforeach
@endif

@if(!$vitals->isEmpty())
    @if(!isset($recordNumber))
        <div class="page-break"></div>
    @endif
    <div class="section-header">Vitals on Day of Seizure</div>
    <div class="vitals-grid">
        @foreach($vitals as $type => $typeVitals)
            <div class="vital-card keep-together">
                <h5>{{ $type }}</h5>
                @foreach($typeVitals as $vital)
                    @php
                        $isAbnormal = false;
                        switch($type) {
                            case 'Heart Rate':
                                $isAbnormal = $vital->value < 60 || $vital->value > 100;
                                $unit = 'bpm';
                                break;
                            case 'Blood Pressure Systolic':
                                $isAbnormal = $vital->value < 90 || $vital->value > 140;
                                $unit = 'mmHg';
                                break;
                            case 'Blood Pressure Diastolic':
                                $isAbnormal = $vital->value < 60 || $vital->value > 90;
                                $unit = 'mmHg';
                                break;
                            case 'Body Temperature':
                                $isAbnormal = $vital->value < 36 || $vital->value > 37.5;
                                $unit = 'C';
                                break;
                            case 'Blood Oxygen Level':
                                $isAbnormal = $vital->value < 95;
                                $unit = '%';
                                break;
                            case 'Blood Sugar':
                                $isAbnormal = $vital->value < 70 || $vital->value > 140;
                                $unit = 'mg/dL';
                                break;
                            case 'Weight':
                                $unit = 'kg';
                                break;
                            case 'Respiratory Rate':
                                $unit = '/min';
                                break;
                            default:
                                $unit = '';
                                break;
                        }
                    @endphp
                    <div class="vital-reading">
                        <div>
                            <span class="vital-value {{ $isAbnormal ? 'vital-abnormal' : '' }}">
                                {{ number_format($vital->value, 1) }} {{ $unit }}
                                @if($isAbnormal) [ABNORMAL] @endif
                            </span>
                            <div class="vital-time">
                                {{ $vital->recorded_at->format('H:i') }}
                                @if($vital->recorded_at <= $seizure->start_time)
                                    (Before)
                                @else
                                    (After)
                                @endif
                            </div>
                            @if($vital->notes)
                                <div style="font-size: 9px; color: #666; font-style: italic; margin-top: 2px;">
                                    {{ Str::limit($vital->notes, 40) }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    @php
        $totalVitals = $vitals->flatten()->count();
        $beforeSeizure = $vitals->flatten()->filter(function($v) use ($seizure) { return $v->recorded_at <= $seizure->start_time; })->count();
        $afterSeizure = $totalVitals - $beforeSeizure;
    @endphp

    <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px; font-size: 10px;">
        <strong>Vitals Summary:</strong> {{ $totalVitals }} total recordings | {{ $beforeSeizure }} before seizure | {{ $afterSeizure }} after seizure
    </div>
@endif

@if(!isset($recordNumber))
    <div class="footer keep-together">
        <p><strong>Record Information</strong></p>
        <p>Created: {{ $seizure->created_at->format('M d, Y H:i') }}</p>
        @if($seizure->updated_at != $seizure->created_at)
            <p>Last Updated: {{ $seizure->updated_at->format('M d, Y H:i') }}</p>
        @endif
        <p style="margin-top: 15px;">This report was generated by EpiCare Seizure Tracker</p>
        <p>For medical purposes only - please consult with your healthcare provider</p>
        <p>Generated: {{ now()->format('F j, Y \a\t g:i A T') }}</p>
    </div>
@else
    <div style="margin-top: 15px; padding: 8px; background: #f8f9fa; border-radius: 4px; font-size: 10px; color: #666;">
        <strong>Record Info:</strong> Created {{ $seizure->created_at->format('M d, Y H:i') }}
        @if($seizure->updated_at != $seizure->created_at)
            | Updated {{ $seizure->updated_at->format('M d, Y H:i') }}
        @endif
    </div>
@endif
