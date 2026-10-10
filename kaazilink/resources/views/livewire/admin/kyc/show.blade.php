<div>
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-[#006B3F]">Review KYC: {{ $submission->user->name }}</h1>
        <a href="{{ route('admin.kyc.index') }}" class="text-gray-600 hover:text-gray-900 underline text-sm">Back to List</a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Documents -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold border-b pb-2 mb-4">Submitted Documents</h2>
                
                <div class="space-y-8">
                    <!-- ID Front -->
                    <div>
                        <h3 class="font-medium text-gray-700 mb-2">National ID (Front)</h3>
                        @if($url = $this->getDocumentUrl($submission->id_front_path))
                            <a href="{{ $url }}" target="_blank" class="block border rounded p-2 hover:border-[#006B3F] transition">
                                <img src="{{ $url }}" alt="ID Front" class="max-h-64 object-contain mx-auto" onerror="this.outerHTML='<span class=\'text-gray-500 italic\'>Document cannot be displayed inline. Click to view.</span>'">
                            </a>
                        @else
                            <p class="text-red-500 italic">File not found.</p>
                        @endif
                    </div>

                    <!-- ID Back -->
                    <div>
                        <h3 class="font-medium text-gray-700 mb-2">National ID (Back)</h3>
                        @if($url = $this->getDocumentUrl($submission->id_back_path))
                            <a href="{{ $url }}" target="_blank" class="block border rounded p-2 hover:border-[#006B3F] transition">
                                <img src="{{ $url }}" alt="ID Back" class="max-h-64 object-contain mx-auto" onerror="this.outerHTML='<span class=\'text-gray-500 italic\'>Document cannot be displayed inline. Click to view.</span>'">
                            </a>
                        @else
                            <p class="text-red-500 italic">File not found.</p>
                        @endif
                    </div>

                    <!-- KRA PIN -->
                    <div>
                        <h3 class="font-medium text-gray-700 mb-2">KRA PIN Certificate</h3>
                        @if($url = $this->getDocumentUrl($submission->kra_pin_path))
                            <a href="{{ $url }}" target="_blank" class="text-[#006B3F] underline flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                View KRA PIN Certificate
                            </a>
                        @else
                            <p class="text-red-500 italic">File not found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="space-y-6">
            <!-- User Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold border-b pb-2 mb-4">User Info</h2>
                <p><strong>Name:</strong> {{ $submission->user->name }}</p>
                <p><strong>Email:</strong> {{ $submission->user->email }}</p>
                <p><strong>Joined:</strong> {{ $submission->user->created_at->format('M d, Y') }}</p>
            </div>

            <!-- Action Panel -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold border-b pb-2 mb-4">Decision</h2>
                
                @if($submission->status === 'pending')
                    <div class="space-y-4">
                        <button wire:click="approve" class="w-full bg-[#006B3F] hover:bg-[#005a35] text-white font-bold py-2 px-4 rounded">
                            Approve KYC
                        </button>
                        
                        <div class="border-t pt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason (Required to reject)</label>
                            <textarea wire:model="rejectionReason" class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500" rows="3" placeholder="e.g. ID is blurry, KRA PIN does not match name..."></textarea>
                            @error('rejectionReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            
                            <button wire:click="reject" class="mt-2 w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Reject KYC
                            </button>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded 
                        {{ $submission->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        This submission was <strong>{{ ucfirst($submission->status) }}</strong>.
                        @if($submission->status === 'rejected')
                            <p class="mt-2 text-sm">Reason: {{ $submission->rejection_reason }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
