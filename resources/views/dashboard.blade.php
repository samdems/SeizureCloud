<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <x-dashboard.header />

        <x-dashboard.trusted-access />

        <x-dashboard.quick-stats />

        <x-dashboard.emergency-settings />

        <x-dashboard.emergency-tracker />

        <x-dashboard.non-patient-info />

        <x-dashboard.recent-activity />
    </div>
</x-layouts.app>
