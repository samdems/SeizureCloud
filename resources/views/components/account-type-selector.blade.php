<!-- Account Type Selection -->
<div class="form-control w-full">
    <div class="space-y-3">
        <!-- Patient Account -->
        <label class="cursor-pointer block">
            <input
                type="radio"
                name="account_type"
                value="patient"
                class="sr-only"
                {{ old('account_type', 'patient') === 'patient' ? 'checked' : '' }}
            />
            <div class="card bg-base-200 border-2 border-transparent hover:border-primary transition-all duration-300 hover:shadow-lg hover:bg-primary/5">
                <div class="card-body p-5">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-primary/10 rounded-lg flex-shrink-0">
                            <x-heroicon-o-user class="w-6 h-6 text-primary" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-primary">Patient Account</h3>
                            <p class="text-sm text-base-content/70 mt-2 leading-relaxed">
                                Track your own seizures, medications, and vitals. Full access to all health tracking features.
                            </p>
                            <div class="text-xs text-base-content/60 mt-3 space-y-1">
                                <div class="flex items-center gap-2"><span class="text-success">✓</span> Seizure tracking</div>
                                <div class="flex items-center gap-2"><span class="text-success">✓</span> Medication management</div>
                                <div class="flex items-center gap-2"><span class="text-success">✓</span> Vitals monitoring</div>
                                <div class="flex items-center gap-2"><span class="text-success">✓</span> Grant trusted access to caregivers</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </label>

        <!-- Caregiver Account -->
        <label class="cursor-pointer block">
            <input
                type="radio"
                name="account_type"
                value="carer"
                class="sr-only"
                {{ old('account_type') === 'carer' ? 'checked' : '' }}
            />
            <div class="card bg-base-200 border-2 border-transparent hover:border-primary transition-all duration-300 hover:shadow-lg hover:bg-accent/5">
                <div class="card-body p-5">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-accent/10 rounded-lg flex-shrink-0">
                            <x-heroicon-o-heart class="w-6 h-6 text-accent" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-accent">Caregiver Account</h3>
                            <p class="text-sm text-base-content/70 mt-2 leading-relaxed">
                                For family members or caregivers who need access to patient records through trusted relationships.
                            </p>
                            <div class="text-xs text-base-content/60 mt-3 space-y-1">
                                <div class="flex items-center gap-2"><span class="text-success">✓</span> View trusted patients' data</div>
                                <div class="flex items-center gap-2"><span class="text-success">✓</span> Assist with medication tracking</div>
                                <div class="flex items-center gap-2"><span class="text-success">✓</span> Emergency access features</div>
                                <div class="flex items-center gap-2"><span class="text-warning">✗</span> Cannot track own health data</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </label>

        <!-- Medical Professional Account -->
        <label class="cursor-pointer block">
            <input
                type="radio"
                name="account_type"
                value="medical"
                class="sr-only"
                {{ old('account_type') === 'medical' ? 'checked' : '' }}
            />
            <div class="card bg-base-200 border-2 border-transparent hover:border-primary transition-all duration-300 hover:shadow-lg hover:bg-success/5">
                <div class="card-body p-5">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-success/10 rounded-lg flex-shrink-0">
                            <x-heroicon-o-academic-cap class="w-6 h-6 text-success" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-success">Medical Professional</h3>
                            <p class="text-sm text-base-content/70 mt-2 leading-relaxed">
                                For healthcare professionals who need access to patient records for medical care.
                            </p>
                            <div class="text-xs text-base-content/60 mt-3 space-y-1">
                                <div class="flex items-center gap-2"><span class="text-success">✓</span> Professional patient access</div>
                                <div class="flex items-center gap-2"><span class="text-success">✓</span> Clinical data review</div>
                                <div class="flex items-center gap-2"><span class="text-success">✓</span> Healthcare team coordination</div>
                                <div class="flex items-center gap-2"><span class="text-warning">✗</span> Cannot track own health data</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </label>
    </div>
    @error('account_type')
        <label class="label">
            <span class="label-text-alt text-error">{{ $message }}</span>
        </label>
    @enderror
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Account type selection interaction
    const accountTypeCards = document.querySelectorAll('input[name="account_type"]');
    const cards = document.querySelectorAll('input[name="account_type"] + div.card');

    function updateCardStyles() {
        accountTypeCards.forEach((radio, index) => {
            const card = cards[index];
            if (radio.checked) {
                card.classList.add('border-primary', 'bg-primary/10', 'shadow-xl', 'ring-2', 'ring-primary/20');
                card.classList.remove('border-transparent', 'bg-base-200');
                // Add selected state styling
                card.style.opacity = '0.95';
            } else {
                card.classList.remove('border-primary', 'bg-primary/10', 'shadow-xl', 'ring-2', 'ring-primary/20');
                card.classList.add('border-transparent', 'bg-base-200');
                card.style.opacity = '1';
            }
        });
    }

    // Initial state
    updateCardStyles();

    // Update on change
    accountTypeCards.forEach(radio => {
        radio.addEventListener('change', updateCardStyles);
    });

    // Make entire card clickable
    cards.forEach((card, index) => {
        card.addEventListener('click', function(e) {
            // Prevent double triggering since label already handles the click
            if (e.target.type !== 'radio') {
                accountTypeCards[index].checked = true;
                updateCardStyles();
            }
        });
    });
});
</script>
