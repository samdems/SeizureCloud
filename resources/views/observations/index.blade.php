<x-layouts.app :title="__('Observations')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <x-page-title
            title="Observations Log"
            :actions="[
                [
                    'href' => route('observations.create'),
                    'class' => 'btn-primary',
                    'icon' => 'heroicon-o-plus',
                    'mobile_text' => 'Add',
                    'desktop_text' => 'Add New Observation',
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
                        <th>Title</th>
                        <th>Description</th>
                        <th>Observed At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($observations as $observation)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ $observation->title }}</div>
                            </td>
                            <td>
                                <div class="max-w-md">
                                    {{ Str::limit($observation->description, 100) }}
                                </div>
                            </td>
                            <td>{{ $observation->observed_at->format('M j, Y g:i A') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-4 items-center justify-center">
                                    <a href="{{ route('observations.show', $observation) }}" class="btn btn-sm btn-info" wire:navigate>
                                        <x-heroicon-o-eye class="h-4 w-4" />
                                        View
                                    </a>
                                    <x-kebab-menu
                                        :items="[
                                            [
                                                'label' => 'Edit',
                                                'href' => route('observations.edit', $observation),
                                                'icon' => 'heroicon-o-pencil',
                                                'wire:navigate' => true,
                                            ],
                                            [
                                                'label' => 'Delete',
                                                'form' => [
                                                    'action' => route('observations.destroy', $observation),
                                                    'method' => 'DELETE',
                                                    'confirm' => 'Are you sure you want to delete this observation?',
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
                            <td colspan="4" class="text-center py-8">
                                <div class="text-gray-500">
                                    <p class="mb-2">No observations recorded yet.</p>
                                    <a href="{{ route('observations.create') }}" class="link link-primary">Add your first observation</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4">
            @forelse($observations as $observation)
                <div class="card bg-base-100 shadow-md">
                    <div class="card-body p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <div class="font-semibold text-base mb-1">{{ $observation->title }}</div>
                                <div class="text-sm text-base-content/70">{{ $observation->observed_at->format('M j, Y g:i A') }}</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="text-xs text-base-content/60 uppercase tracking-wider">Description</div>
                            <div class="text-sm mt-1">{{ Str::limit($observation->description, 150) }}</div>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-2 border-t border-base-300">
                            <a href="{{ route('observations.show', $observation) }}" class="btn btn-sm btn-info flex-1 min-w-0" wire:navigate>
                                <x-heroicon-o-eye class="h-4 w-4" />
                                View
                            </a>
                            <a href="{{ route('observations.edit', $observation) }}" class="btn btn-sm btn-warning" wire:navigate>
                                <x-heroicon-o-pencil class="h-4 w-4" />
                            </a>
                            <form action="{{ route('observations.destroy', $observation) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this observation?')">
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
                    <p class="text-base-content/70 mb-4">No observations recorded yet.</p>
                    <a href="{{ route('observations.create') }}" class="btn btn-primary">
                        <x-heroicon-o-plus class="h-5 w-5" />
                        Add your first observation
                    </a>
                </div>
            @endforelse
        </div>

        @if($observations->hasPages())
            <div class="flex justify-center">
                {{ $observations->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
