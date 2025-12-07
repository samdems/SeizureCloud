<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprehensive Seizure Report - {{ $monthName }}</title>
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

        .patient-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .patient-info h3 {
            margin: 0 0 10px 0;
            color: #2d3748;
        }

        .stats-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            width: 25%;
            vertical-align: top;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #e53e3e;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .summary-table th {
            background: #e53e3e;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }

        .summary-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }

        .summary-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .seizure-detail {
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 30px;
            padding: 20px;
            page-break-before: always;
        }

        .seizure-detail:first-child {
            page-break-before: auto;
        }

        .seizure-header {
            background: #e53e3e;
            color: white;
            padding: 15px;
            margin: -20px -20px 20px -20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .seizure-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .seizure-datetime {
            font-size: 14px;
            margin: 5px 0 0 0;
            opacity: 0.9;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-item {
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            width: 33.33%;
            vertical-align: top;
            text-align: center;
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

        .detail-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .detail-section h4 {
            margin: 0 0 10px 0;
            color: #2d3748;
            font-size: 14px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .detail-table th {
            background: #edf2f7;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            width: 25%;
        }

        .detail-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }

        .severity-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
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
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 14px;
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

        .notes-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
            font-size: 11px;
        }

        .medication-list, .vitals-list {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
        }

        .medication-item {
            border-bottom: 1px solid #f1f5f9;
            padding: 5px 0;
            font-size: 11px;
        }

        .medication-item:last-child {
            border-bottom: none;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        .keep-together {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .summary-section {
            page-break-inside: avoid;
            break-inside: avoid;
            margin-bottom: 30px;
        }

        @media print {
            body { margin: 0; padding: 15px; }
            .page-break { page-break-before: always; }
            .keep-together { page-break-inside: avoid; }
            .summary-section { page-break-inside: avoid; }
            .seizure-detail { page-break-before: always; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comprehensive Seizure Report</h1>
        <p><strong>{{ $monthName }}</strong></p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="patient-info keep-together">
        <h3>Patient Information</h3>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Account Type:</strong> {{ ucfirst($user->account_type) }}</p>
        <p><strong>Report Period:</strong> {{ $startDate->format('F j, Y') }} - {{ $endDate->format('F j, Y') }}</p>
    </div>

    <div class="summary-section">
        <h3 style="color: #2d3748; margin-bottom: 15px; border-bottom: 2px solid #e53e3e; padding-bottom: 10px;">
            📊 Monthly Summary
        </h3>

        <table class="stats-grid">
            <tr>
                <td class="stat-card">
                    <div class="stat-value">{{ $totalSeizures }}</div>
                    <div class="stat-label">Total Seizures</div>
                </td>
                <td class="stat-card">
                    <div class="stat-value">{{ $averageSeverity ? number_format($averageSeverity, 1) : 'N/A' }}</div>
                    <div class="stat-label">Average Severity</div>
                </td>
                <td class="stat-card">
                    <div class="stat-value">{{ $totalDuration ? number_format($totalDuration / 60, 1) : '0' }}</div>
                    <div class="stat-label">Total Hours</div>
                </td>
                <td class="stat-card">
                    <div class="stat-value">{{ $longestSeizure ?? 0 }}</div>
                    <div class="stat-label">Longest (min)</div>
                </td>
            </tr>
        </table>

        @if($seizures->count() > 0)
            <h4 style="color: #2d3748; margin: 20px 0 10px 0;">Seizure Overview</h4>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Date & Time</th>
                        <th style="width: 15%;">Duration</th>
                        <th style="width: 15%;">Severity</th>
                        <th style="width: 20%;">Type</th>
                        <th style="width: 30%;">Notes Summary</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seizures as $seizure)
                        <tr>
                            <td>
                                <strong>{{ $seizure->start_time->format('M j, Y') }}</strong><br>
                                <small>{{ $seizure->start_time->format('g:i A') }}</small>
                                @if($seizure->duration_minutes >= ($user->status_epilepticus_duration_minutes ?? 5))
                                    <br><span style="color: #e53e3e; font-weight: bold; font-size: 9px;">EMERGENCY</span>
                                @endif
                            </td>
                            <td>
                                @if($seizure->calculated_duration)
                                    <strong>{{ $seizure->calculated_duration }} min</strong>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($seizure->severity)
                                    <span class="severity-badge
                                        @if($seizure->severity <= 3) severity-1-3
                                        @elseif($seizure->severity <= 6) severity-4-6
                                        @else severity-7-10
                                        @endif">
                                        {{ $seizure->severity }}/10
                                    </span>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $seizure->seizure_type ?? 'Not specified' }}</td>
                            <td>
                                @if($seizure->notes)
                                    {{ Str::limit($seizure->notes, 80) }}
                                @else
                                    <em>No notes</em>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="padding: 15px; background: #edf2f7; border-radius: 8px; margin-top: 20px;">
                <h4 style="margin: 0 0 10px 0; color: #2d3748;">Summary Insights</h4>
                <ul style="margin: 0; padding-left: 20px; color: #4a5568; font-size: 11px;">
                    <li>{{ $seizures->count() }} seizure(s) recorded during {{ $monthName }}</li>
                    @if($totalDuration > 0)
                        <li>Total seizure time: {{ number_format($totalDuration / 60, 1) }} hours</li>
                    @endif
                    @if($averageSeverity)
                        <li>Average severity: {{ number_format($averageSeverity, 1) }} out of 10</li>
                    @endif
                    @php
                        $emergencySeizures = $seizures->where('duration_minutes', '>=', $user->status_epilepticus_duration_minutes ?? 5);
                    @endphp
                    @if($emergencySeizures->count() > 0)
                        <li style="color: #e53e3e; font-weight: bold;">
                            ⚠️ {{ $emergencySeizures->count() }} seizure(s) met emergency criteria
                        </li>
                    @endif
                </ul>
            </div>
        @else
            <div class="keep-together" style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px; color: #666;">
                <h3 style="margin: 0 0 10px 0;">No seizures recorded</h3>
                <p style="margin: 0;">No seizure activity was recorded for {{ $monthName }}.</p>
            </div>
        @endif
    </div>

    @if($seizuresDetailed->count() > 0)
        <div class="page-break"></div>
        <h2 style="color: #2d3748; text-align: center; margin-bottom: 30px; border-bottom: 2px solid #e53e3e; padding-bottom: 15px;">
            📋 Detailed Seizure Reports
        </h2>

        @foreach($seizuresDetailed as $index => $seizureData)
            @php
                $seizure = $seizureData['seizure'];
                $medications = $seizureData['medications'];
                $vitals = $seizureData['vitals'];
            @endphp

            <div class="seizure-detail">
                <div class="seizure-header">
                    <h3 class="seizure-title">Seizure #{{ $seizure->id }} - Detailed Report</h3>
                    <p class="seizure-datetime">{{ $seizure->start_time->format('F j, Y \a\t g:i A') }}</p>
                </div>

                @if($seizure->duration_minutes >= ($user->status_epilepticus_duration_minutes ?? 5))
                    <div class="emergency-banner">
                        ⚠️ EMERGENCY: This seizure met status epilepticus criteria
                        (Duration: {{ $seizure->duration_minutes }} minutes, Threshold: {{ $user->status_epilepticus_duration_minutes ?? 5 }} minutes)
                    </div>
                @endif

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

                <div class="detail-section">
                    <h4>Seizure Details</h4>
                    <table class="detail-table">
                        <tr>
                            <th>End Time:</th>
                            <td>{{ $seizure->end_time ? $seizure->end_time->format('g:i A') : 'Not recorded' }}</td>
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

                    <h5 style="margin: 15px 0 10px 0; color: #2d3748;">Characteristics</h5>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                                <p><span class="checkbox {{ $seizure->on_period ? 'checked' : '' }}">{{ $seizure->on_period ? '✓' : '' }}</span> On menstrual period</p>
                                <p><span class="checkbox {{ $seizure->ambulance_called ? 'checked' : '' }}">{{ $seizure->ambulance_called ? '✓' : '' }}</span> Ambulance called</p>
                                <p><span class="checkbox {{ $seizure->slept_after ? 'checked' : '' }}">{{ $seizure->slept_after ? '✓' : '' }}</span> Slept after seizure</p>
                                <p><span class="checkbox {{ $seizure->has_video_evidence ? 'checked' : '' }}">{{ $seizure->has_video_evidence ? '✓' : '' }}</span> Video evidence</p>
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
                    <div class="detail-section">
                        <h4>Triggers</h4>
                        @if($seizure->triggers && is_array($seizure->triggers))
                            <p><strong>Identified:</strong> {{ implode(', ', $seizure->triggers) }}</p>
                        @endif
                        @if($seizure->other_triggers)
                            <p><strong>Other:</strong> {{ $seizure->other_triggers }}</p>
                        @endif
                    </div>
                @endif

                @if($medications->count() > 0)
                    <div class="detail-section">
                        <h4>Active Medications</h4>
                        <div class="medication-list">
                            @foreach($medications as $medication)
                                <div class="medication-item">
                                    <strong>{{ $medication->name }}</strong>
                                    @if($medication->dosage) - {{ $medication->dosage }} @endif
                                    @if($medication->frequency) ({{ $medication->frequency }}) @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($vitals->count() > 0)
                    <div class="detail-section">
                        <h4>Vitals from {{ $seizure->start_time->format('M j, Y') }}</h4>
                        <div class="vitals-list">
                            @foreach($vitals as $type => $vitalCollection)
                                <div class="medication-item">
                                    <strong>{{ ucfirst(str_replace('_', ' ', $type)) }}:</strong> {{ $vitalCollection->last()->value }}
                                    @if($type == 'temperature') ° @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($seizure->notes || $seizure->recovery_notes || $seizure->medication_notes)
                    <div class="detail-section">
                        <h4>Notes</h4>
                        @if($seizure->notes)
                            <div class="notes-box">
                                <strong>General Notes:</strong><br>{{ $seizure->notes }}
                            </div>
                        @endif
                        @if($seizure->recovery_notes)
                            <div class="notes-box">
                                <strong>Recovery Notes:</strong><br>{{ $seizure->recovery_notes }}
                            </div>
                        @endif
                        @if($seizure->medication_notes)
                            <div class="notes-box">
                                <strong>Medication Notes:</strong><br>{{ $seizure->medication_notes }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    @endif

    <div class="footer keep-together">
        <p><strong>Comprehensive Seizure Report</strong> - Generated by EpiCare</p>
        <p>This report contains both summary statistics and detailed individual seizure information</p>
        <p>For medical purposes only - please consult with your healthcare provider</p>
        <p>Generated: {{ now()->format('F j, Y \a\t g:i A T') }}</p>
    </div>
</body>
</html>
