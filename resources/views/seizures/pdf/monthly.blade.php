<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Seizure Report - {{ $monthName }}</title>
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

        .seizures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: auto;
        }

        .seizures-table thead {
            page-break-after: avoid;
        }

        .seizures-table tr {
            page-break-inside: avoid;
        }

        .seizures-table th {
            background: #e53e3e;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .seizures-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .seizures-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .severity-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            min-width: 20px;
        }

        .severity-1-3 { background: #68d391; color: #22543d; }
        .severity-4-6 { background: #fbd38d; color: #744210; }
        .severity-7-10 { background: #fc8181; color: #742a2a; }

        .duration {
            font-weight: bold;
            color: #2d3748;
        }

        .notes {
            font-style: italic;
            color: #666;
            max-width: 200px;
            word-wrap: break-word;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .emergency-indicator {
            background: #fed7d7;
            color: #742a2a;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
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
            margin-top: 30px;
        }

        @media print {
            body { margin: 0; padding: 15px; }
            .page-break { page-break-before: always; }
            .keep-together { page-break-inside: avoid; }
            .summary-section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Monthly Seizure Report</h1>
        <p><strong>{{ $monthName }}</strong></p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="patient-info">
        <h3>Patient Information</h3>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Account Type:</strong> {{ ucfirst($user->account_type) }}</p>
        <p><strong>Report Period:</strong> {{ $startDate->format('F j, Y') }} - {{ $endDate->format('F j, Y') }}</p>
    </div>

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
        <h3 style="color: #2d3748; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
            Seizure Details
        </h3>

        <table class="seizures-table keep-together">
            <thead>
                <tr>
                    <th style="width: 15%;">Date & Time</th>
                    <th style="width: 10%;">Duration</th>
                    <th style="width: 10%;">Severity</th>
                    <th style="width: 15%;">Type</th>
                    <th style="width: 20%;">Triggers</th>
                    <th style="width: 30%;">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($seizures as $seizure)
                    <tr>
                        <td>
                            <div style="font-weight: bold;">{{ $seizure->start_time->format('M j, Y') }}</div>
                            <div style="font-size: 11px; color: #666;">{{ $seizure->start_time->format('g:i A') }}</div>
                            @if($seizure->duration_seconds >= (($user->status_epilepticus_duration_minutes ?? 5) * 60))
                                <div class="emergency-indicator">Emergency</div>
                            @endif
                        </td>
                        <td class="duration">
                            @if($seizure->calculated_duration)
                                {{ $seizure->formatted_duration }}
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
                            @if($seizure->triggers && is_array($seizure->triggers))
                                {{ implode(', ', $seizure->triggers) }}
                            @elseif($seizure->other_triggers)
                                {{ $seizure->other_triggers }}
                            @else
                                None specified
                            @endif
                        </td>
                        <td class="notes">
                            @if($seizure->notes)
                                {{ Str::limit($seizure->notes, 150) }}
                            @else
                                <em>No notes</em>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="keep-together" style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px; color: #666;">
            <h3 style="margin: 0 0 10px 0;">No seizures recorded</h3>
            <p style="margin: 0;">No seizure activity was recorded for {{ $monthName }}.</p>
        </div>
    @endif

    @if($seizures->count() > 0)
        <div class="summary-section keep-together" style="padding: 15px; background: #edf2f7; border-radius: 8px;">
            <h4 style="margin: 0 0 10px 0; color: #2d3748;">Summary Notes</h4>
            <ul style="margin: 0; padding-left: 20px; color: #4a5568;">
                <li>This report covers {{ $seizures->count() }} seizure(s) recorded during {{ $monthName }}</li>
                @if($totalDuration > 0)
                    <li>Total seizure time: {{ number_format($totalDuration / 3600, 1) }} hours ({{ floor($totalDuration / 60) }}m {{ $totalDuration % 60 }}s)</li>
                @endif
                @if($averageSeverity)
                    <li>Average severity rating: {{ number_format($averageSeverity, 1) }} out of 10</li>
                @endif
                <li>Emergency threshold duration: {{ $user->status_epilepticus_duration_minutes ?? 5 }} minutes</li>
                @php
                    $emergencySeizures = $seizures->where('duration_seconds', '>=', ($user->status_epilepticus_duration_minutes ?? 5) * 60);
                @endphp
                @if($emergencySeizures->count() > 0)
                    <li style="color: #e53e3e; font-weight: bold;">
                        WARNING: {{ $emergencySeizures->count() }} seizure(s) met emergency duration criteria
                    </li>
                @endif
            </ul>
        </div>
    @endif

    <div class="footer keep-together">
        <p>This report was generated by EpiCare Seizure Tracker</p>
        <p>For medical purposes only - please consult with your healthcare provider</p>
        <p>Generated: {{ now()->format('F j, Y \a\t g:i A T') }}</p>
    </div>
</body>
</html>
