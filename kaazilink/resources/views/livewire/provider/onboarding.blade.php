<div class="py-8 bg-gray-50 min-h-[calc(100vh-4rem)]">
    <x-wirekit::container class="max-w-4xl">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-heading font-bold text-gray-900 mb-2">Provider Onboarding</h1>
            <p class="text-gray-600 max-w-xl mx-auto">
                Set up your verified provider profile to start offering services and receiving client bookings in Nairobi.
            </p>
        </div>

        <!-- 4-Step Stepper Navigation -->
        <div class="mb-8">
            <div class="flex items-center justify-between relative">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-gray-200 -z-0"></div>
                
                <!-- Step 1 Indicator -->
                <button 
                    type="button" 
                    wire:click="setStep(1)" 
                    class="relative z-10 flex flex-col items-center group focus:outline-none"
                >
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-colors {{ $currentStep >= 1 ? 'bg-emerald-600 text-white shadow-md' : 'bg-white border-2 border-gray-300 text-gray-500' }}">
                        @if(!empty($user->id_number) && !empty($user->phone)) ✓ @else 1 @endif
                    </div>
                    <span class="mt-2 text-xs font-medium {{ $currentStep === 1 ? 'text-emerald-700 font-semibold' : 'text-gray-500' }}">
                        1. Personal Details
                    </span>
                </button>

                <!-- Step 2 Indicator -->
                <button 
                    type="button" 
                    wire:click="setStep(2)" 
                    class="relative z-10 flex flex-col items-center group focus:outline-none"
                >
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-colors {{ $currentStep >= 2 ? 'bg-emerald-600 text-white shadow-md' : 'bg-white border-2 border-gray-300 text-gray-500' }}">
                        @if($providerServices->count() > 0) ✓ @else 2 @endif
                    </div>
                    <span class="mt-2 text-xs font-medium {{ $currentStep === 2 ? 'text-emerald-700 font-semibold' : 'text-gray-500' }}">
                        2. Services & Pricing
                    </span>
                </button>

                <!-- Step 3 Indicator -->
                <button 
                    type="button" 
                    wire:click="setStep(3)" 
                    class="relative z-10 flex flex-col items-center group focus:outline-none"
                >
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-colors {{ $currentStep >= 3 ? 'bg-emerald-600 text-white shadow-md' : 'bg-white border-2 border-gray-300 text-gray-500' }}">
                        @if($providerAreas->count() > 0) ✓ @else 3 @endif
                    </div>
                    <span class="mt-2 text-xs font-medium {{ $currentStep === 3 ? 'text-emerald-700 font-semibold' : 'text-gray-500' }}">
                        3. Service Areas
                    </span>
                </button>

                <!-- Step 4 Indicator -->
                <button 
                    type="button" 
                    wire:click="setStep(4)" 
                    class="relative z-10 flex flex-col items-center group focus:outline-none"
                >
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-colors {{ $currentStep >= 4 ? 'bg-emerald-600 text-white shadow-md' : 'bg-white border-2 border-gray-300 text-gray-500' }}">
                        @if($user->kyc_status === 'approved') ✓ @else 4 @endif
                    </div>
                    <span class="mt-2 text-xs font-medium {{ $currentStep === 4 ? 'text-emerald-700 font-semibold' : 'text-gray-500' }}">
                        4. KYC Verification
                    </span>
                </button>
            </div>
        </div>

        <!-- Flash Banners -->
        @if(session()->has('personal_success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('personal_success') }}</span>
            </div>
        @endif

        @if(session()->has('service_success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('service_success') }}</span>
            </div>
        @endif

        @if(session()->has('area_success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('area_success') }}</span>
            </div>
        @endif

        @if(session()->has('kyc_success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('kyc_success') }}</span>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- STEP 1: PERSONAL DETAILS                                       -->
        <!-- ============================================================== -->
        @if($currentStep === 1)
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <div class="flex items-center justify-between">
                        <div>
                            <x-wirekit::heading size="lg">Step 1: Personal Details</x-wirekit::heading>
                            <x-wirekit::text variant="muted">Provide your legal identification and contact details for vetting and profile creation.</x-wirekit::text>
                        </div>
                    </div>
                </x-wirekit::card.header>

                <x-wirekit::card.body>
                    <form wire:submit.prevent="savePersonalDetails" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Legal Name <span class="text-red-500">*</span></label>
                                <input 
                                    type="text" 
                                    wire:model="name" 
                                    placeholder="e.g. John Kamau Ochieng"
                                    class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                />
                                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number (M-Pesa active) <span class="text-red-500">*</span></label>
                                <input 
                                    type="text" 
                                    wire:model="phone" 
                                    placeholder="e.g. 0712345678 or +254712345678"
                                    class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                />
                                @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- National ID Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">National ID Number <span class="text-red-500">*</span></label>
                                <input 
                                    type="text" 
                                    wire:model="id_number" 
                                    placeholder="e.g. 12345678"
                                    class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                />
                                @error('id_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth (Must be 18+) <span class="text-red-500">*</span></label>
                                <input 
                                    type="date" 
                                    wire:model="date_of_birth" 
                                    class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                />
                                @error('date_of_birth') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Physical Address -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Primary Base / Physical Address <span class="text-red-500">*</span></label>
                                <input 
                                    type="text" 
                                    wire:model="address" 
                                    placeholder="e.g. Biashara Plaza, Room 4, Ngara, Nairobi"
                                    class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                />
                                @error('address') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Years of Experience -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Years of Practical Experience <span class="text-red-500">*</span></label>
                                <input 
                                    type="number" 
                                    wire:model="years_of_experience" 
                                    min="0" 
                                    max="50"
                                    placeholder="e.g. 5"
                                    class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                />
                                @error('years_of_experience') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Emergency Contact -->
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact (Name & Phone)</label>
                                <input 
                                    type="text" 
                                    wire:model="emergency_contact" 
                                    placeholder="e.g. Jane Kamau (Spouse) - 0722000000"
                                    class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                />
                                @error('emergency_contact') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Bio / Summary -->
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Professional Bio & Skills Summary <span class="text-red-500">*</span></label>
                                <textarea 
                                    wire:model="bio" 
                                    rows="4" 
                                    placeholder="Introduce yourself to clients. Highlight your craftsmanship, reliability, past projects, and specializations..."
                                    class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                ></textarea>
                                <p class="text-xs text-gray-500 mt-1">Minimum 20 characters. This will be shown on your public verified profile.</p>
                                @error('bio') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <x-wirekit::button type="submit" intent="primary" size="lg">
                                Save & Continue to Services &rarr;
                            </x-wirekit::button>
                        </div>
                    </form>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif

        <!-- ============================================================== -->
        <!-- STEP 2: SERVICES & PRICING                                     -->
        <!-- ============================================================== -->
        @if($currentStep === 2)
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <div class="flex items-center justify-between">
                        <div>
                            <x-wirekit::heading size="lg">Step 2: Services You Offer & Pricing</x-wirekit::heading>
                            <x-wirekit::text variant="muted">Choose standard platform services or add custom offerings, and set your own prices in KES.</x-wirekit::text>
                        </div>
                        <x-wirekit::badge intent="{{ $providerServices->count() > 0 ? 'success' : 'neutral' }}">
                            {{ $providerServices->count() }} Added
                        </x-wirekit::badge>
                    </div>
                </x-wirekit::card.header>

                <x-wirekit::card.body>
                    @error('services_required')
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">{{ $message }}</div>
                    @enderror

                    <!-- Service Creation Box -->
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 mb-6">
                        <!-- Mode Tabs -->
                        <div class="flex items-center gap-3 mb-4">
                            <button 
                                type="button" 
                                wire:click="$set('isCustomService', false)"
                                class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors {{ !$isCustomService ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100' }}"
                            >
                                Standard Platform Service
                            </button>
                            <button 
                                type="button" 
                                wire:click="$set('isCustomService', true)"
                                class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors {{ $isCustomService ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100' }}"
                            >
                                + Create Custom Service
                            </button>
                        </div>

                        @if(!$isCustomService)
                            <!-- Standard Service Form -->
                            <form wire:submit.prevent="addService" class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                    <div class="sm:col-span-7">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Select Service</label>
                                        <select 
                                            wire:model="selectedServiceSlug" 
                                            class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                        >
                                            <option value="">-- Choose a service --</option>
                                            @foreach($availableServices as $service)
                                                <option value="{{ $service['slug'] }}">{{ $service['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('selectedServiceSlug') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Your Price (KES)</label>
                                        <input 
                                            type="number" 
                                            wire:model="servicePrice" 
                                            placeholder="e.g. 2500" 
                                            class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                        />
                                        @error('servicePrice') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="sm:col-span-2 flex items-end">
                                        <x-wirekit::button type="submit" intent="primary" class="w-full">
                                            Add Service
                                        </x-wirekit::button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <!-- Custom Service Form -->
                            <form wire:submit.prevent="addService" class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                    <div class="sm:col-span-5">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Custom Service Name <span class="text-red-500">*</span></label>
                                        <input 
                                            type="text" 
                                            wire:model="customServiceName" 
                                            placeholder="e.g. Solar Inverter Installation" 
                                            class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                        />
                                        @error('customServiceName') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="sm:col-span-4">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Description (Optional)</label>
                                        <input 
                                            type="text" 
                                            wire:model="customServiceDescription" 
                                            placeholder="e.g. Complete setup and battery wiring" 
                                            class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                        />
                                        @error('customServiceDescription') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Price (KES) <span class="text-red-500">*</span></label>
                                        <input 
                                            type="number" 
                                            wire:model="customServicePrice" 
                                            placeholder="e.g. 7500" 
                                            class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                                        />
                                        @error('customServicePrice') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="sm:col-span-12 flex justify-end gap-2 pt-2">
                                        <x-wirekit::button type="button" intent="neutral" surface="outline" size="sm" wire:click="$set('isCustomService', false)">Cancel</x-wirekit::button>
                                        <x-wirekit::button type="submit" intent="primary" size="sm">Save Custom Service</x-wirekit::button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>

                    <!-- Active Services List -->
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Your Configured Services</h3>
                    <div class="space-y-3">
                        @forelse($providerServices as $ps)
                            <div class="flex items-center justify-between p-3.5 bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-gray-900 text-base">{{ $ps->display_name }}</h4>
                                        @if($ps->is_custom)
                                            <span class="px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-800 rounded">Custom Service</span>
                                        @endif
                                    </div>
                                    @if($ps->custom_description)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $ps->custom_description }}</p>
                                    @endif
                                    
                                    <!-- Price Display or Inline Edit -->
                                    <div class="mt-2 flex items-center gap-2">
                                        @if($editingServiceId === $ps->id)
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-gray-600">KES</span>
                                                <input 
                                                    type="number" 
                                                    wire:model="editPrice" 
                                                    class="w-28 px-2 py-1 text-sm border border-emerald-500 rounded focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                                    placeholder="Price"
                                                />
                                                <button 
                                                    type="button" 
                                                    wire:click="saveEditPrice({{ $ps->id }})" 
                                                    class="px-2 py-1 bg-emerald-600 text-white rounded text-xs font-semibold hover:bg-emerald-700"
                                                >
                                                    Save
                                                </button>
                                                <button 
                                                    type="button" 
                                                    wire:click="cancelEditPrice" 
                                                    class="px-2 py-1 text-xs text-gray-600 hover:text-gray-900"
                                                >
                                                    Cancel
                                                </button>
                                            </div>
                                            @error('editPrice') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                        @else
                                            <span class="font-bold text-emerald-700 text-sm">{{ $ps->formatted_price }}</span>
                                            <button 
                                                type="button" 
                                                wire:click="startEditPrice({{ $ps->id }}, {{ $ps->price ?: 0 }})" 
                                                class="text-xs text-blue-600 hover:underline ml-2"
                                            >
                                                Edit Price
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <button 
                                    type="button" 
                                    wire:click="removeService({{ $ps->id }})" 
                                    class="text-xs text-red-600 hover:text-red-800 font-medium px-2 py-1 rounded hover:bg-red-50"
                                >
                                    Remove
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-6 border-2 border-dashed border-gray-200 rounded-lg text-gray-500 text-sm">
                                No services added yet. Select a platform service or add a custom service above.
                            </div>
                        @endforelse
                    </div>

                    <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200">
                        <x-wirekit::button type="button" intent="neutral" surface="outline" wire:click="setStep(1)">
                            &larr; Back to Details
                        </x-wirekit::button>
                        <x-wirekit::button type="button" intent="primary" wire:click="continueToAreas">
                            Continue to Service Areas &rarr;
                        </x-wirekit::button>
                    </div>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif

        <!-- ============================================================== -->
        <!-- STEP 3: SERVICE AREAS                                          -->
        <!-- ============================================================== -->
        @if($currentStep === 3)
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <div class="flex items-center justify-between">
                        <div>
                            <x-wirekit::heading size="lg">Step 3: Service Areas in Nairobi</x-wirekit::heading>
                            <x-wirekit::text variant="muted">Select the neighborhoods where you can travel to serve clients.</x-wirekit::text>
                        </div>
                        <x-wirekit::badge intent="{{ $providerAreas->count() > 0 ? 'success' : 'neutral' }}">
                            {{ $providerAreas->count() }} Areas Selected
                        </x-wirekit::badge>
                    </div>
                </x-wirekit::card.header>

                <x-wirekit::card.body>
                    @error('areas_required')
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">{{ $message }}</div>
                    @enderror

                    <form wire:submit.prevent="addArea" class="space-y-4 mb-6">
                        <div class="flex gap-2">
                            <select 
                                wire:model="selectedAreaId" 
                                class="flex-1 px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                            >
                                <option value="">-- Choose an area in Nairobi --</option>
                                @foreach($availableAreas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            <x-wirekit::button type="submit" intent="primary">
                                Add Area
                            </x-wirekit::button>
                        </div>
                    </form>

                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Your Coverage Areas</h3>
                    <div class="flex flex-wrap gap-2 mb-6">
                        @forelse($providerAreas as $area)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                {{ $area->name }}
                                <button type="button" wire:click="removeArea({{ $area->id }})" class="hover:text-emerald-900 focus:outline-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </span>
                        @empty
                            <p class="text-sm text-gray-500 italic">No areas selected yet. Add at least one area to continue.</p>
                        @endforelse
                    </div>

                    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                        <x-wirekit::button type="button" intent="neutral" surface="outline" wire:click="setStep(2)">
                            &larr; Back to Services
                        </x-wirekit::button>
                        <x-wirekit::button type="button" intent="primary" wire:click="continueToKyc">
                            Continue to Verification &rarr;
                        </x-wirekit::button>
                    </div>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif

        <!-- ============================================================== -->
        <!-- STEP 4: KYC VERIFICATION                                       -->
        <!-- ============================================================== -->
        @if($currentStep === 4)
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <div class="flex items-center justify-between">
                        <div>
                            <x-wirekit::heading size="lg">Step 4: Identity Verification (KYC)</x-wirekit::heading>
                            <x-wirekit::text variant="muted">Upload your government-issued identification and tax documents for vetting.</x-wirekit::text>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $user->kyc_status === 'approved' ? 'bg-green-100 text-green-800' : ($user->kyc_status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">
                            {{ $user->kyc_status ?: 'Unsubmitted' }}
                        </span>
                    </div>
                </x-wirekit::card.header>

                <x-wirekit::card.body>
                    @if($user->kyc_status === 'pending')
                        <div class="p-6 text-center bg-amber-50 border border-amber-200 rounded-lg mb-6">
                            <svg class="w-12 h-12 text-amber-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="text-lg font-bold text-amber-900 mb-1">Your Documents Are Under Review</h3>
                            <p class="text-sm text-amber-700 max-w-md mx-auto mb-4">
                                Thank you for submitting your KYC verification documents. Our compliance team verifies documents within 24 hours.
                            </p>
                            <a href="{{ route('provider.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md font-medium text-sm hover:bg-emerald-700 shadow-sm">
                                Go to Provider Hub &rarr;
                            </a>
                        </div>
                    @elseif($user->kyc_status === 'approved')
                        <div class="p-6 text-center bg-green-50 border border-green-200 rounded-lg mb-6">
                            <svg class="w-12 h-12 text-green-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="text-lg font-bold text-green-900 mb-1">Account Approved!</h3>
                            <p class="text-sm text-green-700 max-w-md mx-auto mb-4">
                                You are a fully verified professional on Kaazilink. Your profile is live and accepting client bookings.
                            </p>
                            <a href="{{ route('provider.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md font-medium text-sm hover:bg-emerald-700 shadow-sm">
                                Open Provider Dashboard &rarr;
                            </a>
                        </div>
                    @else
                        <form wire:submit.prevent="submitKyc" class="space-y-6">
                            <!-- National ID Front -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">National ID (Front Side) <span class="text-red-500">*</span></label>
                                <input 
                                    type="file" 
                                    wire:model="idFront" 
                                    accept="image/*" 
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-300 rounded-md p-1" 
                                />
                                <div wire:loading wire:target="idFront" class="text-xs text-emerald-600 mt-1 flex items-center gap-1">
                                    <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Uploading photo...
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Accepts JPG, PNG up to 5MB.</p>
                                @error('idFront') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- National ID Back -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">National ID (Back Side) <span class="text-red-500">*</span></label>
                                <input 
                                    type="file" 
                                    wire:model="idBack" 
                                    accept="image/*" 
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-300 rounded-md p-1" 
                                />
                                <div wire:loading wire:target="idBack" class="text-xs text-emerald-600 mt-1 flex items-center gap-1">
                                    <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Uploading photo...
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Accepts JPG, PNG up to 5MB.</p>
                                @error('idBack') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- KRA PIN Certificate -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">KRA PIN Certificate <span class="text-red-500">*</span></label>
                                <input 
                                    type="file" 
                                    wire:model="kraPin" 
                                    accept=".pdf,image/*" 
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-300 rounded-md p-1" 
                                />
                                <div wire:loading wire:target="kraPin" class="text-xs text-emerald-600 mt-1 flex items-center gap-1">
                                    <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Uploading certificate...
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Accepts PDF, JPG, PNG up to 5MB.</p>
                                @error('kraPin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                                <x-wirekit::button type="button" intent="neutral" surface="outline" wire:click="setStep(3)">
                                    &larr; Back to Service Areas
                                </x-wirekit::button>
                                <x-wirekit::button type="submit" intent="primary" size="lg">
                                    Submit KYC Documents for Review
                                </x-wirekit::button>
                            </div>
                        </form>
                    @endif
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif
    </x-wirekit::container>
</div>
