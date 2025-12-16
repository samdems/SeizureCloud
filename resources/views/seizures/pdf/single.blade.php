<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seizure Record - {{ $seizure->start_time->format('M d, Y') }}</title>
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

        .severity-star {
            width: 16px;
            height: 16px;
            display: inline-block;
        }

        .severity-filled {
            color: #e53e3e;
        }

        .severity-empty {
            color: #e2e8f0;
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
        }

        @media print {
            body { margin: 0; padding: 15px; }
            .page-break { page-break-before: always; }
            .keep-together { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    {{-- Include the reusable single seizure content --}}
    @include('seizures.pdf.single-content', [
        'seizure' => $seizure,
        'user' => $user,
        'medications' => $medications,
        'vitals' => $vitals,
        'emergencyStatus' => $emergencyStatus,
        'showHeader' => true
    ])
</body>
</html>
