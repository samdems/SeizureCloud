<x-mail::message>
@if(count($overdueMedications) > 0 && count($dueMedications) > 0)
# Medication Reminder: Overdue and Due Medications

@if($recipient->id === $patient->id)
Hello {{ $patient->name }},

You have medications that need your attention:
@else
Hello {{ $recipient->name }},

{{ $patient->name }} has medications that need attention:
@endif

## ⚠️ Overdue Medications ({{ count($overdueMedications) }})

<table style="width: 100%; border-collapse: collapse; margin: 20px 0; border: 2px solid #dc2626;">
@foreach($overdueMedications as $schedule)
    <tr style="border-bottom: 1px solid #fca5a5; background-color: #fef2f2;">
        <td style="padding: 12px 8px; font-weight: bold; width: 40%;">{{ $schedule->medication->name }}</td>
        <td style="padding: 12px 8px;">{{ $schedule->getCalculatedDosageWithUnit() ?: $schedule->medication->dosage . ' ' . $schedule->medication->unit }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #fca5a5; background-color: #fef2f2;">
        <td style="padding: 8px; color: #991b1b;">Was due at</td>
        <td style="padding: 8px; color: #991b1b; font-weight: bold;">{{ $schedule->scheduled_time->format('g:i A') }}</td>
    </tr>
    @if(!$loop->last)
    <tr><td colspan="2" style="padding: 5px;"></td></tr>
    @endif
@endforeach
</table>

## 🔔 Currently Due Medications ({{ count($dueMedications) }})

<table style="width: 100%; border-collapse: collapse; margin: 20px 0; border: 2px solid #f59e0b;">
@foreach($dueMedications as $schedule)
    <tr style="border-bottom: 1px solid #fcd34d; background-color: #fffbeb;">
        <td style="padding: 12px 8px; font-weight: bold; width: 40%;">{{ $schedule->medication->name }}</td>
        <td style="padding: 12px 8px;">{{ $schedule->getCalculatedDosageWithUnit() ?: $schedule->medication->dosage . ' ' . $schedule->medication->unit }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #fcd34d; background-color: #fffbeb;">
        <td style="padding: 8px; color: #92400e;">Due at</td>
        <td style="padding: 8px; color: #92400e; font-weight: bold;">{{ $schedule->scheduled_time->format('g:i A') }}</td>
    </tr>
    @if(!$loop->last)
    <tr><td colspan="2" style="padding: 5px;"></td></tr>
    @endif
@endforeach
</table>

@elseif(count($overdueMedications) > 0)
# ⚠️ Medication Reminder: {{ count($overdueMedications) }} Overdue Medication{{ count($overdueMedications) > 1 ? 's' : '' }}

@if($recipient->id === $patient->id)
Hello {{ $patient->name }},

You have {{ count($overdueMedications) }} overdue medication{{ count($overdueMedications) > 1 ? 's' : '' }} that need{{ count($overdueMedications) === 1 ? 's' : '' }} to be taken.
@else
Hello {{ $recipient->name }},

{{ $patient->name }} has {{ count($overdueMedications) }} overdue medication{{ count($overdueMedications) > 1 ? 's' : '' }} that need{{ count($overdueMedications) === 1 ? 's' : '' }} to be taken.
@endif

<table style="width: 100%; border-collapse: collapse; margin: 20px 0; border: 2px solid #dc2626;">
@foreach($overdueMedications as $schedule)
    <tr style="border-bottom: 1px solid #fca5a5; background-color: #fef2f2;">
        <td style="padding: 12px 8px; font-weight: bold; width: 40%;">{{ $schedule->medication->name }}</td>
        <td style="padding: 12px 8px;">{{ $schedule->getCalculatedDosageWithUnit() ?: $schedule->medication->dosage . ' ' . $schedule->medication->unit }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #fca5a5; background-color: #fef2f2;">
        <td style="padding: 8px; color: #991b1b;">Was due at</td>
        <td style="padding: 8px; color: #991b1b; font-weight: bold;">{{ $schedule->scheduled_time->format('g:i A') }}</td>
    </tr>
    @if(!$loop->last)
    <tr><td colspan="2" style="padding: 10px;"></td></tr>
    @endif
@endforeach
</table>

@else
# 🔔 Medication Reminder: {{ count($dueMedications) }} Medication{{ count($dueMedications) > 1 ? 's' : '' }} Due

@if($recipient->id === $patient->id)
Hello {{ $patient->name }},

You have {{ count($dueMedications) }} medication{{ count($dueMedications) > 1 ? 's' : '' }} that {{ count($dueMedications) === 1 ? 'is' : 'are' }} currently due.
@else
Hello {{ $recipient->name }},

{{ $patient->name }} has {{ count($dueMedications) }} medication{{ count($dueMedications) > 1 ? 's' : '' }} that {{ count($dueMedications) === 1 ? 'is' : 'are' }} currently due.
@endif

<table style="width: 100%; border-collapse: collapse; margin: 20px 0; border: 2px solid #f59e0b;">
@foreach($dueMedications as $schedule)
    <tr style="border-bottom: 1px solid #fcd34d; background-color: #fffbeb;">
        <td style="padding: 12px 8px; font-weight: bold; width: 40%;">{{ $schedule->medication->name }}</td>
        <td style="padding: 12px 8px;">{{ $schedule->getCalculatedDosageWithUnit() ?: $schedule->medication->dosage . ' ' . $schedule->medication->unit }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #fcd34d; background-color: #fffbeb;">
        <td style="padding: 8px; color: #92400e;">Due at</td>
        <td style="padding: 8px; color: #92400e; font-weight: bold;">{{ $schedule->scheduled_time->format('g:i A') }}</td>
    </tr>
    @if(!$loop->last)
    <tr><td colspan="2" style="padding: 10px;"></td></tr>
    @endif
@endforeach
</table>

@endif

@if($recipient->id === $patient->id)
Please ensure your medications are taken as prescribed. If you have already taken these medications, please log them in your diary.
@else
Please check on {{ $patient->name }} to ensure their medications are taken as prescribed.
@endif

<x-mail::button :url="url('/medications')" color="primary">
@if($recipient->id === $patient->id)
View My Medication Schedule
@else
View {{ $patient->name }}'s Schedule
@endif
</x-mail::button>

@if(count($overdueMedications) > 0)
<div style="background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 16px; margin: 20px 0;">
<strong style="color: #991b1b;">⚠️ Important:</strong> Overdue medications should be taken as soon as possible. If there are concerns about missed doses or timing conflicts, please contact your healthcare provider.
</div>
@endif

If you have any concerns about the medication schedule or dosing, please contact your healthcare provider immediately.

---

@if($recipient->id !== $patient->id)
*You are receiving this notification because you have trusted access to {{ $patient->name }}'s medical information and medication reminders are enabled.*

**Patient:** {{ $patient->name }} ({{ $patient->email }})
@else
*This is an automated reminder to help you stay on track with your medication schedule.*
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
