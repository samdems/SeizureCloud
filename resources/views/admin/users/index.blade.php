<x-layouts.app :title="__('User Management')">
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-base-content flex items-center gap-3">
                    <x-heroicon-o-users class="w-8 h-8 text-primary" />
                    User Management
                </h1>
                <p class="text-base-content/70 mt-1">Manage all user accounts and permissions</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Back to Dashboard
                </a>
                <a href="{{ route('admin.export.users') }}" class="btn btn-success">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    Export CSV
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-4">
                    <div class="form-control flex-1 min-w-64">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search by name or email..."
                               class="input input-bordered w-full" />
                    </div>

                    <div class="form-control">
                        <select name="account_type" class="select select-bordered">
                            <option value="">All Account Types</option>
                            <option value="patient" {{ request('account_type') === 'patient' ? 'selected' : '' }}>Patient</option>
                            <option value="carer" {{ request('account_type') === 'carer' ? 'selected' : '' }}>Carer</option>
                            <option value="medical" {{ request('account_type') === 'medical' ? 'selected' : '' }}>Medical</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <select name="admin_filter" class="select select-bordered">
                            <option value="">All Users</option>
                            <option value="admin" {{ request('admin_filter') === 'admin' ? 'selected' : '' }}>Admin Only</option>
                            <option value="regular" {{ request('admin_filter') === 'regular' ? 'selected' : '' }}>Regular Only</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <x-heroicon-o-funnel class="w-4 h-4" />
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'account_type', 'admin_filter']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                            Clear
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card bg-base-100 shadow">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Account Type</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="hover">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="mask mask-circle w-12 h-12">
                                                <img src="{{ $user->avatarUrl(48) }}" alt="{{ $user->name }}" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold">{{ $user->name }}</div>
                                            <div class="text-sm opacity-50">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->account_type === 'patient')
                                        <span class="badge badge-success">Patient</span>
                                    @elseif($user->account_type === 'carer')
                                        <span class="badge badge-info">Carer</span>
                                    @else
                                        <span class="badge badge-accent">Medical</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-1">
                                        @if($user->isAdmin())
                                        <span class="badge badge-error badge-sm">Admin</span>
                                        @endif
                                        @if($user->hasVerifiedEmail())
                                        <span class="badge badge-success badge-sm">Verified</span>
                                        @else
                                        <span class="badge badge-warning badge-sm">Unverified</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">{{ $user->created_at->format('M j, Y') }}</div>
                                    <div class="text-xs opacity-50">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <div class="dropdown dropdown-end">
                                        <label tabindex="0" class="btn btn-ghost btn-sm">
                                            <x-heroicon-o-ellipsis-horizontal class="w-4 h-4" />
                                        </label>
                                        <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-72 z-10">
                                            <li>
                                                <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-2">
                                                    <x-heroicon-o-eye class="w-4 h-4" />
                                                    View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.users.edit', $user) }}" class="flex items-center gap-2">
                                                    <x-heroicon-o-pencil class="w-4 h-4" />
                                                    Edit User
                                                </a>
                                            </li>

                                            @if($user->id !== auth()->id())
                                            <div class="divider my-1"></div>

                                            <li>
                                                <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="w-full">
                                                    @csrf
                                                    <button type="submit"
                                                            class="flex items-center gap-2 w-full text-orange-600"
                                                            onclick="return confirm('Are you sure you want to {{ $user->isAdmin() ? 'demote this user from admin' : 'promote this user to admin' }}?')">
                                                        @if($user->isAdmin())
                                                            <x-heroicon-o-arrow-down class="w-4 h-4" />
                                                            Demote from Admin
                                                        @else
                                                            <x-heroicon-o-arrow-up class="w-4 h-4" />
                                                            Promote to Admin
                                                        @endif
                                                    </button>
                                                </form>
                                            </li>

                                            @if(!$user->hasVerifiedEmail())
                                            <li>
                                                <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="w-full">
                                                    @csrf
                                                    <button type="submit" class="flex items-center gap-2 w-full text-green-600">
                                                        <x-heroicon-o-check-circle class="w-4 h-4" />
                                                        Activate Account
                                                    </button>
                                                </form>
                                            </li>
                                            @else
                                            <li>
                                                <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="w-full">
                                                    @csrf
                                                    <button type="submit"
                                                            class="flex items-center gap-2 w-full text-yellow-600"
                                                            onclick="return confirm('Are you sure you want to deactivate this user?')">
                                                        <x-heroicon-o-x-circle class="w-4 h-4" />
                                                        Deactivate Account
                                                    </button>
                                                </form>
                                            </li>
                                            @endif

                                            <div class="divider my-1"></div>
                                            <li>
                                                <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="w-full">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="flex items-center gap-2 w-full text-red-600"
                                                            onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone and will delete all associated data.')">
                                                        <x-heroicon-o-trash class="w-4 h-4" />
                                                        Delete User
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-4 text-base-content/50">
                                        <x-heroicon-o-users class="w-16 h-16" />
                                        <div>
                                            <h3 class="text-lg font-medium">No users found</h3>
                                            <p class="text-sm">Try adjusting your search or filter criteria.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                <div class="card-actions justify-center p-4 border-t">
                    {{ $users->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Results Summary -->
        @if($users->count() > 0)
        <div class="alert">
            <x-heroicon-o-information-circle class="w-5 h-5" />
            <span>Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users</span>
        </div>
        @endif
    </div>

    <!-- Success/Error Toasts -->
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
