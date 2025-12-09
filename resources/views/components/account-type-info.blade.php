@props(['type' => null])

@php
    $type = $type ?? auth()->user()->account_type;

    $typeInfo = match($type) {
        'patient' => [
            'title' => 'Patient Account',
            'description' => 'Full access to track seizures, medications, vitals and manage all health data',
            'icon' => '👤',
            'color' => 'success',
            'features' => [
                'Track your own seizures',
                'Manage medications and schedules',
                'Record vitals and health data',
                'Grant trusted access to carers',
                'Emergency settings and contacts',
                'Complete medical history'
            ]
        ],
        'carer' => [
            'title' => 'Carer Account',
            'description' => 'Trusted access to view and manage patient accounts you have permission for',
            'icon' => '🤝',
            'color' => 'info',
            'features' => [
                'View patient seizure records',
                'Track seizures for patients',
                'Access patient medication info',
                'View emergency settings',
                'Receive trusted access invitations',
                'Monitor multiple patient accounts'
            ]
        ],
        'medical' => [
            'title' => 'Medical Professional',
            'description' => 'Healthcare provider access for viewing patient data and medical records',
            'icon' => '👩‍⚕️',
            'color' => 'warning',
            'features' => [
                'Access patient medical records',
                'Review seizure patterns and data',
                'Monitor medication adherence',
                'View comprehensive health reports',
                'Emergency contact information',
                'Clinical data export capabilities'
            ]
        ],
        default => [
            'title' => 'Unknown Account',
            'description' => 'Account type not recognized',
            'icon' => '❓',
            'color' => 'neutral',
            'features' => []
        ]
    };
@endphp

<div class="card bg-base-100 shadow-lg border-l-4 border-l-{{ $typeInfo['color'] }}">
    <div class="card-body">
        <div class="flex items-center gap-3 mb-4">
            <div class="text-3xl">{{ $typeInfo['icon'] }}</div>
            <div>
                <h3 class="card-title text-{{ $typeInfo['color'] }}">{{ $typeInfo['title'] }}</h3>
                <p class="text-sm opacity-70">{{ $typeInfo['description'] }}</p>
            </div>
        </div>

        @if(!empty($typeInfo['features']))
            <div class="space-y-2">
                <h4 class="font-semibold text-sm">Account Features:</h4>
                <ul class="space-y-1">
                    @foreach($typeInfo['features'] as $feature)
                        <li class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-{{ $typeInfo['color'] }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($type === 'carer')
            <div class="mt-4 p-3 bg-info/10 rounded-lg">
                <p class="text-sm">
                    <strong>Need access to a patient account?</strong><br>
                    Ask the patient to add you as a trusted contact from their settings.
                </p>
            </div>
        @elseif($type === 'patient')
            <div class="mt-4 p-3 bg-success/10 rounded-lg">
                <p class="text-sm">
                    <strong>Want to give someone access?</strong><br>
                    You can add trusted contacts in your account settings to share access with carers or family.
                </p>
            </div>
        @endif
    </div>
</div>
