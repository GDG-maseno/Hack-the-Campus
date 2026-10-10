<div class="py-8 bg-gray-50 min-h-[calc(100vh-4rem)]">
    <x-wirekit::container class="max-w-6xl">
        <!-- Top Banner / Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-2xl shadow-inner">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-heading font-bold text-gray-900">{{ $user->name }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $user->kyc_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $user->kyc_status === 'approved' ? 'Verified Provider' : 'KYC ' . ucfirst($user->kyc_status ?: 'Pending') }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $user->phone ?: 'No phone set' }} &bull; {{ $user->address ?: 'Nairobi' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($user->kyc_status === 'approved')
                    <a href="{{ route('provider.profile', $user->id) }}" target="_blank" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        View Public Profile
                    </a>
                @endif
                <a href="{{ route('provider.onboarding') }}" class="px-4 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors border border-emerald-200">
                    Onboarding Steps
                </a>
            </div>
        </div>

        @if(session()->has('success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Navigation Tabs -->
        <div class="mb-6 border-b border-gray-200 bg-white rounded-t-xl px-4 pt-2 shadow-sm">
            <nav class="flex space-x-6 overflow-x-auto" aria-label="Tabs">
                <button 
                    type="button" 
                    wire:click="setTab('overview')"
                    class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'overview' ? 'border-emerald-600 text-emerald-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Overview
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('services')"
                    class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'services' ? 'border-emerald-600 text-emerald-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Services & Pricing ({{ $providerServices->count() }})
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('areas')"
                    class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'areas' ? 'border-emerald-600 text-emerald-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Coverage Areas ({{ $providerAreas->count() }})
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('bookings')"
                    class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'bookings' ? 'border-emerald-600 text-emerald-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Bookings ({{ $totalBookings }})
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('profile')"
                    class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'profile' ? 'border-emerald-600 text-emerald-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Profile & Details
                </button>
            </nav>
        </div>

        <!-- ============================================================== -->
        <!-- TAB 1: OVERVIEW                                                -->
        <!-- ============================================================== -->
        @if($activeTab === 'overview')
            <div class="space-y-6">
                <!-- 4 Metrics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Earnings -->
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Earnings</span>
                        <h3 class="text-2xl font-bold text-gray-900 mt-2">KES {{ number_format($totalEarnings, 0) }}</h3>
                        <p class="text-xs text-gray-500 mt-1">From completed bookings</p>
                    </div>

                    <!-- Completed Jobs -->
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Completed Jobs</span>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-2">{{ $completedBookings }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $pendingBookings }} pending requests</p>
                    </div>

                    <!-- Active Services -->
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Services</span>
                        <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ $providerServices->count() }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Across {{ $providerAreas->count() }} Nairobi areas</p>
                    </div>

                    <!-- Rating -->
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Client Rating</span>
                        <h3 class="text-2xl font-bold text-amber-500 mt-2">★ {{ $avgRating }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $reviewsCount }} customer reviews</p>
                    </div>
                </div>

                <!-- Recent Bookings Table -->
                <x-wirekit::card>
                    <x-wirekit::card.header>
                        <div class="flex items-center justify-between">
                            <x-wirekit::heading size="md">Recent Booking Requests</x-wirekit::heading>
                            <button type="button" wire:click="setTab('bookings')" class="text-sm text-emerald-600 hover:underline font-medium">View All &rarr;</button>
                        </div>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        @if($bookings->isEmpty())
                            <div class="text-center py-8 text-gray-500 text-sm">
                                No bookings received yet. Clients will book you as you appear in Nairobi search results!
                            </div>
                        @else
                            <div class="divide-y divide-gray-200">
                                @foreach($bookings->take(5) as $b)
                                    <div class="py-3 flex items-center justify-between">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-gray-900 text-sm">{{ ucfirst(str_replace('-', ' ', $b->service_slug)) }}</span>
                                                <x-wirekit::badge intent="{{ $b->status === 'completed' ? 'success' : ($b->status === 'accepted' ? 'primary' : 'warning') }}" size="xs">
                                                    {{ ucfirst($b->status) }}
                                                </x-wirekit::badge>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-0.5">Client: {{ $b->client ? $b->client->name : 'Customer' }} &bull; {{ $b->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-bold text-emerald-700 text-sm">{{ $b->price ? 'KES ' . number_format($b->price, 0) : 'Contact' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </x-wirekit::card.body>
                </x-wirekit::card>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- TAB 2: SERVICES & PRICING                                      -->
        <!-- ============================================================== -->
        @if($activeTab === 'services')
            <div class="space-y-6">
                <!-- Add Service Card -->
                <x-wirekit::card>
                    <x-wirekit::card.header>
                        <x-wirekit::heading size="md">Add or Create a Service</x-wirekit::heading>
                        <x-wirekit::text variant="muted">Choose an existing platform trade or create your own custom offering with your authoritative price.</x-wirekit::text>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <div class="flex items-center gap-3 mb-4">
                            <button 
                                type="button" 
                                wire:click="$set('isCustomService', false)"
                                class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors {{ !$isCustomService ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            >
                                Standard Platform Service
                            </button>
                            <button 
                                type="button" 
                                wire:click="$set('isCustomService', true)"
                                class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors {{ $isCustomService ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            >
                                + Create Custom Service
                            </button>
                        </div>

                        @if(!$isCustomService)
                            <form wire:submit.prevent="addService" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                <div class="sm:col-span-7">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Select Service</label>
                                    <select wire:model="selectedServiceSlug" class="w-full px-3 py-2 text-sm border rounded-md shadow-sm bg-white border-gray-300">
                                        <option value="">-- Choose a service --</option>
                                        @foreach($availableServices as $service)
                                            <option value="{{ $service['slug'] }}">{{ $service['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedServiceSlug') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Your Price (KES)</label>
                                    <input type="number" wire:model="servicePrice" placeholder="e.g. 2500" class="w-full px-3 py-2 text-sm border rounded-md shadow-sm bg-white border-gray-300" />
                                    @error('servicePrice') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="sm:col-span-2 flex items-end">
                                    <x-wirekit::button type="submit" intent="primary" class="w-full">Add</x-wirekit::button>
                                </div>
                            </form>
                        @else
                            <form wire:submit.prevent="addService" class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                    <div class="sm:col-span-5">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Custom Service Name *</label>
                                        <input type="text" wire:model="customServiceName" placeholder="e.g. Biometrics Installation" class="w-full px-3 py-2 text-sm border rounded-md shadow-sm bg-white border-gray-300" />
                                        @error('customServiceName') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="sm:col-span-4">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Description (Optional)</label>
                                        <input type="text" wire:model="customServiceDescription" placeholder="e.g. Complete setup and cabling" class="w-full px-3 py-2 text-sm border rounded-md shadow-sm bg-white border-gray-300" />
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Price (KES) *</label>
                                        <input type="number" wire:model="customServicePrice" placeholder="e.g. 8000" class="w-full px-3 py-2 text-sm border rounded-md shadow-sm bg-white border-gray-300" />
                                        @error('customServicePrice') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="sm:col-span-12 flex justify-end gap-2">
                                        <x-wirekit::button type="button" intent="neutral" surface="outline" size="sm" wire:click="$set('isCustomService', false)">Cancel</x-wirekit::button>
                                        <x-wirekit::button type="submit" intent="primary" size="sm">Save Custom Service</x-wirekit::button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </x-wirekit::card.body>
                </x-wirekit::card>

                <!-- Active Services List -->
                <x-wirekit::card>
                    <x-wirekit::card.header>
                        <x-wirekit::heading size="md">Active Services & Prices</x-wirekit::heading>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <div class="divide-y divide-gray-200">
                            @forelse($providerServices as $ps)
                                <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-gray-900 text-base">{{ $ps->display_name }}</h4>
                                            @if($ps->is_custom)
                                                <span class="px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-800 rounded">Custom</span>
                                            @else
                                                <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 rounded">Platform Service</span>
                                            @endif
                                        </div>
                                        @if($ps->custom_description)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $ps->custom_description }}</p>
                                        @endif

                                        <div class="mt-2 flex items-center gap-2">
                                            @if($editingServiceId === $ps->id)
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-gray-600">KES</span>
                                                    <input type="number" wire:model="editPrice" class="w-28 px-2 py-1 text-sm border border-emerald-500 rounded focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                                                    <button type="button" wire:click="saveEditPrice({{ $ps->id }})" class="px-2 py-1 bg-emerald-600 text-white rounded text-xs font-semibold hover:bg-emerald-700">Save</button>
                                                    <button type="button" wire:click="cancelEditPrice" class="px-2 py-1 text-xs text-gray-600 hover:text-gray-900">Cancel</button>
                                                </div>
                                                @error('editPrice') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                            @else
                                                <span class="font-bold text-emerald-700 text-sm">{{ $ps->formatted_price }}</span>
                                                <button type="button" wire:click="startEditPrice({{ $ps->id }}, {{ $ps->price ?: 0 }})" class="text-xs text-blue-600 hover:underline">
                                                    Edit Price
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <div>
                                        <button type="button" wire:click="removeService({{ $ps->id }})" class="text-xs text-red-600 hover:text-red-800 font-medium px-2.5 py-1.5 rounded hover:bg-red-50 border border-red-200">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center py-8 text-gray-500 text-sm">No services configured yet.</p>
                            @endforelse
                        </div>
                    </x-wirekit::card.body>
                </x-wirekit::card>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- TAB 3: COVERAGE AREAS                                          -->
        <!-- ============================================================== -->
        @if($activeTab === 'areas')
            <div class="space-y-6">
                <x-wirekit::card>
                    <x-wirekit::card.header>
                        <x-wirekit::heading size="md">Manage Coverage Areas</x-wirekit::heading>
                        <x-wirekit::text variant="muted">Add the neighborhoods in Nairobi where you offer fast, reliable dispatch.</x-wirekit::text>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <form wire:submit.prevent="addArea" class="flex gap-2 max-w-md mb-6">
                            <select wire:model="selectedAreaId" class="flex-1 px-3 py-2 text-sm border rounded-md shadow-sm bg-white border-gray-300">
                                <option value="">-- Choose an area in Nairobi --</option>
                                @foreach($availableAreas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            <x-wirekit::button type="submit" intent="primary">Add Area</x-wirekit::button>
                        </form>

                        <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3">Active Service Areas</h3>
                        <div class="flex flex-wrap gap-2">
                            @forelse($providerAreas as $area)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                    {{ $area->name }}
                                    <button type="button" wire:click="removeArea({{ $area->id }})" class="hover:text-emerald-900 focus:outline-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </span>
                            @empty
                                <p class="text-sm text-gray-500 italic">No areas selected yet.</p>
                            @endforelse
                        </div>
                    </x-wirekit::card.body>
                </x-wirekit::card>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- TAB 4: BOOKINGS                                                -->
        <!-- ============================================================== -->
        @if($activeTab === 'bookings')
            <div class="space-y-6">
                <!-- Status Filter Bar -->
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="$set('bookingStatusFilter', 'all')" class="px-3 py-1 text-xs font-medium rounded-full {{ $bookingStatusFilter === 'all' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-700 border border-gray-300' }}">All ({{ $totalBookings }})</button>
                    <button type="button" wire:click="$set('bookingStatusFilter', 'requested')" class="px-3 py-1 text-xs font-medium rounded-full {{ $bookingStatusFilter === 'requested' ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300' }}">Requested</button>
                    <button type="button" wire:click="$set('bookingStatusFilter', 'accepted')" class="px-3 py-1 text-xs font-medium rounded-full {{ $bookingStatusFilter === 'accepted' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300' }}">Accepted</button>
                    <button type="button" wire:click="$set('bookingStatusFilter', 'completed')" class="px-3 py-1 text-xs font-medium rounded-full {{ $bookingStatusFilter === 'completed' ? 'bg-emerald-700 text-white' : 'bg-white text-gray-700 border border-gray-300' }}">Completed</button>
                </div>

                <div class="space-y-4">
                    @forelse($bookings as $b)
                        <x-wirekit::card>
                            <x-wirekit::card.body class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <x-wirekit::badge intent="{{ $b->status === 'completed' ? 'success' : ($b->status === 'accepted' ? 'primary' : 'warning') }}" size="sm">
                                            {{ ucfirst($b->status) }}
                                        </x-wirekit::badge>
                                        <span class="text-xs text-gray-500">{{ $b->created_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                    <h3 class="font-bold text-lg text-gray-900">{{ ucfirst(str_replace('-', ' ', $b->service_slug)) }}</h3>
                                    <p class="text-sm text-gray-600 mt-0.5">
                                        Client: <span class="font-medium text-gray-900">{{ $b->client ? $b->client->name : 'Client' }}</span>
                                        @if($b->client && $b->client->phone)
                                            &bull; {{ $b->client->phone }}
                                        @endif
                                    </p>
                                    @if($b->scheduled_at)
                                        <p class="text-xs text-gray-500 mt-1">Scheduled for: <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($b->scheduled_at)->format('M j, Y g:i A') }}</span></p>
                                    @endif
                                    <p class="text-sm font-bold text-emerald-700 mt-2">{{ $b->price ? 'KES ' . number_format($b->price, 0) : 'Custom price' }}</p>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if($b->status === 'requested')
                                        <button type="button" wire:click="acceptBooking({{ $b->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded shadow-sm">
                                            Accept Request
                                        </button>
                                    @endif
                                    @if(in_array($b->status, ['requested', 'accepted']))
                                        <button type="button" wire:click="completeBooking({{ $b->id }})" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded shadow-sm">
                                            Mark Completed
                                        </button>
                                    @endif
                                </div>
                            </x-wirekit::card.body>
                        </x-wirekit::card>
                    @empty
                        <x-wirekit::card>
                            <x-wirekit::card.body class="py-12 text-center text-gray-500 text-sm">
                                No bookings found for this filter.
                            </x-wirekit::card.body>
                        </x-wirekit::card>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- TAB 5: PROFILE & DETAILS                                       -->
        <!-- ============================================================== -->
        @if($activeTab === 'profile')
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <x-wirekit::heading size="md">Profile & Personal Details</x-wirekit::heading>
                    <x-wirekit::text variant="muted">Update your public contact info, address, and skills summary.</x-wirekit::text>
                </x-wirekit::card.header>
                <x-wirekit::card.body>
                    <form wire:submit.prevent="updateProfile" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" wire:model="name" class="w-full px-3 py-2 text-sm border rounded-md shadow-sm bg-white border-gray-300" />
                                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Phone Number (M-Pesa) *</label>
                                <input type="text" wire:model="phone" class="w-full px-3 py-2 text-sm border rounded-md shadow-sm bg-white border-gray-300" />
                                @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Physical Base / Address *</label>
                                <input type="text" wire:model="address" class="w-full px-3 py-2 text-sm border rounded-md shadow-sm bg-white border-gray-300" />
                                @error('address') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Years of Practical Experience *</label>
                                <input type="number" wire:model="years_of_experience" min="0" max="50" class="w-full px-3 py-2 text-sm border rounded-md shadow-sm bg-white border-gray-300" />
                                @error('years_of_experience') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Emergency Contact</label>
                                <input type="text" wire:model="emergency_contact" class="w-full px-3 py-2 text-sm border rounded-md shadow-sm bg-white border-gray-300" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Professional Bio & Craftsmanship Summary *</label>
                                <textarea wire:model="bio" rows="4" class="w-full px-3 py-2 text-sm border rounded-md shadow-sm bg-white border-gray-300"></textarea>
                                @error('bio') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <x-wirekit::button type="submit" intent="primary">
                                Save Profile Changes
                            </x-wirekit::button>
                        </div>
                    </form>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif
    </x-wirekit::container>
</div>
