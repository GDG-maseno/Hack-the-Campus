<div class="py-12 bg-[var(--color-wk-bg)] min-h-[70vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Banner --}}
        <div class="mb-10 text-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#006B3F]/10 text-[#006B3F] font-inter mb-3">
                Kenya Data Protection Act (KDPA) 2019 Compliant
            </span>
            <h1 class="text-3xl font-extrabold text-[#1F2937] font-heading tracking-tight sm:text-4xl">
                Privacy Policy
            </h1>
            <p class="mt-2 text-sm text-gray-500 font-inter">
                Last Updated: {{ now()->format('F Y') }} &bull; Kaazilink Kenya
            </p>
        </div>

        {{-- Content Card --}}
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-6 sm:p-10 space-y-8 font-inter text-gray-700 leading-relaxed">
            <div class="bg-gray-50 border-l-4 border-[#006B3F] p-4 rounded-r-lg">
                <p class="text-sm text-gray-700 font-medium">
                    Your trust and data privacy are foundational to Kaazilink. This policy outlines how we collect, safeguard, and process your personal and verification data.
                </p>
            </div>

            <section class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#006B3F]/10 text-[#006B3F] text-xs font-bold">1</span>
                    <h2 class="text-lg font-bold text-[#1F2937] font-heading">Information We Collect</h2>
                </div>
                <p class="text-sm text-gray-600 pl-9">
                    We collect personal details you directly provide when registering and using Kaazilink, including:
                </p>
                <div class="pl-9 space-y-2 text-sm text-gray-600">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <strong class="text-gray-900">Account Information:</strong> Name, email address, password, profile picture / avatar, and chosen platform role.
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <strong class="text-gray-900">Google Authentication:</strong> When you choose to sign up or sign in with Google, we receive your verified Google name, email address, avatar, and unique Google ID via secure OAuth.
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <strong class="text-gray-900">Provider Verification Data:</strong> National ID details, photos of your identification documents (front and back), KRA PIN certificate, phone number, operating address, and emergency contacts.
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <strong class="text-gray-900">Booking & Transaction Records:</strong> Service requests, agreed pricing, service locations, and payment transaction identifiers.
                    </div>
                </div>
            </section>

            <section class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#006B3F]/10 text-[#006B3F] text-xs font-bold">2</span>
                    <h2 class="text-lg font-bold text-[#1F2937] font-heading">How We Use Your Information</h2>
                </div>
                <p class="text-sm text-gray-600 pl-9">
                    We use your data strictly to facilitate trusted grassroots service connections:
                </p>
                <ul class="pl-9 list-disc list-inside space-y-1.5 text-sm text-gray-600">
                    <li>To authenticate accounts, manage user profiles, and verify email addresses.</li>
                    <li>To facilitate booking requests between clients and local service providers.</li>
                    <li>To verify provider authenticity through our manual KYC review workflow.</li>
                    <li>To dispatch essential platform notifications (booking confirmations, status updates, reviews).</li>
                    <li>To process transactions securely via trusted mobile payment partners.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#006B3F]/10 text-[#006B3F] text-xs font-bold">3</span>
                    <h2 class="text-lg font-bold text-[#1F2937] font-heading">Protection & Storage of Verification Documents</h2>
                </div>
                <p class="text-sm text-gray-600 pl-9">
                    Provider identification documents (National ID, KRA PIN) are stored securely in encrypted cloud object storage (Cloudflare R2) and are never exposed publicly. Only authorized Kaazilink administrators inspect these files during the manual verification process.
                </p>
            </section>

            <section class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#006B3F]/10 text-[#006B3F] text-xs font-bold">4</span>
                    <h2 class="text-lg font-bold text-[#1F2937] font-heading">Data Sharing & Third Parties</h2>
                </div>
                <p class="text-sm text-gray-600 pl-9">
                    We do not sell, rent, or trade your personal data. We only share data with essential third-party service providers required for application functionality:
                </p>
                <div class="pl-9 space-y-2 text-sm text-gray-600">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <strong class="text-gray-900">Google OAuth:</strong> To securely authenticate your identity without storing raw external credentials.
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <strong class="text-gray-900">Payment Gateways (PayHero / M-PESA):</strong> To process STK Push mobile money payments.
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <strong class="text-gray-900">Cloud Hosting & Infrastructure (Render, NeonDB):</strong> To reliably run our platform application.
                    </div>
                </div>
            </section>

            <section class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#006B3F]/10 text-[#006B3F] text-xs font-bold">5</span>
                    <h2 class="text-lg font-bold text-[#1F2937] font-heading">Your Data Rights</h2>
                </div>
                <p class="text-sm text-gray-600 pl-9">
                    In compliance with the Kenya Data Protection Act, 2019, you have the right to access, rectify, or request the deletion of your personal data held on Kaazilink. You may edit your profile information at any time through your dashboard or contact our support team.
                </p>
            </section>

            <section class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold text-[#1F2937] font-heading">Contact Our Privacy Team</h3>
                    <p class="text-xs text-gray-500 font-inter">Direct all data protection inquiries to our compliance desk.</p>
                </div>
                <a href="mailto:hello@kaazilink.co.ke" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Email Privacy Team
                </a>
            </section>
        </div>
    </div>
</div>
