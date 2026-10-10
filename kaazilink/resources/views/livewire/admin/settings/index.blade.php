<div>
    <h1 class="text-3xl font-bold mb-6 text-[#006B3F]">Site Settings</h1>
    
    <div class="bg-white shadow rounded-lg p-6 max-w-3xl">
        
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <form wire:submit="save" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Platform Commission Percentage (%)</label>
                <div class="mt-1">
                    <input type="number" step="0.1" wire:model="commission_percentage" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#006B3F] focus:border-[#006B3F] sm:text-sm">
                </div>
                <p class="mt-1 text-xs text-gray-500">The default cut Kaazilink takes from every completed booking.</p>
                @error('commission_percentage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Support Email</label>
                <div class="mt-1">
                    <input type="email" wire:model="support_email" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#006B3F] focus:border-[#006B3F] sm:text-sm">
                </div>
                @error('support_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t">
                <button type="submit" class="bg-[#006B3F] hover:bg-[#005a35] text-white font-bold py-2 px-6 rounded shadow">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
