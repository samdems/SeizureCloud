<x-layouts.app.sidebar :title="$title ?? null">
    <main class="container mx-auto">
        {{ $slot }}
    </main>
</x-layouts.app.sidebar>
