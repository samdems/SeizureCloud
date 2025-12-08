<x-layouts.app :title="__('Edit Vital')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Edit Vital</h1>
            <a href="{{ route('vitals.index') }}" class="btn btn-ghost">
                Back to List
            </a>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form action="{{ route('vitals.update', $vital) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-vitals.form-fields
                        :vital="$vital"
                        submit-button-text="Update Vital"
                        :cancel-url="route('vitals.show', $vital)"
                    />
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
