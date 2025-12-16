<x-mail::message>
@if($type === 'bulk')
# {{ $count }} {{ ucfirst($period) }} Medications {{ $isSkipped ?? false ? 'Skipped' : 'Taken' }}

{{ $patient->name }} has {{ $isSkipped ?? false ? 'skipped' : 'taken' }} {{ $count }} medications for their {{ $period }} schedule.

**Medications:**
@foreach($medications as $medication)
• **{{ $medication['name'] }}** ({{ $medication['dosage'] }})
@if(isset($medication['timing_info']) && $medication['timing_info'])
  - {{ $medication['timing_info'] }}
@endif
@if(isset($medication['is_late']) && $medication['is_late'])
  - ⚠️ Taken late
@endif
@endforeach

@if($notes)
**Notes:** {{ $notes }}
@endif

@else
# Medication {{ $isSkipped ? 'Skipped' : 'Taken' }}

{{ $patient->name }} has {{ $isSkipped ? 'skipped' : 'taken' }} their medication.

**Medication:** {{ $medications->medication->name }}
@if(!$isSkipped)
**Dosage:** {{ $medications->dosage_taken }}
**Time:** {{ $medications->taken_at->format('M j, Y \a\t g:i A') }}
@if($medications->getTimeDifference())
**Timing:** {{ $medications->getTimeDifference() }}
@endif
@if($medications->isTakenLate())
⚠️ **This medication was taken late**
@endif
@else
**Skipped at:** {{ $medications->taken_at->format('M j, Y \a\t g:i A') }}
@if($medications->skip_reason)
**Reason:** {{ $medications->skip_reason }}
@endif
@endif

@if($medications->notes)
**Notes:** {{ $medications->notes }}
@endif

@endif

@if($recipient->id !== $patient->id)
You are receiving this notification because you have trusted access to {{ $patient->name }}'s medical information.
@else
This is a confirmation that your medication has been logged.
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
