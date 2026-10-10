<div
    x-data="{
        init() {
            Livewire.on('schedule-modal-close', () => {
                setTimeout(() => {
                    $wire.closeModal();
                }, 800);
            });
        }
    }"
>
    <x-wirekit::modal name="booking-create-modal" ariaLabel="Book Provider">
        <x-wirekit::card wire:id="{{ $this->getId() }}">
            <x-wirekit::card.header>
                <div class="flex items-center justify-between">
                    <div>
                        <x-wirekit::heading size="lg">Book {{ $provider ? $provider->name : 'Provider' }}</x-wirekit::heading>
                        <x-wirekit::text variant="muted">Request a service from this verified professional.</x-wirekit::text>
                    </div>
                </div>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                @if($successMessage || session()->has('success'))
                    <div class="p-4 mb-4 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $successMessage ?: session('success') }}</span>
                    </div>
                @endif

                @if($errorMessage)
                    <div class="p-4 mb-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $errorMessage }}</span>
                    </div>
                @endif

                <form wire:submit="submit" @submit.prevent="$wire.submit()" class="space-y-4">
                    <!-- Service Selection (Searchable Dropdown / Autocomplete) -->
                    <div 
                        x-data="{
                            open: false,
                            search: '',
                            selectedVal: @entangle('serviceSlug').live,
                            options: {{ json_encode($servicesOptions) }},
                            get currentLabel() {
                                const found = this.options.find(o => o.value == this.selectedVal);
                                return found ? found.label : '';
                            },
                            get filtered() {
                                if (!this.search || this.search === this.currentLabel) {
                                    return this.options;
                                }
                                return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            init() {
                                this.search = this.currentLabel;
                                this.$watch('selectedVal', () => {
                                    this.search = this.currentLabel;
                                });
                            },
                            select(opt) {
                                this.selectedVal = opt.value;
                                this.search = opt.label;
                                this.open = false;
                            },
                            onInput() {
                                this.open = true;
                                const match = this.options.find(o => o.label.toLowerCase() === this.search.trim().toLowerCase());
                                if (match) {
                                    this.selectedVal = match.value;
                                } else {
                                    this.selectedVal = '';
                                }
                            }
                        }"
                        @click.outside="open = false"
                        class="relative"
                    >
                        <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="search"
                                @focus="open = true"
                                @input="onInput()"
                                placeholder="Select or search a service..."
                                class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300 pr-10"
                                autocomplete="off"
                            />
                            <button 
                                type="button" 
                                @click="open = !open" 
                                class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                tabindex="-1"
                            >
                                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Dropdown panel -->
                        <div 
                            x-show="open" 
                            x-cloak 
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-56 overflow-y-auto"
                        >
                            <template x-if="filtered.length === 0">
                                <div class="px-4 py-2.5 text-sm text-gray-500">No services match your search</div>
                            </template>
                            <template x-for="opt in filtered" :key="opt.value">
                                <div 
                                    @click="select(opt)"
                                    class="px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-900 cursor-pointer flex items-center justify-between"
                                    :class="{ 'bg-emerald-50 text-emerald-900 font-semibold': selectedVal == opt.value }"
                                >
                                    <span x-text="opt.label"></span>
                                    <span x-show="selectedVal == opt.value" class="text-emerald-600 text-xs font-bold">Selected</span>
                                </div>
                            </template>
                        </div>
                        @error('serviceSlug')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Service Area Selection (Searchable Autocomplete Dropdown) -->
                    <div 
                        x-data="{
                            open: false,
                            search: '',
                            selectedId: @entangle('serviceAreaId').live,
                            options: {{ json_encode($areasOptions) }},
                            get currentLabel() {
                                const found = this.options.find(o => o.value == this.selectedId);
                                return found ? found.label : '';
                            },
                            get filtered() {
                                if (!this.search || this.search === this.currentLabel) {
                                    return this.options;
                                }
                                return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            init() {
                                this.search = this.currentLabel;
                                this.$watch('selectedId', () => {
                                    this.search = this.currentLabel;
                                });
                            },
                            select(opt) {
                                this.selectedId = opt.value;
                                this.search = opt.label;
                                this.open = false;
                            },
                            onInput() {
                                this.open = true;
                                const match = this.options.find(o => o.label.toLowerCase() === this.search.trim().toLowerCase());
                                if (match) {
                                    this.selectedId = match.value;
                                } else {
                                    this.selectedId = '';
                                }
                            }
                        }"
                        @click.outside="open = false"
                        class="relative"
                    >
                        <label class="block text-sm font-medium text-gray-700 mb-1">Service Area</label>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="search"
                                @focus="open = true"
                                @input="onInput()"
                                placeholder="Search areas served by this provider (e.g. Eastleigh)..."
                                class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300 pr-10"
                                autocomplete="off"
                            />
                            <button 
                                type="button" 
                                @click="open = !open" 
                                class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                tabindex="-1"
                            >
                                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Dropdown panel -->
                        <div 
                            x-show="open" 
                            x-cloak 
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-56 overflow-y-auto"
                        >
                            <template x-if="filtered.length === 0">
                                <div class="px-4 py-2.5 text-sm text-gray-500">No areas found for this provider</div>
                            </template>
                            <template x-for="opt in filtered" :key="opt.value">
                                <div 
                                    @click="select(opt)"
                                    class="px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-900 cursor-pointer flex items-center justify-between"
                                    :class="{ 'bg-emerald-50 text-emerald-900 font-semibold': selectedId == opt.value }"
                                >
                                    <span x-text="opt.label"></span>
                                    <span x-show="selectedId == opt.value" class="text-emerald-600 text-xs font-bold">Selected</span>
                                </div>
                            </template>
                        </div>
                        @error('serviceAreaId')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-wirekit::input 
                        type="datetime-local" 
                        name="scheduledAt" 
                        label="Scheduled Time (Optional)" 
                        wire:model="scheduledAt" 
                    />

                    <div class="flex justify-end gap-2 pt-4">
                        <x-wirekit::button type="button" intent="neutral" wire:click="closeModal" @click.prevent="$wire.closeModal()">Cancel</x-wirekit::button>
                        <x-wirekit::button type="submit" intent="primary" @click.prevent="$wire.submit()">Request Booking</x-wirekit::button>
                    </div>
                </form>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </x-wirekit::modal>
</div>
