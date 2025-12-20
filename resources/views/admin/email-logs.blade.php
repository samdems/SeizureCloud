<x-layouts.app :title="__('System Email Logs')">
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-base-content flex items-center gap-3">
                    <x-heroicon-o-envelope class="w-8 h-8 text-primary" />
                    System Email Logs
                </h1>
                <p class="text-base-content/70 mt-1">Monitor all email activity across the system</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Back to Dashboard
                </a>
                <button type="button" class="btn btn-success" onclick="refreshLogs()">
                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                    Refresh
                </button>
            </div>
        </div>

        <!-- Email Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-primary">
                    <x-heroicon-o-envelope class="w-8 h-8" />
                </div>
                <div class="stat-title">Total Emails</div>
                <div class="stat-value text-primary">{{ $stats['total_emails'] }}</div>
                <div class="stat-desc">All time</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-success">
                    <x-heroicon-o-check-circle class="w-8 h-8" />
                </div>
                <div class="stat-title">Sent Successfully</div>
                <div class="stat-value text-success">{{ $stats['sent_emails'] }}</div>
                <div class="stat-desc">{{ $stats['total_emails'] > 0 ? round(($stats['sent_emails'] / $stats['total_emails']) * 100, 1) : 0 }}% success rate</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-error">
                    <x-heroicon-o-x-circle class="w-8 h-8" />
                </div>
                <div class="stat-title">Failed</div>
                <div class="stat-value text-error">{{ $stats['failed_emails'] }}</div>
                <div class="stat-desc">Need attention</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-warning">
                    <x-heroicon-o-clock class="w-8 h-8" />
                </div>
                <div class="stat-title">Pending</div>
                <div class="stat-value text-warning">{{ $stats['pending_emails'] }}</div>
                <div class="stat-desc">In queue</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-info">
                    <x-heroicon-o-calendar class="w-8 h-8" />
                </div>
                <div class="stat-title">Today</div>
                <div class="stat-value text-info">{{ $stats['today_emails'] }}</div>
                <div class="stat-desc">{{ now()->format('M j') }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.email-logs') }}" class="flex flex-wrap gap-4">
                    <div class="form-control flex-1 min-w-64">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search by email or subject..."
                               class="input input-bordered w-full" />
                    </div>

                    <div class="form-control">
                        <select name="status" class="select select-bordered">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="bounced" {{ request('status') === 'bounced' ? 'selected' : '' }}>Bounced</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <select name="email_type" class="select select-bordered">
                            <option value="">All Types</option>
                            <option value="verification" {{ request('email_type') === 'verification' ? 'selected' : '' }}>Verification</option>
                            <option value="password_reset" {{ request('email_type') === 'password_reset' ? 'selected' : '' }}>Password Reset</option>
                            <option value="notification" {{ request('email_type') === 'notification' ? 'selected' : '' }}>Notification</option>
                            <option value="invitation" {{ request('email_type') === 'invitation' ? 'selected' : '' }}>Invitation</option>
                            <option value="emergency" {{ request('email_type') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                            <option value="reminder" {{ request('email_type') === 'reminder' ? 'selected' : '' }}>Reminder</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="input input-bordered" placeholder="From date" />
                    </div>

                    <div class="form-control">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="input input-bordered" placeholder="To date" />
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <x-heroicon-o-funnel class="w-4 h-4" />
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'email_type', 'date_from', 'date_to']))
                        <a href="{{ route('admin.email-logs') }}" class="btn btn-outline">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                            Clear
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Email Logs Table -->
        <div class="card bg-base-100 shadow">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Recipient</th>
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($emailLogs as $email)
                            <tr class="hover">
                                <td>
                                    <div class="font-medium">{{ $email->created_at->format('M j, Y') }}</div>
                                    <div class="text-sm opacity-70">{{ $email->created_at->format('g:i A') }}</div>
                                </td>
                                <td>
                                    @if($email->user)
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="mask mask-circle w-8 h-8">
                                                <img src="{{ $email->user->avatarUrl(32) }}" alt="{{ $email->user->name }}" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-medium text-sm">{{ $email->user->name }}</div>
                                            <div class="text-xs opacity-50">{{ $email->user->email }}</div>
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-sm opacity-50">System</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-sm">{{ $email->recipient_email }}</div>
                                        @if($email->recipient_name && $email->recipient_name !== $email->recipient_email)
                                        <div class="text-xs opacity-50">{{ $email->recipient_name }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $email->getTypeBadgeClass() }} badge-sm">
                                        {{ $email->getFormattedEmailType() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="max-w-xs">
                                        <div class="font-medium truncate">{{ $email->subject }}</div>
                                        @if($email->error_message)
                                            <div class="text-xs text-error truncate">{{ $email->error_message }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="flex flex-col gap-1">
                                        <span class="badge {{ $email->getStatusBadgeClass() }} badge-sm">
                                            {{ ucfirst($email->status) }}
                                        </span>
                                        @if($email->sent_at)
                                            <span class="text-xs opacity-70">{{ $email->sent_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown dropdown-end">
                                        <label tabindex="0" class="btn btn-ghost btn-xs">
                                            <x-heroicon-o-ellipsis-horizontal class="w-4 h-4" />
                                        </label>
                                        <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-64 z-10">
                                            <li>
                                                <a href="#" onclick="showEmailDetails({{ $email->id }})">
                                                    <x-heroicon-o-eye class="w-4 h-4" />
                                                    View Details
                                                </a>
                                            </li>
                                            @if($email->user)
                                            <li>
                                                <a href="{{ route('admin.users.show', $email->user) }}">
                                                    <x-heroicon-o-user class="w-4 h-4" />
                                                    View User
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.users.email-logs', $email->user) }}">
                                                    <x-heroicon-o-envelope class="w-4 h-4" />
                                                    User's Email Logs
                                                </a>
                                            </li>
                                            @endif
                                            @if($email->body)
                                            <li>
                                                <a href="#" onclick="showEmailBody({{ $email->id }})">
                                                    <x-heroicon-o-document-text class="w-4 h-4" />
                                                    View Content
                                                </a>
                                            </li>
                                            @endif
                                            @if($email->metadata)
                                            <li>
                                                <a href="#" onclick="showEmailMetadata({{ $email->id }})">
                                                    <x-heroicon-o-information-circle class="w-4 h-4" />
                                                    View Metadata
                                                </a>
                                            </li>
                                            @endif
                                            @if($email->failed() || $email->status === 'pending')
                                            <li>
                                                <a href="#" onclick="resendEmail({{ $email->id }})" class="text-warning">
                                                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                                                    Retry Send
                                                </a>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-4 text-base-content/50">
                                        <x-heroicon-o-envelope class="w-16 h-16" />
                                        <div>
                                            <h3 class="text-lg font-medium">No email logs found</h3>
                                            <p class="text-sm">No emails match your current filters.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($emailLogs->hasPages())
                <div class="card-actions justify-center p-4 border-t">
                    {{ $emailLogs->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Results Summary -->
        @if($emailLogs->count() > 0)
        <div class="alert">
            <x-heroicon-o-information-circle class="w-5 h-5" />
            <span>Showing {{ $emailLogs->firstItem() }} to {{ $emailLogs->lastItem() }} of {{ $emailLogs->total() }} email logs</span>
        </div>
        @endif

        <!-- System Health Alert -->
        @if($stats['failed_emails'] > 0 || $stats['pending_emails'] > 10)
        <div class="alert alert-warning">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
            <div>
                <h4 class="font-bold">Email System Health Notice</h4>
                <p class="text-sm">
                    @if($stats['failed_emails'] > 0)
                        There are {{ $stats['failed_emails'] }} failed emails that may need attention.
                    @endif
                    @if($stats['pending_emails'] > 10)
                        There are {{ $stats['pending_emails'] }} emails still pending delivery.
                    @endif
                </p>
            </div>
        </div>
        @endif
    </div>

    <!-- Email Detail Modal -->
    <input type="checkbox" id="emailDetailModal" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box max-w-4xl">
            <label for="emailDetailModal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
            <h3 class="font-bold text-lg mb-4">Email Details</h3>
            <div id="emailDetailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Email Body Modal -->
    <input type="checkbox" id="emailBodyModal" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box max-w-4xl">
            <label for="emailBodyModal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
            <h3 class="font-bold text-lg mb-4">Email Content</h3>
            <div id="emailBodyContent" class="prose max-w-none">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Email Metadata Modal -->
    <input type="checkbox" id="emailMetadataModal" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box max-w-2xl">
            <label for="emailMetadataModal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
            <h3 class="font-bold text-lg mb-4">Email Metadata</h3>
            <div id="emailMetadataContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        function refreshLogs() {
            window.location.reload();
        }

        function showEmailDetails(emailId) {
            fetch(`/admin/email-logs/${emailId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('emailDetailContent').innerHTML = `
                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div><strong>Recipient:</strong> ${data.recipient_email}</div>
                                    <div><strong>Name:</strong> ${data.recipient_name || 'N/A'}</div>
                                    <div><strong>Type:</strong> <span class="badge badge-outline">${data.email_type || 'N/A'}</span></div>
                                    <div><strong>Status:</strong> <span class="badge">${data.status}</span></div>
                                </div>
                                <div class="space-y-2">
                                    <div><strong>Provider:</strong> ${data.provider || 'N/A'}</div>
                                    <div><strong>Message ID:</strong> <span class="font-mono text-xs">${data.provider_message_id || 'N/A'}</span></div>
                                    <div><strong>Created:</strong> ${new Date(data.created_at).toLocaleString()}</div>
                                    ${data.sent_at ? `<div><strong>Sent:</strong> ${new Date(data.sent_at).toLocaleString()}</div>` : ''}
                                </div>
                            </div>

                            <div>
                                <strong>Subject:</strong>
                                <div class="mt-2 p-3 bg-base-200 rounded">${data.subject}</div>
                            </div>

                            ${data.error_message ? `
                            <div>
                                <strong class="text-error">Error Message:</strong>
                                <div class="mt-2 p-3 bg-error/10 text-error rounded">${data.error_message}</div>
                            </div>
                            ` : ''}

                            <div class="grid grid-cols-4 gap-4 text-center">
                                <div class="stat">
                                    <div class="stat-title text-xs">Sent</div>
                                    <div class="stat-value text-sm ${data.sent_at ? 'text-success' : 'text-base-content/50'}">
                                        ${data.sent_at ? '✓' : '✗'}
                                    </div>
                                </div>
                                <div class="stat">
                                    <div class="stat-title text-xs">Delivered</div>
                                    <div class="stat-value text-sm ${data.delivered_at ? 'text-success' : 'text-base-content/50'}">
                                        ${data.delivered_at ? '✓' : '✗'}
                                    </div>
                                </div>
                                <div class="stat">
                                    <div class="stat-title text-xs">Opened</div>
                                    <div class="stat-value text-sm ${data.opened_at ? 'text-info' : 'text-base-content/50'}">
                                        ${data.opened_at ? '✓' : '✗'}
                                    </div>
                                </div>
                                <div class="stat">
                                    <div class="stat-title text-xs">Clicked</div>
                                    <div class="stat-value text-sm ${data.clicked_at ? 'text-info' : 'text-base-content/50'}">
                                        ${data.clicked_at ? '✓' : '✗'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById('emailDetailModal').checked = true;
                })
                .catch(error => {
                    console.error('Error fetching email details:', error);
                    alert('Failed to load email details');
                });
        }

        function showEmailBody(emailId) {
            fetch(`/admin/email-logs/${emailId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('emailBodyContent').innerHTML = data.body || '<p class="text-center text-gray-500">No email body content available</p>';
                    document.getElementById('emailBodyModal').checked = true;
                })
                .catch(error => {
                    console.error('Error fetching email body:', error);
                    alert('Failed to load email content');
                });
        }

        function showEmailMetadata(emailId) {
            fetch(`/admin/email-logs/${emailId}`)
                .then(response => response.json())
                .then(data => {
                    const metadata = data.metadata || {};
                    document.getElementById('emailMetadataContent').innerHTML = `
                        <pre class="bg-base-200 p-4 rounded text-sm overflow-auto max-h-96">${JSON.stringify(metadata, null, 2)}</pre>
                    `;
                    document.getElementById('emailMetadataModal').checked = true;
                })
                .catch(error => {
                    console.error('Error fetching email metadata:', error);
                    alert('Failed to load email metadata');
                });
        }

        function resendEmail(emailId) {
            if (confirm('Are you sure you want to retry sending this email?')) {
                fetch(`/admin/email-logs/${emailId}/resend`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to resend email');
                    }
                })
                .catch(error => {
                    console.error('Error resending email:', error);
                    alert('Failed to resend email');
                });
            }
        }
    </script>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="toast toast-end">
        <div class="alert alert-success">
            <x-heroicon-o-check-circle class="w-6 h-6" />
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="toast toast-end">
        <div class="alert alert-error">
            <x-heroicon-o-x-circle class="w-6 h-6" />
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif
</x-layouts.app>
