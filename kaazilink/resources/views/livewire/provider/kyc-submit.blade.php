<div class="max-w-2xl mx-auto py-12 px-4">
    <div class="bg-white shadow rounded-lg p-8">
        <h2 class="text-2xl font-bold text-[#006B3F] mb-6">KYC Document Submission</h2>
        
        @if(auth()->user()->kyc_status === 'rejected')
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            <strong>Previous Submission Rejected:</strong><br>
                            {{ auth()->user()->kycSubmissions()->latest()->first()?->rejection_reason ?? 'Please resubmit your documents.' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit="submit" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">National ID (Front)</label>
                <div class="mt-1">
                    <input type="file" wire:model="idFront" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#006B3F] file:text-white hover:file:bg-[#005a35]"/>
                </div>
                @error('idFront') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">National ID (Back)</label>
                <div class="mt-1">
                    <input type="file" wire:model="idBack" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#006B3F] file:text-white hover:file:bg-[#005a35]"/>
                </div>
                @error('idBack') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">KRA PIN Certificate (PDF or Image)</label>
                <div class="mt-1">
                    <input type="file" wire:model="kraPin" accept=".pdf,image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#006B3F] file:text-white hover:file:bg-[#005a35]"/>
                </div>
                @error('kraPin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#006B3F] hover:bg-[#005a35] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#006B3F]">
                    Submit Documents for Review
                </button>
            </div>
            
            <div wire:loading wire:target="submit" class="text-sm text-[#E8A317] font-medium text-center w-full">
                Uploading documents... Please wait.
            </div>
        </form>
    </div>
</div>
