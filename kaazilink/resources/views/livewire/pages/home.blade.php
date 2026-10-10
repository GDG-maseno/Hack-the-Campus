<div>
    {{-- Hero Section: Full-bleed with hero.jpeg background --}}
    <section class="relative w-full overflow-hidden" style="min-height: 520px;">
        {{-- Background image --}}
        <div class="absolute inset-0">
            <img
                src="/images/hero.jpeg"
                alt=""
                aria-hidden="true"
                class="w-full h-full object-cover object-center"
            >
            {{-- Gradient overlay: dark on left for text readability, fades to transparent on right --}}
            <div class="absolute inset-0" style="background: linear-gradient(105deg, rgba(0,0,0,0.72) 0%, rgba(0,0,0,0.55) 40%, rgba(0,0,0,0.18) 70%, rgba(0,0,0,0.05) 100%);"></div>
        </div>

        {{-- Content --}}
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 flex flex-col items-start justify-center" style="min-height: 520px;">
            {{-- Badge --}}
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#006B3F] text-white mb-6 tracking-wide uppercase">
                Tupate Fundi Haraka
            </span>

            {{-- Headline --}}
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-bold text-white leading-tight mb-6 max-w-2xl">
                Pata Kaazi<br class="hidden sm:block"> <span class="text-[#4ADE80]">Faster</span>
            </h1>

            {{-- Lede --}}
            <p class="text-lg sm:text-xl text-white/85 max-w-xl mb-8 leading-relaxed">
                Connect with vetted local service providers across Kenya. Safe, reliable, and grassroots.
            </p>

            {{-- CTAs --}}
            <div class="flex flex-wrap gap-4">
                <a href="/services"
                   class="inline-flex items-center px-6 py-3 rounded-lg bg-[#006B3F] text-white font-semibold text-base hover:bg-[#005432] transition-colors shadow-lg focus:outline-none focus:ring-2 focus:ring-[#006B3F] focus:ring-offset-2">
                    Find a Service
                </a>
                <a href="/how-it-works"
                   class="inline-flex items-center px-6 py-3 rounded-lg bg-white/15 border border-white/40 text-white font-semibold text-base hover:bg-white/25 transition-colors backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2">
                    How It Works
                </a>
            </div>
        </div>
    </section>

    {{-- Trust strip: uses banner.jpg as a rich visual accent strip --}}
    <div class="relative w-full overflow-hidden" style="height: 120px;">
        <img src="/images/banner.jpg" alt="KaaziLink service professionals" class="w-full h-full object-cover object-bottom" aria-hidden="true">
        <div class="absolute inset-0 bg-[#006B3F]/80 flex items-center justify-center">
            <div class="max-w-4xl mx-auto px-4 flex flex-wrap items-center justify-center gap-8 sm:gap-16 text-white text-sm font-semibold">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#4ADE80] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    More Jobs, More Opportunities
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#4ADE80] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Work Today, Get Paid Early
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#4ADE80] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Empowering You Every Step
                </div>
            </div>
        </div>
    </div>


    <x-wirekit::section>
        <x-wirekit::container>
            <h2 class="text-3xl font-heading font-semibold text-center mb-12">Popular Service Categories</h2>
            
            <x-wirekit::grid cols="1 md:3 lg:3" class="gap-6">
                <x-wirekit::card>
                    <x-wirekit::card.header>
                        <h3 class="font-heading font-semibold text-xl text-wk-accent">Home Care</h3>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <p class="text-wk-text-subtle">Househelp, Nannies, Cleaners, Laundry</p>
                    </x-wirekit::card.body>
                    <x-wirekit::card.footer>
                        <x-wirekit::button href="/services/home-care" size="sm" intent="neutral" surface="outline">View Services</x-wirekit::button>
                    </x-wirekit::card.footer>
                </x-wirekit::card>
                
                <x-wirekit::card>
                    <x-wirekit::card.header>
                        <h3 class="font-heading font-semibold text-xl text-wk-accent">Fundi & Technical</h3>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <p class="text-wk-text-subtle">Plumbers, Electricians, Carpenters, Mechanics</p>
                    </x-wirekit::card.body>
                    <x-wirekit::card.footer>
                        <x-wirekit::button href="/services/fundi-technical-repairs" size="sm" intent="neutral" surface="outline">View Services</x-wirekit::button>
                    </x-wirekit::card.footer>
                </x-wirekit::card>

                <x-wirekit::card>
                    <x-wirekit::card.header>
                        <h3 class="font-heading font-semibold text-xl text-wk-accent">Daily Needs</h3>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <p class="text-wk-text-subtle">Riders, Water Delivery, Gas Refill</p>
                    </x-wirekit::card.body>
                    <x-wirekit::card.footer>
                        <x-wirekit::button href="/services/daily-needs-emergency-response" size="sm" intent="neutral" surface="outline">View Services</x-wirekit::button>
                    </x-wirekit::card.footer>
                </x-wirekit::card>
            </x-wirekit::grid>
            
            <div class="mt-12 text-center">
                <x-wirekit::button href="/services" surface="outline">View All 51 Services</x-wirekit::button>
            </div>
        </x-wirekit::container>
    </x-wirekit::section>
</div>
