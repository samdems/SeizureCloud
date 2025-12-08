@props(['user' => null])

@php
    $user = $user ?? auth()->user();

    if (!$user) {
        return;
    }

    if ($user->canTrackSeizures()) {
        return;
    }
@endphp

<div class="card bg-base-100 shadow-xl border-l-4 border-l-info">
    <div class="card-body">
        <div class="flex items-center gap-3 mb-4">
            <div class="text-4xl">🤝</div>
            <div>
                <h2 class="card-title text-xl">{{ ucfirst($user->account_type) }} Account</h2>
                <p class="text-base-content/70">Your account is set up for trusted access to patient records</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="card bg-base-200">
                <div class="card-body p-4">
                    <h3 class="font-semibold mb-2">How it works</h3>
                    <ul class="text-sm space-y-1">
                        <li>• Patients add you as a trusted contact</li>
                        <li>• You receive access to their seizure data</li>
                        <li>• Track seizures on their behalf</li>
                        <li>• View their medical information</li>
                    </ul>
                </div>
            </div>

            <div class="card bg-base-200">
                <div class="card-body p-4">
                    <h3 class="font-semibold mb-2">Getting started</h3>
                    <p class="text-sm mb-3">Ask a patient to invite you as a trusted contact from their settings.</p>
                    <a href="{{ route('settings.trusted-contacts.index') }}" class="btn btn-primary btn-sm">
                        View Trusted Access
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
