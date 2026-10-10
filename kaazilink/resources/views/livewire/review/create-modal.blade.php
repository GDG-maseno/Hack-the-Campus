<div>
    <x-wirekit::modal name="review-create-modal" ariaLabel="Leave a Review">
        <x-wirekit::card wire:id="{{ $this->getId() }}">
            <x-wirekit::card.header>
                <x-wirekit::heading size="lg">Leave a Review</x-wirekit::heading>
                <x-wirekit::text variant="muted">Rate your experience with {{ $booking ? $booking->provider->name : 'the provider' }}.</x-wirekit::text>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                @if(session()->has('error'))
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif
                <form 
                    x-data="{
                        rating: window.Livewire.find('{{ $this->getId() }}').entangle('rating').live,
                        comment: window.Livewire.find('{{ $this->getId() }}').entangle('comment').live,
                        submit() {
                            window.Livewire.find('{{ $this->getId() }}').submit();
                        }
                    }"
                    @submit.prevent="submit()" 
                    class="space-y-4"
                >
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                        <select x-model="rating" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Good</option>
                            <option value="3">3 - Average</option>
                            <option value="2">2 - Poor</option>
                            <option value="1">1 - Terrible</option>
                        </select>
                        @error('rating') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Comment (Optional)</label>
                        <textarea 
                            x-model="comment" 
                            rows="4" 
                            placeholder="Share details of your own experience..."
                            class="w-full px-3 py-2 text-sm border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white border-gray-300"
                        ></textarea>
                        @error('comment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <x-wirekit::button type="button" intent="neutral" @click="$dispatch('wirekit-modal-close', { name: 'review-create-modal' })">Cancel</x-wirekit::button>
                        <x-wirekit::button type="submit" intent="primary" @click.prevent="submit()">Submit Review</x-wirekit::button>
                    </div>
                </form>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </x-wirekit::modal>
</div>
