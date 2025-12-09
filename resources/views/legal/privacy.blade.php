<x-layouts.auth title="Privacy Policy">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Privacy Policy" description="Your privacy and health data protection" />

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
                            <div class="text-sm">This privacy policy applies to Epilepsy Diary, a personal health tracking application. Your health data privacy is our top priority.</div>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold mt-8 mb-4">1. Introduction</h2>
                    <p>
                        Epilepsy Diary ("we," "our," or "us") is committed to protecting your privacy and the confidentiality of your personal health information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our seizure tracking application and related services.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">2. Information We Collect</h2>

                    <h3 class="text-xl font-semibold mt-6 mb-3">2.1 Health Information</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Seizure records including date, time, duration, severity, and symptoms</li>
                        <li>Medication information including names, dosages, schedules, and adherence logs</li>
                        <li>Vital signs data such as blood pressure, heart rate, and temperature</li>
                        <li>Emergency contacts and trusted contact information</li>
                        <li>Medical notes and observations</li>
                    </ul>

                    <h3 class="text-xl font-semibold mt-6 mb-3">2.2 Personal Information</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Name, email address, and contact information</li>
                        <li>Profile information and avatars</li>
                        <li>Account preferences and settings</li>
                        <li>Authentication and security information</li>
                    </ul>

                    <h3 class="text-xl font-semibold mt-6 mb-3">2.3 Technical Information</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Device information and identifiers</li>
                        <li>IP addresses and browser information</li>
                        <li>Usage patterns and application interactions</li>
                        <li>Log files and error reports</li>
                    </ul>

                    <h2 class="text-2xl font-bold mt-8 mb-4">3. How We Use Your Information</h2>

                    <h3 class="text-xl font-semibold mt-6 mb-3">3.1 Primary Uses</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Providing seizure tracking and health monitoring services</li>
                        <li>Medication management and scheduling</li>
                        <li>Emergency detection and trusted contact notifications</li>
                        <li>Generating health reports and insights</li>
                        <li>Account management and user authentication</li>
                    </ul>

                    <h3 class="text-xl font-semibold mt-6 mb-3">3.2 Secondary Uses</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Improving application functionality and user experience</li>
                        <li>Technical support and customer service</li>
                        <li>Security monitoring and fraud prevention</li>
                        <li>Compliance with legal obligations</li>
                    </ul>

                    <h2 class="text-2xl font-bold mt-8 mb-4">4. Information Sharing and Disclosure</h2>

                    <div class="alert alert-warning mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <span><strong>We do not sell, rent, or trade your personal health information to third parties.</strong></span>
                    </div>

                    <h3 class="text-xl font-semibold mt-6 mb-3">4.1 Permitted Disclosures</h3>
                    <p>We may share your information in the following limited circumstances:</p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li><strong>Trusted Contacts:</strong> With individuals you have explicitly designated as trusted contacts for emergency situations</li>
                        <li><strong>Healthcare Providers:</strong> When you export and share your health data with medical professionals</li>
                        <li><strong>Legal Compliance:</strong> When required by law, court order, or government regulation</li>
                        <li><strong>Emergency Situations:</strong> To protect your vital interests or those of others in emergency circumstances</li>
                        <li><strong>Service Providers:</strong> With trusted third-party service providers who assist in application operations (under strict confidentiality agreements)</li>
                    </ul>

                    <h2 class="text-2xl font-bold mt-8 mb-4">5. Data Security</h2>

                    <h3 class="text-xl font-semibold mt-6 mb-3">5.1 Security Measures</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Encryption of data in transit and at rest</li>
                        <li>Multi-factor authentication options</li>
                        <li>Regular security audits and assessments</li>
                        <li>Access controls and user permission systems</li>
                        <li>Secure server infrastructure and monitoring</li>
                    </ul>

                    <h3 class="text-xl font-semibold mt-6 mb-3">5.2 Data Breach Response</h3>
                    <p>
                        In the event of a data breach affecting your personal information, we will notify you and relevant authorities within 72 hours of discovery, as required by applicable law.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">6. Your Rights and Choices</h2>

                    <h3 class="text-xl font-semibold mt-6 mb-3">6.1 Access and Control</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li><strong>Access:</strong> View and download your personal health data</li>
                        <li><strong>Correction:</strong> Update or correct inaccurate information</li>
                        <li><strong>Deletion:</strong> Request deletion of your account and associated data</li>
                        <li><strong>Portability:</strong> Export your data in standard formats</li>
                        <li><strong>Restriction:</strong> Limit how we process your information</li>
                    </ul>

                    <h3 class="text-xl font-semibold mt-6 mb-3">6.2 Communication Preferences</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Control emergency notification settings</li>
                        <li>Manage trusted contact permissions</li>
                        <li>Opt out of non-essential communications</li>
                        <li>Adjust data sharing preferences</li>
                    </ul>

                    <h2 class="text-2xl font-bold mt-8 mb-4">7. Data Retention</h2>
                    <p>
                        We retain your health information for as long as your account remains active or as needed to provide services. Upon account deletion, we will securely delete your personal data within 30 days, except where retention is required by law or for legitimate business purposes (such as resolving disputes).
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">8. International Data Transfers</h2>
                    <p>
                        Your information may be transferred to and processed in countries other than your country of residence. We ensure appropriate safeguards are in place to protect your information in accordance with applicable data protection laws.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">9. Children's Privacy</h2>
                    <p>
                        Epilepsy Diary is not intended for use by children under 13 years of age. We do not knowingly collect personal information from children under 13. If you believe a child has provided us with personal information, please contact us immediately.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">10. Changes to This Privacy Policy</h2>
                    <p>
                        We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the new Privacy Policy on this page and updating the "Last Updated" date. Your continued use of Epilepsy Diary after changes become effective constitutes acceptance of the revised Privacy Policy.
                    </p>

                    <h2 class="text-2xl font-bold mt-8 mb-4">11. Contact Information</h2>
                    <div class="bg-base-200 p-6 rounded-lg">
                        <p class="mb-4">
                            If you have questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us:
                        </p>
                        <ul class="space-y-2">
                            <li><strong>Email:</strong> privacy@epilepsydiary.com</li>
                        </ul>
                        <p class="mt-4 text-sm text-base-content/70">
                            For data protection inquiries in the EU, you may also contact our Data Protection Officer at dpo@epilepsydiary.com
                        </p>
                    </div>

                    <div class="mt-8 p-4 bg-primary/10 border border-primary/20 rounded-lg">
                        <h3 class="font-bold text-primary mb-2">Medical Disclaimer</h3>
                        <p class="text-sm">
                            Epilepsy Diary is designed to support seizure tracking and medication management but is not a substitute for professional medical advice, diagnosis, or treatment. Always consult with qualified healthcare providers regarding your medical condition.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center text-sm text-zinc-600 dark:text-zinc-400 space-x-2">
            <a href="{{ route('legal.terms') }}" class="link link-primary" wire:navigate>Terms of Service</a>
            <span>•</span>
            <a href="{{ route('home') }}" class="link link-primary" wire:navigate>Back to Home</a>
        </div>
    </div>
</x-layouts.auth>
