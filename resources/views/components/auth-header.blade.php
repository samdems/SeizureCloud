@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <h1 class="text-2xl font-bold mb-2">{{ $title }}</h1>
    <p class="text-sm text-base-content/70">{{ $description }}</p>
</div>
