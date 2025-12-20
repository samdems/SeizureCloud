<x-layouts.auth :title="__('Email Preview')">
    <div class="min-h-screen bg-gray-100 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Email Preview</h1>
                <p class="text-gray-600 mt-2">Preview of the invitation email that would be sent</p>
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <strong>Testing Mode:</strong> This preview is only available in local development.
                    </p>
                </div>
            </div>

            <!-- Invitation Details -->
            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Invitation Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">From:</span>
                        <span class="text-gray-900">{{ $invitation->inviter->name }} ({{ $invitation->inviter->email }})</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">To:</span>
                        <span class="text-gray-900">{{ $invitation->email }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Token:</span>
                        <span class="text-gray-900 font-mono text-xs">{{ $invitation->token }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Status:</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                              {{ $invitation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($invitation->status) }}
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Created:</span>
                        <span class="text-gray-900">{{ $invitation->created_at->format('M j, Y g:i A') }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Expires:</span>
                        <span class="text-gray-900 {{ $invitation->invitation_expires_at->isPast() ? 'text-red-600' : '' }}">
                            {{ $invitation->invitation_expires_at->format('M j, Y g:i A') }}
                            ({{ $invitation->invitation_expires_at->diffForHumans() }})
                        </span>
                    </div>
                    @if($invitation->nickname)
                    <div>
                        <span class="font-medium text-gray-700">Nickname:</span>
                        <span class="text-gray-900">{{ $invitation->nickname }}</span>
                    </div>
                    @endif
                    @if($invitation->access_note)
                    <div class="md:col-span-2">
                        <span class="font-medium text-gray-700">Access Note:</span>
                        <span class="text-gray-900">{{ $invitation->access_note }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Email Preview -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Email Content</h2>
                        <div class="text-sm text-gray-500">
                            Subject: {{ $mailMessage->subject }}
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Email Header -->
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <div class="text-sm text-gray-600 mb-2">
                            <strong>From:</strong> {{ config('mail.from.name') }} &lt;{{ config('mail.from.address') }}&gt;
                        </div>
                        <div class="text-sm text-gray-600 mb-2">
                            <strong>To:</strong> {{ $invitation->email }}
                        </div>
                        <div class="text-sm text-gray-600">
                            <strong>Subject:</strong> {{ $mailMessage->subject }}
                        </div>
                    </div>

                    <!-- Email Body -->
                    <div class="prose max-w-none">
                        <!-- Greeting -->
                        @if($mailMessage->greeting)
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $mailMessage->greeting }}</h2>
                        @endif

                        <!-- Introduction Lines -->
                        @if($mailMessage->introLines)
                        <div class="space-y-3 mb-6">
                            @foreach($mailMessage->introLines as $line)
                            <p class="text-gray-700 leading-relaxed">{{ $line }}</p>
                            @endforeach
                        </div>
                        @endif

                        <!-- Action Button -->
                        @if($mailMessage->actionText && $mailMessage->actionUrl)
                        <div class="text-center my-8">
                            <div class="inline-block">
                                <a href="{{ $mailMessage->actionUrl }}"
                                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-decoration-none shadow-sm transition-colors">
                                    {{ $mailMessage->actionText }}
                                </a>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                Link: <a href="{{ $mailMessage->actionUrl }}" class="text-blue-600 underline">{{ $mailMessage->actionUrl }}</a>
                            </p>
                        </div>
                        @endif

                        <!-- Outro Lines -->
                        @if($mailMessage->outroLines)
                        <div class="space-y-3 mt-6">
                            @foreach($mailMessage->outroLines as $line)
                            <p class="text-gray-700 leading-relaxed">{{ $line }}</p>
                            @endforeach
                        </div>
                        @endif

                        <!-- Salutation -->
                        @if($mailMessage->salutation)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <p class="text-gray-700">{{ $mailMessage->salutation }}</p>
                        </div>
                        @else
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <p class="text-gray-700">
                                Regards,<br>
                                {{ config('app.name') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('invitation.show', $invitation->token) }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    View Actual Invitation Page
                </a>

                @auth
                    @if(auth()->user()->id === $invitation->inviter_id)
                    <a href="{{ route('settings.trusted-contacts.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                        Back to Trusted Contacts
                    </a>
                    @endif
                @endauth

                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition-colors">
                    Dashboard
                </a>
            </div>

            <!-- Debug Info -->
            @if(config('app.debug'))
            <div class="mt-8 bg-gray-800 text-gray-100 rounded-lg p-4 text-sm font-mono">
                <h3 class="text-white font-semibold mb-2">Debug Information</h3>
                <div class="space-y-1">
                    <div><span class="text-yellow-400">Mail Driver:</span> {{ config('mail.default') }}</div>
                    <div><span class="text-yellow-400">From Address:</span> {{ config('mail.from.address') }}</div>
                    <div><span class="text-yellow-400">From Name:</span> {{ config('mail.from.name') }}</div>
                    <div><span class="text-yellow-400">Environment:</span> {{ config('app.env') }}</div>
                    <div><span class="text-yellow-400">Debug Mode:</span> {{ config('app.debug') ? 'true' : 'false' }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts.auth>
