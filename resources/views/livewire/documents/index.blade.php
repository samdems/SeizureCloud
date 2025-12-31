<div class="space-y-6">
    <!-- Header with Upload Button -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold">Documents</h2>
            <p class="text-base-content/60 mt-1">Manage your medical documents and files</p>
        </div>
        <button wire:click="toggleUploadModal" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Upload Document
        </button>
    </div>

    <!-- Search and Filters -->
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Search -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Search</span>
                    </label>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by title, description, or filename..."
                        class="input input-bordered w-full"
                    />
                </div>

                <!-- Category Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Category</span>
                    </label>
                    <select wire:model.live="categoryFilter" class="select select-bordered w-full">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($search || $categoryFilter)
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-sm text-base-content/60">Active filters:</span>
                    @if($search)
                        <div class="badge badge-outline gap-1">
                            Search: {{ $search }}
                            <button wire:click="$set('search', '')" class="btn btn-ghost btn-xs">✕</button>
                        </div>
                    @endif
                    @if($categoryFilter)
                        <div class="badge badge-outline gap-1">
                            Category: {{ $categories[$categoryFilter] }}
                            <button wire:click="$set('categoryFilter', '')" class="btn btn-ghost btn-xs">✕</button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Documents List -->
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            @if($documents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>
                                    <button wire:click="sortByColumn('title')" class="flex items-center gap-1 font-bold">
                                        Title
                                        @if($sortBy === 'title')
                                            <span class="text-xs">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </button>
                                </th>
                                <th>
                                    <button wire:click="sortByColumn('category')" class="flex items-center gap-1 font-bold">
                                        Category
                                        @if($sortBy === 'category')
                                            <span class="text-xs">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </button>
                                </th>
                                <th>File Info</th>
                                <th>
                                    <button wire:click="sortByColumn('document_date')" class="flex items-center gap-1 font-bold">
                                        Doc Date
                                        @if($sortBy === 'document_date')
                                            <span class="text-xs">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </button>
                                </th>
                                <th>
                                    <button wire:click="sortByColumn('created_at')" class="flex items-center gap-1 font-bold">
                                        Uploaded
                                        @if($sortBy === 'created_at')
                                            <span class="text-xs">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </button>
                                </th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                <tr>
                                    <td>
                                        <div class="flex items-start gap-2">
                                            <!-- File Type Icon -->
                                            @if($document->isPdf())
                                                <svg class="w-5 h-5 text-error flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm3 2h6v4H7V5zm8 8v2h1v-2h-1zm-2-3v1h1v-1h-1zm-4 0v1h1v-1h-1zm-2 0v1h1v-1H7z"/>
                                                </svg>
                                            @elseif($document->isImage())
                                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-info flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                            <div class="min-w-0">
                                                <div class="font-semibold truncate">{{ $document->title }}</div>
                                                @if($document->description)
                                                    <div class="text-xs text-base-content/60 line-clamp-2 mt-1">
                                                        {{ $document->description }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-outline badge-sm">
                                            {{ $document->category_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-sm">
                                            <div class="font-mono text-xs truncate max-w-xs">{{ $document->file_name }}</div>
                                            <div class="text-base-content/60 text-xs">{{ $document->formatted_file_size }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($document->document_date)
                                            <span class="text-sm">{{ $document->document_date->format('M j, Y') }}</span>
                                        @else
                                            <span class="text-base-content/40 text-sm">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-sm">
                                            <div>{{ $document->created_at->format('M j, Y') }}</div>
                                            <div class="text-base-content/60 text-xs">{{ $document->created_at->format('g:i A') }}</div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-1">
                                            <a
                                                href="{{ route('documents.download', $document->id) }}"
                                                class="btn btn-ghost btn-sm"
                                                title="Download"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                            <button
                                                wire:click="deleteDocument({{ $document->id }})"
                                                wire:confirm="Are you sure you want to delete this document? This action cannot be undone."
                                                class="btn btn-ghost btn-sm text-error"
                                                title="Delete"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $documents->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="w-24 h-24 mx-auto text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-xl font-semibold mt-4 mb-2">No documents found</h3>
                    <p class="text-base-content/60 mb-4">
                        @if($search || $categoryFilter)
                            Try adjusting your search or filters
                        @else
                            Get started by uploading your first document
                        @endif
                    </p>
                    @if(!$search && !$categoryFilter)
                        <button wire:click="toggleUploadModal" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Upload Your First Document
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Upload Modal -->
    @if($showUploadModal)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl">
                <h3 class="font-bold text-2xl mb-4">Upload Document</h3>
                <livewire:documents.upload :key="'upload-'.now()" />
                <div class="modal-action">
                    <button wire:click="toggleUploadModal" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <div class="modal-backdrop" wire:click="toggleUploadModal"></div>
        </div>
    @endif
</div>
