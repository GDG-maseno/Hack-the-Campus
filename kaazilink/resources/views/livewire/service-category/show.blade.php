<div>
    <x-wirekit::section class="bg-[var(--color-wk-bg-subtle)] border-b border-[var(--color-wk-border-subtle)]">
        <x-wirekit::container>
            <div class="py-8">
                <x-wirekit::breadcrumb class="mb-6">
                    <x-wirekit::link href="/" variant="subtle">Home</x-wirekit::link>
                    <x-wirekit::link href="/browse" variant="subtle">Services</x-wirekit::link>
                    <span class="text-wk-text font-medium">{{ str($slug)->headline() }}</span>
                </x-wirekit::breadcrumb>
                
                <h1 class="text-4xl font-heading font-bold text-wk-text mb-4">{{ str($slug)->headline() }} Providers</h1>
                <p class="text-lg text-wk-text-subtle">Found {{ $providers->total() }} vetted professionals near you.</p>
            </div>
        </x-wirekit::container>
    </x-wirekit::section>

    <x-wirekit::section>
        <x-wirekit::container>
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Filters Sidebar -->
                <div class="w-full md:w-64 shrink-0">
                    <x-wirekit::card>
                        <x-wirekit::card.header>
                            <h3 class="font-heading font-semibold text-lg text-wk-accent">Filters</h3>
                        </x-wirekit::card.header>
                        <x-wirekit::card.body>
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-medium text-wk-text mb-2">Location</h4>
                                    <x-wirekit::combobox
                                        name="searchAreaId"
                                        label=""
                                        wire:model.live="searchAreaId"
                                        :options="$availableAreasOptions"
                                        placeholder="Search areas..."
                                    />
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-wk-text mb-2">Price Range</h4>
                                    <div class="space-y-2">
                                        <x-wirekit::checkbox name="price" value="low">Under KES 1,000</x-wirekit::checkbox>
                                        <x-wirekit::checkbox name="price" value="med">KES 1,000 - 3,000</x-wirekit::checkbox>
                                        <x-wirekit::checkbox name="price" value="high">Over KES 3,000</x-wirekit::checkbox>
                                    </div>
                                </div>
                            </div>
                        </x-wirekit::card.body>
                    </x-wirekit::card>
                </div>

                <!-- Provider List -->
                <div class="flex-1">
                    @if(count($mapMarkers) > 0)
                        <x-wirekit::map 
                            :markers="$mapMarkers" 
                            styleUrl="https://tiles.openfreemap.org/styles/positron"
                            :zoom="10"
                            class="mb-6"
                        />
                    @endif

                    <x-wirekit::grid cols="1 lg:2" class="gap-6">
                        @forelse($providers as $provider)
                        <x-wirekit::card>
                            <x-wirekit::card.body class="flex gap-4">
                                <x-wirekit::avatar src="https://ui-avatars.com/api/?name={{ urlencode($provider->name) }}&background=006B3F&color=fff" size="lg" />
                                <div>
                                    <h3 class="font-heading font-semibold text-lg">{{ $provider->name }}</h3>
                                    @if(isset($provider->distance))
                                        <p class="text-sm text-wk-text-subtle mb-2">{{ number_format($provider->distance, 1) }} km away</p>
                                    @else
                                        <p class="text-sm text-wk-text-subtle mb-2">Serves {{ $provider->serviceAreas->count() ?: 'multiple' }} areas</p>
                                    @endif
                                    <x-wirekit::badge intent="warning" class="mb-2">★ 5.0 (New)</x-wirekit::badge>
                                    @php
                                        $matchedPs = $provider->providerServices->first(function($ps) use ($serviceSlugs) {
                                            return in_array($ps->service_slug, $serviceSlugs);
                                        }) ?? $provider->providerServices->first();
                                    @endphp
                                    @if($matchedPs && $matchedPs->price > 0)
                                        <p class="text-sm font-bold text-wk-accent">{{ $matchedPs->formatted_price }}</p>
                                    @else
                                        <p class="text-sm font-medium text-wk-accent">Contact for pricing</p>
                                    @endif
                                </div>
                            </x-wirekit::card.body>
                            <x-wirekit::card.footer class="flex justify-between">
                                <x-wirekit::button href="{{ route('provider.profile', ['slug' => $provider->id]) }}" size="sm" intent="neutral" surface="outline">View Profile</x-wirekit::button>
                                @auth
                                    <x-wirekit::button size="sm" wire:click="bookNow({{ $provider->id }})">Book Now</x-wirekit::button>
                                @else
                                    <x-wirekit::button size="sm" href="{{ route('login') }}">Book Now</x-wirekit::button>
                                @endauth
                            </x-wirekit::card.footer>
                        </x-wirekit::card>
                        @empty
                        <div class="col-span-full py-8 text-center text-gray-500">
                            No approved providers found for this category yet.
                        </div>
                        @endforelse
                    </x-wirekit::grid>

                    <div class="mt-8">
                        {{ $providers->links() }}
                    </div>
                </div>
            </div>
        </x-wirekit::container>
    </x-wirekit::section>

    <livewire:booking.create-modal />
    <livewire:review.create-modal />
</div>
