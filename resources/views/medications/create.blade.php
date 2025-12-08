<x-layouts.app :title="__('Add Medication')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Add Medication</h1>
            <a href="{{ route('medications.index') }}" class="btn btn-ghost">
                Back to List
            </a>
        </div>

        <form action="{{ route('medications.store') }}" method="POST" class="card bg-base-100 shadow-xl">
            <div class="card-body space-y-6">
                @csrf

                <x-medications.form-fields
                    :medication="null"
                    submit-button-text="Add Medication"
                    :cancel-url="route('medications.index')"
                />
            </div>
        </form>
    </div>
</x-layouts.app>
