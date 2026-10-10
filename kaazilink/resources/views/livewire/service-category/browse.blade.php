<div>
    <x-wirekit::section class="bg-[var(--color-wk-bg-subtle)] border-b border-[var(--color-wk-border-subtle)]">
        <x-wirekit::container>
            <div class="py-12 text-center">
                <h1 class="text-4xl font-heading font-bold text-wk-text mb-4">Service Directory</h1>
                <p class="text-lg text-wk-text-subtle max-w-2xl mx-auto mb-8">Browse our 51 vetted services across 6 categories to find the right professional for your needs.</p>

                {{-- Search bar --}}
                <div
                    x-data="{ query: '' }"
                    x-on:keydown.escape="query = ''"
                    class="max-w-xl mx-auto relative"
                >
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                        <input
                            x-model="query"
                            type="search"
                            id="service-search"
                            placeholder="Search services e.g. Plumber, Nanny, DJ…"
                            class="w-full pl-12 pr-10 py-3 rounded-xl border border-gray-300 bg-white shadow-sm text-base text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#006B3F] focus:border-[#006B3F] transition"
                            autocomplete="off"
                        />
                        <button
                            x-show="query.length > 0"
                            x-on:click="query = ''"
                            type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            aria-label="Clear search"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- No results message --}}
                    <p
                        x-show="query.length > 0"
                        x-data
                        x-init="$watch('query', () => {})"
                        class="mt-3 text-sm text-gray-500 hidden"
                        id="no-results-msg"
                    >
                        No services match "<span id="no-results-query"></span>". Try a different keyword.
                    </p>

                    {{-- Alpine search logic — runs on the grid below via data-search attributes --}}
                    <template x-if="query.length > 0">
                        <span
                            x-init
                            x-effect="
                                const q = query.toLowerCase().trim();
                                let anyVisible = false;
                                document.querySelectorAll('[data-service-item]').forEach(li => {
                                    const name = li.dataset.serviceName.toLowerCase();
                                    const cat  = li.dataset.serviceCategory.toLowerCase();
                                    const show = name.includes(q) || cat.includes(q);
                                    li.style.display = show ? '' : 'none';
                                    if (show) anyVisible = true;
                                });
                                document.querySelectorAll('[data-category-card]').forEach(card => {
                                    const visibleInCard = [...card.querySelectorAll('[data-service-item]')].some(li => li.style.display !== 'none');
                                    card.style.display = visibleInCard ? '' : 'none';
                                });
                                const msg = document.getElementById('no-results-msg');
                                const qSpan = document.getElementById('no-results-query');
                                if (msg && qSpan) {
                                    qSpan.textContent = query;
                                    msg.classList.toggle('hidden', anyVisible);
                                }
                            "
                        ></span>
                    </template>
                    <template x-if="query.length === 0">
                        <span
                            x-init
                            x-effect="
                                document.querySelectorAll('[data-service-item]').forEach(li => li.style.display = '');
                                document.querySelectorAll('[data-category-card]').forEach(card => card.style.display = '');
                                const msg = document.getElementById('no-results-msg');
                                if (msg) msg.classList.add('hidden');
                            "
                        ></span>
                    </template>
                </div>
            </div>
        </x-wirekit::container>
    </x-wirekit::section>

    <x-wirekit::section>
        <x-wirekit::container>
            <x-wirekit::grid cols="1 md:2 lg:3" class="gap-8">
                <!-- Home Care -->
                <x-wirekit::card data-category-card>
                    <x-wirekit::card.header>
                        <h3 class="font-heading font-semibold text-xl text-wk-accent">Home Care</h3>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <ul class="space-y-2 text-sm text-wk-text-subtle">
                            <li data-service-item data-service-name="Househelp / Nanny" data-service-category="Home Care"><x-wirekit::link href="/services/househelp-nanny">Househelp / Nanny</x-wirekit::link></li>
                            <li data-service-item data-service-name="Cleaner" data-service-category="Home Care"><x-wirekit::link href="/services/cleaner">Cleaner</x-wirekit::link></li>
                            <li data-service-item data-service-name="Laundry (Mama Fua)" data-service-category="Home Care"><x-wirekit::link href="/services/laundry-mama-fua">Laundry (Mama Fua)</x-wirekit::link></li>
                            <li data-service-item data-service-name="Chef / Cook" data-service-category="Home Care"><x-wirekit::link href="/services/chef-cook">Chef / Cook</x-wirekit::link></li>
                            <li data-service-item data-service-name="Caregiver (Elderly)" data-service-category="Home Care"><x-wirekit::link href="/services/caregiver-elderly">Caregiver (Elderly)</x-wirekit::link></li>
                        </ul>
                    </x-wirekit::card.body>
                    <x-wirekit::card.footer>
                        <x-wirekit::button href="/services/home-care" size="sm" intent="neutral" surface="outline" class="w-full">Explore Category</x-wirekit::button>
                    </x-wirekit::card.footer>
                </x-wirekit::card>

                <!-- Fundi & Technical Repairs -->
                <x-wirekit::card data-category-card>
                    <x-wirekit::card.header>
                        <h3 class="font-heading font-semibold text-xl text-wk-accent">Fundi & Technical</h3>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <ul class="space-y-2 text-sm text-wk-text-subtle">
                            <li data-service-item data-service-name="Plumber" data-service-category="Fundi Technical"><x-wirekit::link href="/services/plumber">Plumber</x-wirekit::link></li>
                            <li data-service-item data-service-name="Electrician" data-service-category="Fundi Technical"><x-wirekit::link href="/services/electrician">Electrician</x-wirekit::link></li>
                            <li data-service-item data-service-name="Carpenter" data-service-category="Fundi Technical"><x-wirekit::link href="/services/carpenter">Carpenter</x-wirekit::link></li>
                            <li data-service-item data-service-name="Mason / Builder" data-service-category="Fundi Technical"><x-wirekit::link href="/services/mason-builder">Mason / Builder</x-wirekit::link></li>
                            <li data-service-item data-service-name="Painter" data-service-category="Fundi Technical"><x-wirekit::link href="/services/painter">Painter</x-wirekit::link></li>
                            <li data-service-item data-service-name="AC Fridge Repair" data-service-category="Fundi Technical"><x-wirekit::link href="/services/ac-fridge-repair">AC / Fridge Repair</x-wirekit::link></li>
                            <li data-service-item data-service-name="Washing Machine Repair" data-service-category="Fundi Technical"><x-wirekit::link href="/services/washing-machine-repair">Washing Machine Repair</x-wirekit::link></li>
                            <li data-service-item data-service-name="Electronics Repair" data-service-category="Fundi Technical"><x-wirekit::link href="/services/electronics-repair">Electronics Repair</x-wirekit::link></li>
                        </ul>
                    </x-wirekit::card.body>
                    <x-wirekit::card.footer>
                        <x-wirekit::button href="/services/fundi-technical-repairs" size="sm" intent="neutral" surface="outline" class="w-full">Explore Category</x-wirekit::button>
                    </x-wirekit::card.footer>
                </x-wirekit::card>

                <!-- Daily Needs & Emergency Response -->
                <x-wirekit::card data-category-card>
                    <x-wirekit::card.header>
                        <h3 class="font-heading font-semibold text-xl text-wk-accent">Daily Needs & Emergency</h3>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <ul class="space-y-2 text-sm text-wk-text-subtle">
                            <li data-service-item data-service-name="Boda Boda Rider" data-service-category="Daily Needs Emergency"><x-wirekit::link href="/services/boda-boda-rider">Boda Boda Rider</x-wirekit::link></li>
                            <li data-service-item data-service-name="Tuk Tuk Driver" data-service-category="Daily Needs Emergency"><x-wirekit::link href="/services/tuk-tuk-driver">Tuk Tuk Driver</x-wirekit::link></li>
                            <li data-service-item data-service-name="Water Vendor" data-service-category="Daily Needs Emergency"><x-wirekit::link href="/services/water-vendor">Water Vendor</x-wirekit::link></li>
                            <li data-service-item data-service-name="Gas Delivery" data-service-category="Daily Needs Emergency"><x-wirekit::link href="/services/gas-delivery">Gas Delivery</x-wirekit::link></li>
                            <li data-service-item data-service-name="Garbage Collection" data-service-category="Daily Needs Emergency"><x-wirekit::link href="/services/garbage-collection">Garbage Collection</x-wirekit::link></li>
                        </ul>
                    </x-wirekit::card.body>
                    <x-wirekit::card.footer>
                        <x-wirekit::button href="/services/daily-needs-emergency-response" size="sm" intent="neutral" surface="outline" class="w-full">Explore Category</x-wirekit::button>
                    </x-wirekit::card.footer>
                </x-wirekit::card>

                <!-- Beauty, Events & Social Support -->
                <x-wirekit::card data-category-card>
                    <x-wirekit::card.header>
                        <h3 class="font-heading font-semibold text-xl text-wk-accent">Beauty & Events</h3>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <ul class="space-y-2 text-sm text-wk-text-subtle">
                            <li data-service-item data-service-name="Hairdresser / Salonist" data-service-category="Beauty Events"><x-wirekit::link href="/services/hairdresser-salonist">Hairdresser / Salonist</x-wirekit::link></li>
                            <li data-service-item data-service-name="Barber" data-service-category="Beauty Events"><x-wirekit::link href="/services/barber">Barber</x-wirekit::link></li>
                            <li data-service-item data-service-name="Makeup Artist" data-service-category="Beauty Events"><x-wirekit::link href="/services/makeup-artist">Makeup Artist</x-wirekit::link></li>
                            <li data-service-item data-service-name="MC / Hype Man" data-service-category="Beauty Events"><x-wirekit::link href="/services/mc-hype-man">MC / Hype Man</x-wirekit::link></li>
                            <li data-service-item data-service-name="DJ" data-service-category="Beauty Events"><x-wirekit::link href="/services/dj">DJ</x-wirekit::link></li>
                        </ul>
                    </x-wirekit::card.body>
                    <x-wirekit::card.footer>
                        <x-wirekit::button href="/services/beauty-events-social-support" size="sm" intent="neutral" surface="outline" class="w-full">Explore Category</x-wirekit::button>
                    </x-wirekit::card.footer>
                </x-wirekit::card>

                <!-- Agriculture, Rural & Farming -->
                <x-wirekit::card data-category-card>
                    <x-wirekit::card.header>
                        <h3 class="font-heading font-semibold text-xl text-wk-accent">Agriculture & Farming</h3>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <ul class="space-y-2 text-sm text-wk-text-subtle">
                            <li data-service-item data-service-name="Farm Hand (Kibarua)" data-service-category="Agriculture Farming"><x-wirekit::link href="/services/farm-hand">Farm Hand (Kibarua)</x-wirekit::link></li>
                            <li data-service-item data-service-name="Tractor Operator" data-service-category="Agriculture Farming"><x-wirekit::link href="/services/tractor-operator">Tractor Operator</x-wirekit::link></li>
                            <li data-service-item data-service-name="Veterinary Officer" data-service-category="Agriculture Farming"><x-wirekit::link href="/services/veterinary-officer">Veterinary Officer</x-wirekit::link></li>
                            <li data-service-item data-service-name="Agronomist" data-service-category="Agriculture Farming"><x-wirekit::link href="/services/agronomist">Agronomist</x-wirekit::link></li>
                            <li data-service-item data-service-name="Produce Transporter" data-service-category="Agriculture Farming"><x-wirekit::link href="/services/produce-transporter">Produce Transporter</x-wirekit::link></li>
                        </ul>
                    </x-wirekit::card.body>
                    <x-wirekit::card.footer>
                        <x-wirekit::button href="/services/agriculture-rural-farming" size="sm" intent="neutral" surface="outline" class="w-full">Explore Category</x-wirekit::button>
                    </x-wirekit::card.footer>
                </x-wirekit::card>

                <!-- Business, Cyber, Education & Health -->
                <x-wirekit::card data-category-card>
                    <x-wirekit::card.header>
                        <h3 class="font-heading font-semibold text-xl text-wk-accent">Business & Education</h3>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <ul class="space-y-2 text-sm text-wk-text-subtle">
                            <li data-service-item data-service-name="Tutor (Primary/Secondary)" data-service-category="Business Education"><x-wirekit::link href="/services/tutor">Tutor (Primary/Secondary)</x-wirekit::link></li>
                            <li data-service-item data-service-name="Cybercafe Attendant" data-service-category="Business Education"><x-wirekit::link href="/services/cybercafe-attendant">Cybercafe Attendant</x-wirekit::link></li>
                            <li data-service-item data-service-name="Graphic Designer" data-service-category="Business Education"><x-wirekit::link href="/services/graphic-designer">Graphic Designer</x-wirekit::link></li>
                            <li data-service-item data-service-name="Photographer" data-service-category="Business Education"><x-wirekit::link href="/services/photographer">Photographer</x-wirekit::link></li>
                            <li data-service-item data-service-name="Translator" data-service-category="Business Education"><x-wirekit::link href="/services/translator">Translator</x-wirekit::link></li>
                        </ul>
                    </x-wirekit::card.body>
                    <x-wirekit::card.footer>
                        <x-wirekit::button href="/services/business-cyber-education-health" size="sm" intent="neutral" surface="outline" class="w-full">Explore Category</x-wirekit::button>
                    </x-wirekit::card.footer>
                </x-wirekit::card>
            </x-wirekit::grid>
        </x-wirekit::container>
    </x-wirekit::section>
</div>
