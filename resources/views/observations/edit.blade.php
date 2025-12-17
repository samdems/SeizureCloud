<x-layouts.app :title="__('Edit Observation')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Edit Observation</h1>
            <a href="{{ route('observations.show', $observation) }}" class="btn btn-ghost">
                Back to Observation
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-error mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form action="{{ route('observations.update', $observation) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-form-field
                        name="title"
                        label="Title"
                        type="text"
                        :value="old('title', $observation->title)"
                        placeholder="Brief title for your observation..."
                        required
                        max="255"
                    />

                    <x-form-field
                        name="description"
                        label="Description"
                        type="textarea"
                        :value="old('description', $observation->description)"
                        placeholder="Detailed description of what you observed..."
                        rows="6"
                        required
                        wrapper-class="mb-4"
                    />

                    <x-form-field
                        name="observed_at"
                        label="Observed At"
                        type="datetime-local"
                        :value="old('observed_at', $observation->observed_at->format('Y-m-d\TH:i'))"
                        max="{{ now()->format('Y-m-d\TH:i') }}"
                        required
                    />

                    <div class="card-actions justify-end gap-2">
                        <a href="{{ route('observations.show', $observation) }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <x-heroicon-o-check class="h-5 w-5" />
                            Update Observation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
