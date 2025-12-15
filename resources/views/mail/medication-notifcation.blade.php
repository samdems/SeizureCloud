<x-mail::message>
@if($type === 'bulk')
# {{ $count }} {{ ucfirst($period) }} Medications {{ $isSkipped ?? false ? 'Skipped' : 'Taken' }}

{{ $patient->name }} has {{ $isSkipped ?? false ? 'skipped' : 'taken' }} {{ $count }} medications for their {{ $period }} schedule.

<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
@foreach($medications as $medication)
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 12px 8px; font-weight: bold; width: 40%;">{{ $medication['name'] }}</td>
        <td style="padding: 12px 8px;">{{ $medication['dosage'] }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px; color: #6b7280;">Scheduled for</td>
        <td style="padding: 8px;">{{ $medication['intended_time'] ?? 'N/A' }}</td>
    </tr>
    @if(isset($medication['is_late']) && $medication['is_late'])
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px; color: #6b7280;">Status</td>
        <td style="padding: 8px; color: #dc2626;">⚠️ Taken late at {{ $medication['taken_time'] ?? 'N/A' }}</td>
    </tr>
    @elseif(isset($medication['timing_info']) && $medication['timing_info'] !== 'On time')
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px; color: #6b7280;">Status</td>
        <td style="padding: 8px;">{{ $medication['timing_info'] }} at {{ $medication['taken_time'] ?? 'N/A' }}</td>
    </tr>
    @else
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px; color: #6b7280;">Status</td>
        <td style="padding: 8px; color: #16a34a;">✅ Taken on time at {{ $medication['taken_time'] ?? 'N/A' }}</td>
    </tr>
    @endif
    @if(!$loop->last)
    <tr><td colspan="2" style="padding: 10px;"></td></tr>
    @endif
@endforeach
</table>

@if($notes)
**Notes:** {{ $notes }}
@endif

@else
# Medication {{ $isSkipped ? 'Skipped' : 'Taken' }}

{{ $patient->name }} has {{ $isSkipped ? 'skipped' : 'taken' }} their medication.

<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 12px 8px; font-weight: bold; width: 40%;">{{ $medications->medication->name }}</td>
        <td style="padding: 12px 8px;">{{ $medications->dosage_taken ?? 'N/A' }}</td>
    </tr>
    @if($medications->intended_time)
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px; color: #6b7280;">Scheduled for</td>
        <td style="padding: 8px;">{{ $medications->intended_time->format('g:i A') }}</td>
    </tr>
    @endif
    @if(!$isSkipped)
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px; color: #6b7280;">Actually taken at</td>
        <td style="padding: 8px;">{{ $medications->taken_at->format('g:i A') }}</td>
    </tr>
    @if($medications->getTimeDifference() && $medications->getTimeDifference() !== 'On time')
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px; color: #6b7280;">Timing</td>
        <td style="padding: 8px; @if($medications->isTakenLate()) color: #dc2626; @endif">{{ $medications->getTimeDifference() }} @if($medications->isTakenLate()) ⚠️ @endif</td>
    </tr>
    @else
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px; color: #6b7280;">Timing</td>
        <td style="padding: 8px; color: #16a34a;">✅ On time</td>
    </tr>
    @endif
    @else
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px; color: #6b7280;">Skipped at</td>
        <td style="padding: 8px;">{{ $medications->taken_at->format('g:i A') }}</td>
    </tr>
    @if($medications->skip_reason)
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px; color: #6b7280;">Reason</td>
        <td style="padding: 8px;">{{ $medications->skip_reason }}</td>
    </tr>
    @endif
    @endif
    @if($medications->notes)
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px; color: #6b7280;">Notes</td>
        <td style="padding: 8px;">{{ $medications->notes }}</td>
    </tr>
    @endif
</table>

@endif

---

@if($recipient->id !== $patient->id)
*You are receiving this notification because you have trusted access to {{ $patient->name }}'s medical information.*
@else
*This is a confirmation that your medication has been logged.*
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
