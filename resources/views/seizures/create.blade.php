<x-layouts.app :title="__('Add Seizure Record')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Add Seizure Record</h1>
            <div class="flex gap-2">
                <button type="button" class="btn btn-ghost btn-sm" onclick="clearDraft()" title="Clear Draft">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
                <a href="{{ route('seizures.index') }}" class="btn btn-ghost btn-sm">
                    Back to List
                </a>
            </div>
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
                    action="{{ route('seizures.store') }}"
                    :time-fields-editable="true"
                    form-id="seizure_create_form"
                    submit-text="Add Seizure Record"
                    cancel-url="{{ route('seizures.index') }}"
                />
            </div>
        </div>
    </div>

    <script>
        const DRAFT_KEY = 'seizure_create_draft';

        // Local Storage Draft Functionality
        function saveDraft() {
            const formData = {
                start_time: document.getElementById('start_time_input')?.value || '',
                end_time: document.getElementById('end_time_input')?.value || '',
                duration_minutes: document.getElementById('duration_input')?.value || '',
                severity: document.getElementById('severity_input')?.value || '',
                postictal_state_end: document.getElementById('postictal_state_end')?.value || '',
                on_period: document.querySelector('input[name="on_period"]')?.checked || false,
                ambulance_called: document.querySelector('input[name="ambulance_called"]')?.checked || false,
                slept_after: document.querySelector('input[name="slept_after"]')?.checked || false,
                nhs_contact_type: document.querySelector('select[name="nhs_contact_type"]')?.value || '',
                notes: document.getElementById('notes')?.value || '',
                timestamp: new Date().toISOString()
            };

            localStorage.setItem(DRAFT_KEY, JSON.stringify(formData));
        }

        function loadDraft() {
            try {
                const draftData = localStorage.getItem(DRAFT_KEY);
                if (!draftData) return;

                const data = JSON.parse(draftData);
                const hasData = Object.values(data).some(value =>
                    value !== '' && value !== false && value !== null && value !== undefined
                );

                if (!hasData) return;

                // Show draft notification
                const notification = document.createElement('div');
                notification.className = 'alert alert-info mb-4';
                notification.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Draft found from ${new Date(data.timestamp).toLocaleString()}</span>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-success" onclick="restoreDraft()">Restore Draft</button>
                        <button class="btn btn-sm btn-outline" onclick="clearDraft()">Clear Draft</button>
                    </div>
                `;

                const form = document.getElementById('seizure_create_form');
                form.parentNode.insertBefore(notification, form);
            } catch (e) {
                console.error('Error loading draft:', e);
                clearDraft();
            }
        }

        function restoreDraft() {
            try {
                const draftData = localStorage.getItem(DRAFT_KEY);
                if (!draftData) return;

                const data = JSON.parse(draftData);

                // Restore form values
                if (data.start_time) document.getElementById('start_time_input').value = data.start_time;
                if (data.end_time) document.getElementById('end_time_input').value = data.end_time;
                if (data.duration_minutes) document.getElementById('duration_input').value = data.duration_minutes;
                if (data.severity) setSeverity(parseInt(data.severity));
                if (data.postictal_state_end) document.getElementById('postictal_state_end').value = data.postictal_state_end;
                if (data.on_period) document.querySelector('input[name="on_period"]').checked = data.on_period;
                if (data.ambulance_called) document.querySelector('input[name="ambulance_called"]').checked = data.ambulance_called;
                if (data.slept_after) document.querySelector('input[name="slept_after"]').checked = data.slept_after;
                if (data.nhs_contact_type) document.querySelector('select[name="nhs_contact_type"]').value = data.nhs_contact_type;
                if (data.notes) document.getElementById('notes').value = data.notes;

                // Remove notification
                const notification = document.querySelector('.alert-info');
                if (notification) notification.remove();

            } catch (e) {
                console.error('Error restoring draft:', e);
                clearDraft();
            }
        }

        function clearDraft() {
            localStorage.removeItem(DRAFT_KEY);
            const notification = document.querySelector('.alert-info');
            if (notification) notification.remove();
        }

        // Auto-save draft functionality
        function setupAutoSave() {
            const form = document.getElementById('seizure_create_form');
            if (!form) return;

            const inputs = form.querySelectorAll('input, select, textarea');

            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            const debouncedSave = debounce(saveDraft, 1000);

            inputs.forEach(input => {
                input.addEventListener('input', debouncedSave);
                input.addEventListener('change', debouncedSave);
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check if this is a reload/error case with old input
            const hasOldInput = {{ old() ? 'true' : 'false' }};

            if (!hasOldInput) {
                loadDraft();
            }

            setupAutoSave();

            // Clear draft on successful form submission
            const form = document.getElementById('seizure_create_form');
            form.addEventListener('submit', function() {
                setTimeout(clearDraft, 100);
            });
        });
    </script>
</x-layouts.app>
