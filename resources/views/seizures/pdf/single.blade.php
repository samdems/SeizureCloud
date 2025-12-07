<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seizure Report - {{ $seizure->start_time->format('M j, Y g:i A') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #e53e3e;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #e53e3e;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .section h3 {
            margin: 0 0 15px 0;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            width: 33.33%;
            vertical-align: top;
        }

        .info-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
        }

        .severity-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        .severity-1-3 { background: #68d391; color: #22543d; }
        .severity-4-6 { background: #fbd38d; color: #744210; }
        .severity-7-10 { background: #fc8181; color: #742a2a; }

        .emergency-banner {
            background: #fed7d7;
            border: 2px solid #fc8181;
            color: #742a2a;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .detail-table th {
            background: #edf2f7;
            padding: 8px 12px;
            text-align: left;
            font-weight: bold;
            border-bottom: 1px solid #e2e8f0;
            width: 25%;
        }

        .detail-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .notes-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            min-height: 60px;
        }

        .medication-list {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }

        .medication-item {
            border-bottom: 1px solid #f1f5f9;
            padding: 8px 0;
        }

        .medication-item:last-child {
            border-bottom: none;
        }

        .vitals-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .vital-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            width: 25%;
            text-align: center;
            vertical-align: top;
        }

        .vital-value {
            font-size: 18px;
            font-weight: bold;
            color: #2d3748;
        }

        .vital-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #666;
            margin-right: 5px;
            text-align: center;
            line-height: 10px;
            font-size: 10px;
        }

        .checkbox.checked {
            background: #68d391;
            color: white;
        }

        .keep-together {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .section {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        @media print {
            body { margin: 0; padding: 15px; }
            .keep-together { page-break-inside: avoid; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Seizure Report</h1>
        <p><strong>{{ $seizure->start_time->format('F j, Y \a\t g:i A') }}</strong></p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    @if($seizure->duration_minutes >= ($user->status_epilepticus_duration_minutes ?? 5))
        <div class="emergency-banner">
            ⚠️ EMERGENCY: This seizure met status epilepticus criteria
            (Duration: {{ $seizure->duration_minutes }} minutes, Threshold: {{ $user->status_epilepticus_duration_minutes ?? 5 }} minutes)
        </div>
    @endif

    <div class="section">
        <h3>Patient Information</h3>
        <table class="detail-table">
            <tr>
                <th>Name:</th>
                <td>{{ $user->name }}</td>
                <th>Email:</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>Account Type:</th>
                <td>{{ ucfirst($user->account_type) }}</td>
                <th>Report ID:</th>
                <td>#{{ $seizure->id }}</td>
            </tr>
        </table>
    </div>

    <table class="info-grid">
        <tr>
            <td class="info-item">
                <div class="info-label">Start Time</div>
                <div class="info-value">{{ $seizure->start_time->format('g:i A') }}</div>
                <div style="font-size: 11px; color: #666;">{{ $seizure->start_time->format('M j, Y') }}</div>
            </td>
            <td class="info-item">
                <div class="info-label">Duration</div>
                <div class="info-value">
                    @if($seizure->calculated_duration)
                        {{ $seizure->calculated_duration }} min
                    @else
                        Unknown
                    @endif
                </div>
            </td>
            <td class="info-item">
                <div class="info-label">Severity</div>
                <div class="info-value">
                    @if($seizure->severity)
                        <span class="severity-badge
                            @if($seizure->severity <= 3) severity-1-3
                            @elseif($seizure->severity <= 6) severity-4-6
                            @else severity-7-10
                            @endif">
                            {{ $seizure->severity }}/10
                        </span>
                    @else
                        Not rated
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="section">
        <h3>Seizure Details</h3>
        <table class="detail-table">
            <tr>
                <th>End Time:</th>
                <td>{{ $seizure->end_time ? $seizure->end_time->format('M j, Y g:i A') : 'Not recorded' }}</td>
                <th>Type:</th>
                <td>{{ $seizure->seizure_type ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <th>Recovery Time:</th>
                <td>{{ $seizure->recovery_time ?? 'Not recorded' }}</td>
                <th>Postictal End:</th>
                <td>{{ $seizure->postictal_state_end ? $seizure->postictal_state_end->format('g:i A') : 'Not recorded' }}</td>
            </tr>
        </table>

        <h4 style="margin: 20px 0 10px 0; color: #2d3748;">Seizure Characteristics</h4>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                    <p><span class="checkbox {{ $seizure->on_period ? 'checked' : '' }}">{{ $seizure->on_period ? '✓' : '' }}</span> On menstrual period</p>
                    <p><span class="checkbox {{ $seizure->ambulance_called ? 'checked' : '' }}">{{ $seizure->ambulance_called ? '✓' : '' }}</span> Ambulance called</p>
                    <p><span class="checkbox {{ $seizure->slept_after ? 'checked' : '' }}">{{ $seizure->slept_after ? '✓' : '' }}</span> Slept after seizure</p>
                    <p><span class="checkbox {{ $seizure->has_video_evidence ? 'checked' : '' }}">{{ $seizure->has_video_evidence ? '✓' : '' }}</span> Video evidence available</p>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                    <p><span class="checkbox {{ $seizure->post_ictal_confusion ? 'checked' : '' }}">{{ $seizure->post_ictal_confusion ? '✓' : '' }}</span> Post-ictal confusion</p>
                    <p><span class="checkbox {{ $seizure->post_ictal_headache ? 'checked' : '' }}">{{ $seizure->post_ictal_headache ? '✓' : '' }}</span> Post-ictal headache</p>
                    <p><span class="checkbox {{ $seizure->recent_medication_change ? 'checked' : '' }}">{{ $seizure->recent_medication_change ? '✓' : '' }}</span> Recent medication change</p>
                    <p><span class="checkbox {{ $seizure->experiencing_side_effects ? 'checked' : '' }}">{{ $seizure->experiencing_side_effects ? '✓' : '' }}</span> Experiencing side effects</p>
                </td>
            </tr>
        </table>
    </div>

    @if($seizure->triggers || $seizure->other_triggers)
        <div class="section keep-together">
            <h3>Triggers</h3>
            <div class="notes-box">
                @if($seizure->triggers && is_array($seizure->triggers))
                    <p><strong>Identified triggers:</strong> {{ implode(', ', $seizure->triggers) }}</p>
                @endif
                @if($seizure->other_triggers)
                    <p><strong>Other triggers:</strong> {{ $seizure->other_triggers }}</p>
                @endif
            </div>
        </div>
    @endif

    @if($seizure->pre_ictal_symptoms || $seizure->pre_ictal_notes)
        <div class="section keep-together">
            <h3>Pre-Ictal Information</h3>
            @if($seizure->pre_ictal_symptoms && is_array($seizure->pre_ictal_symptoms))
                <p><strong>Symptoms:</strong> {{ implode(', ', $seizure->pre_ictal_symptoms) }}</p>
            @endif
            @if($seizure->pre_ictal_notes)
                <div class="notes-box">
                    <strong>Notes:</strong><br>
                    {{ $seizure->pre_ictal_notes }}
                </div>
            @endif
        </div>
    @endif

    @if($seizure->nhs_contacted || $seizure->nhs_contact_type)
        <div class="section">
            <h3>Medical Contact</h3>
            <table class="detail-table">
                <tr>
                    <th>NHS Contacted:</th>
                    <td>{{ $seizure->nhs_contacted ? 'Yes' : 'No' }}</td>
                    <th>Contact Type:</th>
                    <td>{{ $seizure->nhs_contact_type ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
    @endif

    @if($medications->count() > 0)
        <div class="section">
            <h3>Active Medications at Time of Seizure</h3>
            <div class="medication-list">
                @foreach($medications as $medication)
                    <div class="medication-item">
                        <strong>{{ $medication->name }}</strong>
                        @if($medication->dosage)
                            - {{ $medication->dosage }}
                        @endif
                        @if($medication->frequency)
                            ({{ $medication->frequency }})
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($vitals->count() > 0)
        <div class="section">
            <h3>Vitals Recorded on {{ $seizure->start_time->format('F j, Y') }}</h3>
            <table class="vitals-grid">
                <tr>
                    @if($vitals->has('blood_pressure'))
                        <td class="vital-card">
                            <div class="vital-value">{{ $vitals['blood_pressure']->last()->value }}</div>
                            <div class="vital-label">Blood Pressure</div>
                        </td>
                    @endif
                    @if($vitals->has('heart_rate'))
                        <td class="vital-card">
                            <div class="vital-value">{{ $vitals['heart_rate']->last()->value }}</div>
                            <div class="vital-label">Heart Rate</div>
                        </td>
                    @endif
                    @if($vitals->has('temperature'))
                        <td class="vital-card">
                            <div class="vital-value">{{ $vitals['temperature']->last()->value }}°</div>
                            <div class="vital-label">Temperature</div>
                        </td>
                    @endif
                    @if($vitals->has('weight'))
                        <td class="vital-card">
                            <div class="vital-value">{{ $vitals['weight']->last()->value }}</div>
                            <div class="vital-label">Weight</div>
                        </td>
                    @endif
                </tr>
            </table>
        </div>
    @endif

    @if($seizure->notes || $seizure->recovery_notes || $seizure->medication_notes || $seizure->wellbeing_notes || $seizure->video_notes)
        <div class="section">
            <h3>Additional Notes</h3>

            @if($seizure->notes)
                <h4 style="margin: 15px 0 5px 0; color: #2d3748;">General Notes</h4>
                <div class="notes-box">{{ $seizure->notes }}</div>
            @endif

            @if($seizure->recovery_notes)
                <h4 style="margin: 15px 0 5px 0; color: #2d3748;">Recovery Notes</h4>
                <div class="notes-box">{{ $seizure->recovery_notes }}</div>
            @endif

            @if($seizure->medication_notes)
                <h4 style="margin: 15px 0 5px 0; color: #2d3748;">Medication Notes</h4>
                <div class="notes-box">{{ $seizure->medication_notes }}</div>
            @endif

            @if($seizure->wellbeing_notes)
                <h4 style="margin: 15px 0 5px 0; color: #2d3748;">Wellbeing Notes</h4>
                <div class="notes-box">{{ $seizure->wellbeing_notes }}</div>
            @endif

            @if($seizure->video_notes)
                <h4 style="margin: 15px 0 5px 0; color: #2d3748;">Video Evidence Notes</h4>
                <div class="notes-box">{{ $seizure->video_notes }}</div>
            @endif
        </div>
    @endif

    <div class="section">
        <h3>Additional Information</h3>
        <table class="detail-table">
            @if($seizure->wellbeing_rating)
                <tr>
                    <th>Wellbeing Rating:</th>
                    <td>{{ $seizure->wellbeing_rating }}/10</td>
                    <th>Sleep Quality:</th>
                    <td>{{ $seizure->sleep_quality ?? 'Not recorded' }}</td>
                </tr>
            @endif
            @if($seizure->days_since_period !== null)
                <tr>
                    <th>Days Since Period:</th>
                    <td>{{ $seizure->days_since_period }}</td>
                    <th>Medication Adherence:</th>
                    <td>{{ $seizure->medication_adherence ?? 'Not recorded' }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="footer keep-together">
        <p><strong>Important:</strong> This report is for medical reference only</p>
        <p>Please consult with your healthcare provider regarding any concerns about your seizure activity</p>
        <p>Generated by EpiCare Seizure Tracker on {{ now()->format('F j, Y \a\t g:i A T') }}</p>
    </div>
</body>
</html>
