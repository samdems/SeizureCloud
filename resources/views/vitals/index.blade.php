<x-layouts.app :title="__('Vitals')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <x-page-title
            title="Vitals Tracker"
            :actions="[
                [
                    'href' => route('vitals.thresholds'),
                    'class' => 'btn-secondary',
                    'icon' => 'heroicon-o-adjustments-horizontal',
                    'mobile_text' => 'Thresholds',
                    'desktop_text' => 'Manage Thresholds',
                ],
                [
                    'href' => route('vitals.create'),
                    'class' => 'btn-primary',
                    'icon' => 'heroicon-o-plus',
                    'mobile_text' => 'Add',
                    'desktop_text' => 'Add New Vital',
                ],
            ]"
        />

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th>Recorded At</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vitals as $vital)
                        @php
                            $status = $vital->getStatus();
                            $rowClass = ($status === 'too_low' || $status === 'too_high') ? 'bg-error/10' : '';
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>{{ $vital->type }}</td>
                            <td>
                                <span class="font-semibold">{{ $vital->getFormattedValue() }}</span>
                                @if($vital->getStatus() !== 'normal')
                                    @if($vital->getStatus() === 'too_low')
                                        <x-heroicon-o-arrow-down class="h-4 w-4 inline text-error ml-1" />
                                    @else
                                        <x-heroicon-o-arrow-up class="h-4 w-4 inline text-error ml-1" />
                                    @endif
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $vital->getStatusBadgeClass() }}">
                                    {{ $vital->getStatusText() }}
                                </span>
                            </td>
                            <td>{{ $vital->recorded_at->format('M j, Y g:i A') }}</td>
                            <td>{{ Str::limit($vital->notes, 50) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-4 items-center justify-center">
                                    <a href="{{ route('vitals.show', $vital) }}" class="btn btn-sm btn-info" wire:navigate>
                                        <x-heroicon-o-eye class="h-4 w-4" />
                                        View
                                    </a>
                                    <x-kebab-menu
                                        :items="[
                                            [
                                                'label' => 'Edit',
                                                'href' => route('vitals.edit', $vital),
                                                'icon' => 'heroicon-o-pencil',
                                                'wire:navigate' => true,
                                            ],
                                            [
                                                'label' => 'Delete',
                                                'form' => [
                                                    'action' => route('vitals.destroy', $vital),
                                                    'method' => 'DELETE',
                                                    'confirm' => 'Are you sure you want to delete this vital?',
                                                ],
                                                'icon' => 'heroicon-o-trash',
                                            ],
                                        ]"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8">
                                <div class="text-gray-500">
                                    <p class="mb-2">No vitals recorded yet.</p>
                                    <a href="{{ route('vitals.create') }}" class="link link-primary">Add your first vital</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4">
            @forelse($vitals as $vital)
                @php
                    $status = $vital->getStatus();
                    $cardClass = ($status === 'too_low' || $status === 'too_high') ? 'border-l-4 border-l-error bg-error/5' : '';
                @endphp
                <div class="card bg-base-100 shadow-md {{ $cardClass }}">
                    <div class="card-body p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-semibold text-base">{{ $vital->type }}</div>
                                <div class="text-sm text-base-content/70">{{ $vital->recorded_at->format('M j, Y g:i A') }}</div>
                            </div>
                            <span class="badge {{ $vital->getStatusBadgeClass() }}">
                                {{ $vital->getStatusText() }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <div class="text-xs text-base-content/60 uppercase tracking-wider">Value</div>
                            <div class="font-semibold text-lg flex items-center">
                                {{ $vital->getFormattedValue() }}
                                @if($vital->getStatus() !== 'normal')
                                    @if($vital->getStatus() === 'too_low')
                                        <x-heroicon-o-arrow-down class="h-4 w-4 text-error ml-2" />
                                    @else
                                        <x-heroicon-o-arrow-up class="h-4 w-4 text-error ml-2" />
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if($vital->notes)
                            <div class="mb-4">
                                <div class="text-xs text-base-content/60 uppercase tracking-wider">Notes</div>
                                <div class="text-sm">{{ $vital->notes }}</div>
                            </div>
                        @endif

                        <div class="flex flex-wrap gap-2 pt-2 border-t border-base-300">
                            <a href="{{ route('vitals.show', $vital) }}" class="btn btn-sm btn-info flex-1 min-w-0" wire:navigate>
                                <x-heroicon-o-eye class="h-4 w-4" />
                                View
                            </a>
                            <a href="{{ route('vitals.edit', $vital) }}" class="btn btn-sm btn-warning" wire:navigate>
                                <x-heroicon-o-pencil class="h-4 w-4" />
                            </a>
                            <form action="{{ route('vitals.destroy', $vital) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this vital?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-error">
                                    <x-heroicon-o-trash class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p class="text-base-content/70 mb-4">No vitals recorded yet.</p>
                    <a href="{{ route('vitals.create') }}" class="btn btn-primary">
                        <x-heroicon-o-plus class="h-5 w-5" />
                        Add your first vital
                    </a>
                </div>
            @endforelse
        </div>

        @if($vitals->hasPages())
            <div class="flex justify-center">
                {{ $vitals->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
