<div>
    {{-- "How KaaziLink Works" Hero — banner.jpg background with dark overlay for readability --}}
    <section class="relative w-full overflow-hidden" style="min-height: 340px;">
        {{-- Background: banner.jpg, anchored to the top so the worker photography is visible --}}
        <div class="absolute inset-0">
            <img
                src="/images/banner.jpg"
                alt=""
                aria-hidden="true"
                class="w-full h-full object-cover object-top"
            >
            {{-- Layered overlay: strong dark wash keeps ALL text readable regardless of image content --}}
            <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(0,0,0,0.70) 0%, rgba(0,0,0,0.65) 60%, rgba(0,0,0,0.75) 100%);"></div>
            {{-- Subtle green brand accent at bottom edge --}}
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-[#006B3F]"></div>
        </div>

        {{-- Content —  centred layout matches the wirekit "centered" layout style --}}
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24 text-center flex flex-col items-center justify-center" style="min-height: 340px;">
            <h1 class="text-4xl sm:text-5xl font-heading font-bold text-white leading-tight mb-5 tracking-tight">
                How <span class="text-[#4ADE80]">KaaziLink</span> Works
            </h1>
            <p class="text-lg sm:text-xl text-white/85 max-w-2xl leading-relaxed">
                Whether you need a job done or you're offering your skills, we make the connection simple, safe, and grassroots.
            </p>
        </div>
    </section>

    <x-wirekit::section>
        <x-wirekit::container>
            <div class="mb-16">
                <h2 class="text-3xl font-heading font-semibold text-center mb-10">For Households & Businesses</h2>
                <x-wirekit::grid cols="1 md:3" class="gap-8">
                    <x-wirekit::card>
                        <x-wirekit::card.header>
                            <div class="w-12 h-12 rounded-full bg-wk-accent text-white flex items-center justify-center font-bold text-xl mb-4">1</div>
                            <h3 class="font-heading font-semibold text-xl">Search</h3>
                        </x-wirekit::card.header>
                        <x-wirekit::card.body>
                            <p class="text-wk-text-subtle">Browse through our 51 categories to find the right fundi, househelp, or professional near you.</p>
                        </x-wirekit::card.body>
                    </x-wirekit::card>
                    
                    <x-wirekit::card>
                        <x-wirekit::card.header>
                            <div class="w-12 h-12 rounded-full bg-wk-accent text-white flex items-center justify-center font-bold text-xl mb-4">2</div>
                            <h3 class="font-heading font-semibold text-xl">Compare</h3>
                        </x-wirekit::card.header>
                        <x-wirekit::card.body>
                            <p class="text-wk-text-subtle">Check profiles, read reviews from real Kenyans, and compare pricing to make an informed choice.</p>
                        </x-wirekit::card.body>
                    </x-wirekit::card>

                    <x-wirekit::card>
                        <x-wirekit::card.header>
                            <div class="w-12 h-12 rounded-full bg-wk-accent text-white flex items-center justify-center font-bold text-xl mb-4">3</div>
                            <h3 class="font-heading font-semibold text-xl">Book & Pay</h3>
                        </x-wirekit::card.header>
                        <x-wirekit::card.body>
                            <p class="text-wk-text-subtle">Book their services securely. Payment is held in escrow until the job is completed to your satisfaction.</p>
                        </x-wirekit::card.body>
                    </x-wirekit::card>
                </x-wirekit::grid>
            </div>

            <x-wirekit::divider />

            <div class="mt-16">
                <h2 class="text-3xl font-heading font-semibold text-center mb-10">For Service Providers (Fundis)</h2>
                <x-wirekit::grid cols="1 md:3" class="gap-8">
                    <x-wirekit::card>
                        <x-wirekit::card.header>
                            <div class="w-12 h-12 rounded-full bg-wk-warning text-white flex items-center justify-center font-bold text-xl mb-4">1</div>
                            <h3 class="font-heading font-semibold text-xl">Create Profile</h3>
                        </x-wirekit::card.header>
                        <x-wirekit::card.body>
                            <p class="text-wk-text-subtle">Sign up and list your skills, experience, location, and pricing. Get verified to build trust.</p>
                        </x-wirekit::card.body>
                    </x-wirekit::card>
                    
                    <x-wirekit::card>
                        <x-wirekit::card.header>
                            <div class="w-12 h-12 rounded-full bg-wk-warning text-white flex items-center justify-center font-bold text-xl mb-4">2</div>
                            <h3 class="font-heading font-semibold text-xl">Get Bookings</h3>
                        </x-wirekit::card.header>
                        <x-wirekit::card.body>
                            <p class="text-wk-text-subtle">Receive booking requests directly from clients near you. Accept the ones that fit your schedule.</p>
                        </x-wirekit::card.body>
                    </x-wirekit::card>

                    <x-wirekit::card>
                        <x-wirekit::card.header>
                            <div class="w-12 h-12 rounded-full bg-wk-warning text-white flex items-center justify-center font-bold text-xl mb-4">3</div>
                            <h3 class="font-heading font-semibold text-xl">Get Paid</h3>
                        </x-wirekit::card.header>
                        <x-wirekit::card.body>
                            <p class="text-wk-text-subtle">Complete the work, get good reviews, and receive your payment directly to your M-Pesa account.</p>
                        </x-wirekit::card.body>
                    </x-wirekit::card>
                </x-wirekit::grid>
            </div>
            
            <div class="mt-16 text-center">
                <x-wirekit::button size="lg" href="/register" tag="a">Join Kaazilink Today</x-wirekit::button>
            </div>
        </x-wirekit::container>
    </x-wirekit::section>
</div>
