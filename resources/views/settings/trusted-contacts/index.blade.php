<x-layouts.app :title="__('Trusted Contacts')">
    <x-settings.layout>
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Trusted Contacts</h2>
            <p class="text-base-content/70 mt-2">Manage who can access your account and which accounts you have access to</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Trusted Access Banner -->
        @if (session('viewing_as_trusted_contact') && session('original_user_id'))
            @php
                $originalUser = \App\Models\User::find(session('original_user_id'));
                $trustedUser = auth()->user();
            @endphp
            <div class="alert alert-info mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <div>You are viewing <strong>{{ $trustedUser->name }}'s</strong> account as a trusted contact.</div>
                    <div class="mt-2">
                        <form action="{{ route('trusted-access.switch-back') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline">
                                Switch back to your account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Accounts You Have Access To -->
            <div class="card bg-base-100 border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="card-title text-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8v2.25H5.5v2.25H3v-5.25l8.485-8.485c.404-.404.527-1 .43-1.563A6 6 0 0117 3a2 2 0 012 2z" />
                            </svg>
                            Accounts You Can Access
                        </h3>
                        <div class="badge badge-primary">{{ $accessibleAccounts->count() }}</div>
                    </div>

                    @if($accessibleAccounts->isEmpty())
                        <div class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-base-content/30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <p class="text-base-content/70">You don't have access to any accounts yet.</p>
                            <p class="text-sm text-base-content/50 mt-1">Account owners can grant you access to view their data.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($accessibleAccounts as $access)
                                <div class="card bg-base-200/50 border border-base-300/50">
                                    <div class="card-body p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <x-avatar :user="$access->user" size="md" />
                                                <div>
                                                    <h4 class="font-semibold">{{ $access->user->name }}</h4>
                                                    @if($access->nickname)
                                                        <p class="text-sm text-base-content/70">{{ $access->nickname }}</p>
                                                    @endif
                                                    <p class="text-xs text-base-content/50">
                                                        Access granted {{ $access->granted_at->diffForHumans() }}
                                                        @if($access->expires_at)
                                                            • Expires {{ $access->expires_at->diffForHumans() }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex gap-2">
                                                @unless(session('viewing_as_trusted_contact') && session('trusted_user_id') == $access->user_id)
                                                    <form action="{{ route('trusted-access.switch-to', $access->user) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            View Account
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="badge badge-success">Currently Viewing</span>
                                                @endif
                                                <form action="{{ route('settings.trusted-contacts.destroy', $access) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove your access to this account?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-error btn-outline">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Remove Access
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        @if($access->access_note)
                                            <p class="text-sm text-base-content/70 mt-2 pl-13">{{ $access->access_note }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Your Trusted Contacts -->
            <div class="card bg-base-100 border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="card-title text-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Your Trusted Contacts
                        </h3>
                        <div class="flex items-center gap-2">
                            <div class="badge badge-secondary">{{ $trustedContacts->where('is_active', true)->count() }} active</div>
                            <a href="{{ route('settings.trusted-contacts.create') }}" class="btn btn-sm btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Contact
                            </a>
                        </div>
                    </div>

                    @if($trustedContacts->isEmpty())
                        <div class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-base-content/30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="text-base-content/70">No trusted contacts yet.</p>
                            <p class="text-sm text-base-content/50 mt-1">Add trusted contacts who can access your account in case of emergency.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($trustedContacts as $contact)
                                <div class="card bg-base-200/50 border border-base-300/50">
                                    <div class="card-body p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <x-avatar :user="$contact->trustedUser" size="md" />
                                                <div>
                                                    <h4 class="font-semibold flex items-center gap-2">
                                                        {{ $contact->getDisplayName() }}
                                                        @if($contact->nickname)
                                                            <span class="badge badge-outline badge-sm">{{ $contact->trustedUser->name }}</span>
                                                        @endif
                                                    </h4>
                                                    <p class="text-sm text-base-content/70">{{ $contact->trustedUser->email }}</p>
                                                    <div class="flex items-center gap-2 text-xs text-base-content/50 mt-1">
                                                        <span>Access granted {{ $contact->granted_at->diffForHumans() }}</span>
                                                        @if($contact->expires_at)
                                                            <span>•</span>
                                                            <span class="{{ $contact->isExpired() ? 'text-error' : '' }}">
                                                                Expires {{ $contact->expires_at->diffForHumans() }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="flex flex-col items-end gap-1">
                                                    @if($contact->is_active)
                                                        <span class="badge badge-success badge-sm">Active</span>
                                                    @else
                                                        <span class="badge badge-error badge-sm">Inactive</span>
                                                    @endif
                                                    @if($contact->isExpired())
                                                        <span class="badge badge-warning badge-sm">Expired</span>
                                                    @endif
                                                </div>
                                                <div class="dropdown dropdown-end">
                                                    <div tabindex="0" role="button" class="btn btn-sm btn-ghost">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                        </svg>
                                                    </div>
                                                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                                        <li><a href="{{ route('settings.trusted-contacts.edit', $contact) }}">Edit</a></li>
                                                        <li>
                                                            <form action="{{ route('settings.trusted-contacts.toggle-status', $contact) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit">
                                                                    {{ $contact->is_active ? 'Deactivate' : 'Activate' }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('settings.trusted-contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke access for this trusted contact?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-error">Remove</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        @if($contact->access_note)
                                            <p class="text-sm text-base-content/70 mt-2 pl-13">{{ $contact->access_note }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-8">
            <div class="card bg-info/10 border border-info/20">
                <div class="card-body">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-info shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="font-semibold text-info mb-2">About Trusted Contacts</h4>
                            <div class="text-sm text-base-content/80 space-y-2">
                                <p><strong>Trusted contacts</strong> can access your account as if it were their own. This includes viewing all your seizures, medications, vitals, and settings.</p>
                                <p><strong>Security:</strong> Only grant access to people you completely trust, such as family members or caregivers who need to manage your health information in case of emergency.</p>
                                <p><strong>Control:</strong> You can activate, deactivate, or revoke access at any time. You can also set expiration dates for temporary access.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-settings.layout>
</x-layouts.app>
