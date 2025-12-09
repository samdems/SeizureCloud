<x-layouts.auth title="Terms of Service">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Terms of Service" description="Legal agreement for using Epilepsy Diary" />

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="prose prose-lg max-w-none">
                    <p class="text-base-content/70 mb-6">
                        <strong>Effective Date:</strong> {{ date('F j, Y') }}<br>
                        <strong>Last Updated:</strong> {{ date('F j, Y') }}
                    </p>

                    <div class="alert alert-info mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold">Important Notice</h3>
                            <div class="text-sm">Please read these terms carefully before using Epilepsy Diary. By using our service, you agree to be bound by these terms.</div>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold mt-8 mb-4">1. Acceptance of Terms</h2>
                    <p>
                        By accessing or using Epilepsy Diary ("the Service"), you agree to be bound by these Terms of Service ("Terms"). If you do not agree to these Terms, please do not use the Service. These Terms constitute a legally binding agreement between you and Epilepsy Diary.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">2. Description of Service</h2>
                    <p>
                        Epilepsy Diary is a personal health tracking application designed to help users monitor and manage seizure-related health information. The Service includes features for:
                    </p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Recording and tracking seizure episodes</li>
                        <li>Managing medication schedules and adherence</li>
                        <li>Monitoring vital signs and health metrics</li>
                        <li>Emergency detection and trusted contact notifications</li>
                        <li>Generating health reports and insights</li>
                        <li>Sharing data with healthcare providers</li>
                    </ul>

                    <h2 class="text-2xl font-bold mt-8 mb-4">3. Medical Disclaimer</h2>

                    <div class="alert alert-warning mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <span><strong>Critical Medical Disclaimer</strong></span>
                    </div>

                    <p>
                        <strong>Epilepsy Diary is NOT a medical device and is NOT intended to diagnose, treat, cure, or prevent any disease or medical condition.</strong> The Service is designed solely as a tracking and organizational tool to help you manage health information. You understand and agree that:
                    </p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>The Service is not a substitute for professional medical advice, diagnosis, or treatment</li>
                        <li>You should always consult with qualified healthcare professionals regarding your medical condition</li>
                        <li>You should not rely solely on the Service for emergency medical situations</li>
                        <li>The Service may not detect all seizure episodes or medical emergencies</li>
                        <li>Technology failures may occur and should not be solely relied upon in critical situations</li>
                    </ul>

                    <h2 class="text-2xl font-bold mt-8 mb-4">4. User Eligibility and Accounts</h2>

                    <h3 class="text-xl font-semibold mt-6 mb-3">4.1 Age Requirements</h3>
                    <p>
                        You must be at least 13 years old to use the Service. If you are under 18, you must have parental or guardian consent to use the Service.
                    </p>

                    <h3 class="text-xl font-semibold mt-6 mb-3">4.2 Account Responsibilities</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>You are responsible for maintaining the confidentiality of your account credentials</li>
                        <li>You must provide accurate and complete information during registration</li>
                        <li>You must promptly update your account information if it changes</li>
                        <li>You are responsible for all activities that occur under your account</li>
                        <li>You must notify us immediately of any unauthorized use of your account</li>
                    </ul>

                    <h2 class="text-2xl font-bold mt-8 mb-4">5. Acceptable Use</h2>

                    <h3 class="text-xl font-semibold mt-6 mb-3">5.1 Permitted Uses</h3>
                    <p>You may use the Service only for lawful purposes and in accordance with these Terms. You agree to:</p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Use the Service for personal health tracking purposes only</li>
                        <li>Provide accurate health information to the best of your ability</li>
                        <li>Respect the privacy and rights of other users</li>
                        <li>Comply with all applicable laws and regulations</li>
                    </ul>

                    <h3 class="text-xl font-semibold mt-6 mb-3">5.2 Prohibited Uses</h3>
                    <p>You agree not to:</p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Use the Service for commercial purposes without written permission</li>
                        <li>Share your account credentials with unauthorized persons</li>
                        <li>Upload or transmit malicious code, viruses, or harmful content</li>
                        <li>Attempt to gain unauthorized access to the Service or other users' accounts</li>
                        <li>Use the Service in any way that could damage, disable, or impair the Service</li>
                        <li>Violate any applicable laws, regulations, or third-party rights</li>
                        <li>Use automated systems to access the Service without permission</li>
                    </ul>

                    <h2 class="text-2xl font-bold mt-8 mb-4">6. Trusted Access and Data Sharing</h2>

                    <h3 class="text-xl font-semibold mt-6 mb-3">6.1 Trusted Contacts</h3>
                    <p>
                        The Service allows you to designate trusted contacts who may access your health information in emergency situations. By granting trusted access:
                    </p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>You consent to sharing your health data with designated trusted contacts</li>
                        <li>You acknowledge that trusted contacts may view sensitive health information</li>
                        <li>You can revoke trusted access at any time through your account settings</li>
                        <li>You are responsible for managing your trusted contact list</li>
                    </ul>

                    <h3 class="text-xl font-semibold mt-6 mb-3">6.2 Healthcare Provider Sharing</h3>
                    <p>
                        You may choose to export and share your health data with healthcare providers. Such sharing is entirely voluntary and under your control.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">7. Privacy and Data Protection</h2>
                    <p>
                        Your privacy is important to us. Our collection, use, and protection of your personal information is governed by our <a href="{{ route('legal.privacy') }}" class="link link-primary" wire:navigate>Privacy Policy</a>, which is incorporated into these Terms by reference.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">8. Intellectual Property</h2>

                    <h3 class="text-xl font-semibold mt-6 mb-3">8.1 Our Rights</h3>
                    <p>
                        The Service, including all content, features, and functionality, is owned by Epilepsy Diary and is protected by copyright, trademark, and other intellectual property laws.
                    </p>

                    <h3 class="text-xl font-semibold mt-6 mb-3">8.2 Your Rights</h3>
                    <p>
                        You retain ownership of the health data you input into the Service. By using the Service, you grant us a limited license to use your data solely for providing and improving the Service as described in our Privacy Policy.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">9. Service Availability and Modifications</h2>

                    <h3 class="text-xl font-semibold mt-6 mb-3">9.1 Service Availability</h3>
                    <p>
                        We strive to provide reliable service but cannot guarantee 100% uptime. The Service may be temporarily unavailable due to maintenance, updates, or technical issues.
                    </p>

                    <h3 class="text-xl font-semibold mt-6 mb-3">9.2 Service Modifications</h3>
                    <p>
                        We reserve the right to modify, suspend, or discontinue the Service at any time, with or without notice. We will make reasonable efforts to provide advance notice of significant changes.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">10. Limitation of Liability</h2>

                    <div class="alert alert-error mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><strong>Important Legal Notice</strong></span>
                    </div>

                    <p>
                        <strong>TO THE MAXIMUM EXTENT PERMITTED BY LAW:</strong>
                    </p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>THE SERVICE IS PROVIDED "AS IS" WITHOUT WARRANTIES OF ANY KIND</li>
                        <li>WE DISCLAIM ALL WARRANTIES, EXPRESS OR IMPLIED, INCLUDING MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE</li>
                        <li>WE SHALL NOT BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, OR CONSEQUENTIAL DAMAGES</li>
                        <li>OUR TOTAL LIABILITY SHALL NOT EXCEED THE AMOUNT YOU PAID FOR THE SERVICE IN THE PAST 12 MONTHS</li>
                        <li>WE ARE NOT LIABLE FOR DAMAGES RESULTING FROM SERVICE INTERRUPTIONS, DATA LOSS, OR SYSTEM FAILURES</li>
                    </ul>

                    <h2 class="text-2xl font-bold mt-8 mb-4">11. Emergency Situations</h2>
                    <p>
                        <strong>IN CASE OF MEDICAL EMERGENCY, ALWAYS CALL YOUR LOCAL EMERGENCY SERVICES (911, 999, etc.) IMMEDIATELY.</strong> Do not rely solely on the Service's emergency detection features or trusted contact notifications during actual emergencies.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">12. Indemnification</h2>
                    <p>
                        You agree to indemnify and hold harmless Epilepsy Diary, its officers, directors, employees, and agents from any claims, damages, losses, or expenses arising from your use of the Service, violation of these Terms, or infringement of any rights of another person or entity.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">13. Termination</h2>

                    <h3 class="text-xl font-semibold mt-6 mb-3">13.1 Termination by You</h3>
                    <p>
                        You may terminate your account at any time by contacting us or using the account deletion feature in your settings.
                    </p>

                    <h3 class="text-xl font-semibold mt-6 mb-3">13.2 Termination by Us</h3>
                    <p>
                        We may terminate or suspend your account immediately if you violate these Terms or for any other reason at our sole discretion.
                    </p>

                    <h3 class="text-xl font-semibold mt-6 mb-3">13.3 Effect of Termination</h3>
                    <p>
                        Upon termination, your right to use the Service will cease immediately. We will delete your personal data in accordance with our Privacy Policy, except where retention is required by law.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">14. Governing Law and Disputes</h2>
                    <p>
                        These Terms are governed by applicable law. Any disputes arising from these Terms or the Service will be resolved through binding arbitration or in the appropriate courts having jurisdiction.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">15. Changes to Terms</h2>
                    <p>
                        We may update these Terms from time to time. We will notify you of material changes by posting the updated Terms on this page and updating the "Last Updated" date. Your continued use of the Service after changes become effective constitutes acceptance of the revised Terms.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">16. Severability</h2>
                    <p>
                        If any provision of these Terms is found to be unenforceable, the remaining provisions will remain in full force and effect.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">17. Contact Information</h2>
                    <div class="bg-base-200 p-6 rounded-lg">
                        <p class="mb-4">
                            If you have questions about these Terms or need support, please contact us:
                            </p>
                            <ul class="space-y-2">
                                <li><strong>Email:</strong> support@epilepsydiary.com</li>
                                <li><strong>Legal:</strong> legal@epilepsydiary.com</li>
                            </ul>
                    </div>

                    <div class="mt-8 p-4 bg-success/10 border border-success/20 rounded-lg">
                        <h3 class="font-bold text-success mb-2">Thank You</h3>
                        <p class="text-sm">
                            Thank you for using Epilepsy Diary. We are committed to helping you manage your health information safely and effectively. Please use the Service responsibly and always prioritize your health and safety.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center text-sm text-zinc-600 dark:text-zinc-400 space-x-2">
            <a href="{{ route('legal.privacy') }}" class="link link-primary" wire:navigate>Privacy Policy</a>
            <span>•</span>
            <a href="{{ route('home') }}" class="link link-primary" wire:navigate>Back to Home</a>
        </div>
    </div>
</x-layouts.auth>
