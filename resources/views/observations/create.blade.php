<x-layouts.app :title="__('Add New Observation')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Add New Observation</h1>
            <a href="{{ route('observations.index') }}" class="btn btn-ghost">
                Back to List
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
                <form action="{{ route('observations.store') }}" method="POST">
                    @csrf

                    <x-form-field
                        name="title"
                        label="Title"
                        type="text"
                        :value="old('title')"
                        placeholder="Brief title for your observation..."
                        required
                        max="255"
                    />

                    <x-form-field
                        name="description"
                        label="Description"
                        type="textarea"
                        :value="old('description')"
                        placeholder="Detailed description of what you observed..."
                        rows="6"
                        required
                        wrapper-class="mb-4"
                    />

                    <x-form-field
                        name="observed_at"
                        label="Observed At"
                        type="datetime-local"
                        :value="old('observed_at', now()->format('Y-m-d\TH:i'))"
                        max="{{ now()->format('Y-m-d\TH:i') }}"
                        required
                    />

                    <div class="card-actions justify-end gap-2">
                        <a href="{{ route('observations.index') }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <x-heroicon-o-plus class="h-5 w-5" />
                            Add Observation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
