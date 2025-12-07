<x-layouts.app :title="__('Edit Seizure Record')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Edit Seizure Record</h1>
            <a href="{{ route('seizures.index') }}" class="btn btn-ghost">
                Back to List
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Please fix the following errors:</span>
                <ul class="list-disc list-inside mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <x-seizure.record-form
                    action="{{ route('seizures.update', $seizure) }}"
                    method="PUT"
                    :seizure="$seizure"
                    :time-fields-editable="true"
                    form-id="seizure_edit_form"
                    submit-text="Update Seizure Record"
                    cancel-url="{{ route('seizures.index') }}"
                />
            </div>
        </div>
    </div>
</x-layouts.app>
