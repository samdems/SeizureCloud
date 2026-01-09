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
            font-size: 26px;
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

        .emergency-indicator {
            background: #fed7d7;
            color: #742a2a;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        /* Single seizure content CSS */
        .seizure-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .seizure-details td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .seizure-details .label {
            background: #f8f9fa;
            font-weight: bold;
            width: 25%;
            color: #2d3748;
        }

        .section-header {
            background: #e53e3e;
            color: white;
            padding: 12px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0 0 0;
        }

        .individual-seizure .section-header {
            background: #2d3748;
            color: white;
            padding: 10px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0 0 0;
            font-size: 14px;
        }

        .emergency-alert {
            background: #fed7d7;
            border: 2px solid #fc8181;
            color: #742a2a;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .emergency-alert h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
        }

        .severity-display {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .list-section {
            margin-bottom: 15px;
        }

        .list-section h4 {
            margin: 0 0 10px 0;
            color: #2d3748;
            font-size: 14px;
            font-weight: bold;
        }

        .list-item {
            margin-bottom: 5px;
            padding-left: 15px;
            position: relative;
        }

        .list-item:before {
            content: "-";
            position: absolute;
            left: 0;
            color: #e53e3e;
        }

        .checkbox-item {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox {
            width: 30px;
            height: 16px;
            border: 1px solid #333;
            display: inline-block;
            text-align: center;
            line-height: 14px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 3px;
        }

        .checked {
            background: #10b981;
            color: white;
        }

        .unchecked {
            background: #f3f4f6;
            color: #666;
        }

        .medication-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }

        .medication-table th {
            background: #f8f9fa;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #e2e8f0;
        }

        .medication-table td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .status-taken {
            color: #10b981;
            font-weight: bold;
        }

        .status-missed {
            color: #ef4444;
            font-weight: bold;
        }

        .status-late {
            color: #f59e0b;
            font-weight: bold;
        }

        .vitals-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .vital-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
            background: #fff;
        }

        .vital-card h5 {
            margin: 0 0 8px 0;
            font-size: 12px;
            font-weight: bold;
            color: #2d3748;
        }

        .vital-reading {
            margin-bottom: 8px;
            padding: 6px;
            background: #f8f9fa;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .vital-value {
            font-weight: bold;
            font-size: 13px;
        }

        .vital-time {
            font-size: 10px;
            color: #666;
        }

        .vital-abnormal {
            color: #ef4444;
        }

        .notes-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .notes-section h4 {
            margin: 0 0 10px 0;
            color: #2d3748;
        }

        .summary-section {
            page-break-inside: avoid;
            break-inside: avoid;
            margin-top: 30px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #666;
            font-size: 10px;
            page-break-inside: avoid;
        }

        .page-break {
            page-break-before: always;
        }

        .keep-together {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Override styles for individual seizure records */
        .individual-seizure {
            page-break-before: always;
            border-top: 3px solid #e53e3e;
            padding-top: 20px;
            margin-bottom: 30px;
        }

        .individual-seizure .header {
            border-bottom: 1px solid #e53e3e;
            margin-bottom: 20px;
            padding-bottom: 10px;
            text-align: center;
        }

        .individual-seizure .header h1 {
            font-size: 18px;
            color: #2d3748;
        }

        .individual-seizure .patient-info {
            display: none; /* Hide patient info in individual records to avoid repetition */
        }

        .individual-seizure .footer {
            display: none; /* Hide footer in individual records */
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
        <h1>Comprehensive Seizure Report</h1>
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
            Seizure Overview
        </h3>

        <table class="seizures-table keep-together">
            <thead>
                <tr>
                    <th style="width: 15%;">Date & Time</th>
                    <th style="width: 10%;">Duration</th>
                    <th style="width: 8%;">Severity</th>
                    <th style="width: 12%;">Type</th>
                    <th style="width: 5%;">Video</th>
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
                            @if($seizure->duration_minutes >= ($user->status_epilepticus_duration_minutes ?? 5))
                                <div class="emergency-indicator">Emergency</div>
                            @endif
                        </td>
                        <td class="duration">
                            @if($seizure->calculated_duration)
                                {{ $seizure->calculated_duration }} min
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
                        <td style="text-align: center;">
                            @if($seizure->hasValidVideo())
                                <div style="color: #10b981; font-weight: bold; font-size: 14px;" title="Video Available">📹</div>
                            @elseif($seizure->video_file_path)
                                <div style="color: #f59e0b; font-weight: bold; font-size: 12px;" title="Video Expired">⚠️</div>
                            @else
                                <div style="color: #cbd5e0; font-size: 12px;">—</div>
                            @endif
                        </td>
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

        <div class="summary-section keep-together" style="padding: 15px; background: #edf2f7; border-radius: 8px;">
            <h4 style="margin: 0 0 10px 0; color: #2d3748;">Monthly Summary Notes</h4>
            <ul style="margin: 0; padding-left: 20px; color: #4a5568;">
                <li>This report covers {{ $seizures->count() }} seizure(s) recorded during {{ $monthName }}</li>
                @if($totalDuration > 0)
                    <li>Total seizure time: {{ number_format($totalDuration / 60, 1) }} hours ({{ $totalDuration }} minutes)</li>
                @endif
                @if($averageSeverity)
                    <li>Average severity rating: {{ number_format($averageSeverity, 1) }} out of 10</li>
                @endif
                <li>Emergency threshold duration: {{ $user->status_epilepticus_duration_minutes ?? 5 }} minutes</li>
                @php
                    $emergencySeizures = $seizures->where('duration_minutes', '>=', $user->status_epilepticus_duration_minutes ?? 5);
                    $videosAvailable = $seizures->where('video_file_path', '!=', null)->count();
                    $validVideos = $seizures->filter(fn($s) => $s->hasValidVideo())->count();
                @endphp
                @if($emergencySeizures->count() > 0)
                    <li style="color: #e53e3e; font-weight: bold;">
                        WARNING: {{ $emergencySeizures->count() }} seizure(s) met emergency duration criteria
                    </li>
                @endif
                @if($videosAvailable > 0)
                    <li style="color: #2d3748;">
                        Video evidence: {{ $videosAvailable }} video(s) permanently accessible
                    </li>
                @endif
            </ul>
        </div>

        @if($validVideos > 0)
            <div class="summary-section keep-together" style="padding: 15px; background: #f0fff4; border-radius: 8px; margin-top: 15px;">
                <h4 style="margin: 0 0 15px 0; color: #2d3748;">Video Evidence Quick Access</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    @foreach($seizures->filter(fn($s) => $s->hasValidVideo()) as $seizure)
                        <div style="text-align: center; padding: 10px; border: 1px solid #c6f6d5; border-radius: 6px; background: white;">
                            <div style="font-size: 11px; font-weight: bold; margin-bottom: 8px; color: #2d3748;">
                                {{ $seizure->start_time->format('M j, Y H:i') }}
                            </div>
                            <img src="{{ app('App\Services\QrCodeService')->generateForPdf($seizure->getVideoPublicUrl(), 80) }}"
                                 style="max-width: 80px; height: auto; margin-bottom: 5px;"
                                 alt="QR Code for Video">
                            <div style="font-size: 8px; color: #666;">
                                Scan to view video
                            </div>
                        </div>
                    @endforeach
                </div>
                <div style="margin-top: 10px; font-size: 10px; color: #666; text-align: center;">
                    QR codes provide permanent direct access to seizure videos.
                </div>
            </div>
        @endif

        {{-- DETAILED INDIVIDUAL SEIZURE RECORDS --}}
        @foreach($seizuresDetailed as $index => $seizureData)
            @php
                $seizure = $seizureData['seizure'];
                $medications = $seizureData['medications'];
                $vitals = $seizureData['vitals'];
                $emergencyStatus = auth()->user()->getEmergencyStatus($seizure);
            @endphp

            <div class="individual-seizure">
                <div style="margin-bottom: 20px;">
                    {{-- Include the single seizure template --}}
                    @include('seizures.pdf.single-content', [
                        'seizure' => $seizure,
                        'user' => $user,
                        'medications' => $medications,
                        'vitals' => $vitals,
                        'emergencyStatus' => $emergencyStatus,
                        'showHeader' => true,
                        'recordNumber' => $index + 1
                    ])
                </div>
            </div>
        @endforeach

    @else
        <div class="keep-together" style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px; color: #666;">
            <h3 style="margin: 0 0 10px 0;">No seizures recorded</h3>
            <p style="margin: 0;">No seizure activity was recorded for {{ $monthName }}.</p>
        </div>
    @endif

    <div class="footer keep-together">
        <p><strong>Comprehensive Seizure Report</strong></p>
        <p>This report includes a monthly summary followed by detailed records for each seizure</p>
        <p>Generated by EpiCare Seizure Tracker on {{ now()->format('F j, Y \a\t g:i A T') }}</p>
        <p style="margin-top: 10px;">For medical purposes only - please consult with your healthcare provider</p>
    </div>
</body>
</html>
