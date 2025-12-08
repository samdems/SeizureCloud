<x-layouts.app :title="__('Add New Vital')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Add New Vital</h1>
            <a href="{{ route('vitals.index') }}" class="btn btn-ghost">
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
                <form action="{{ route('vitals.store') }}" method="POST">
                    @csrf

                    <x-vitals.form-fields
                        :vital="null"
                        submit-button-text="Add Vital"
                        :cancel-url="route('vitals.index')"
                    />
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
