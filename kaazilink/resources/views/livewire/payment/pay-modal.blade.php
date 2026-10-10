<div>
    <x-wirekit::modal name="payment-modal" ariaLabel="Pay with M-PESA">
        <x-wirekit::card>
            <x-wirekit::card.header>
                <div class="flex items-center justify-between">
                    <x-wirekit::heading size="lg">M-PESA Payment</x-wirekit::heading>
                    @if($booking)
                        <div class="text-right">
                            <span class="text-xs text-gray-500 uppercase tracking-wider block">Amount Due</span>
                            <span class="text-lg font-bold text-green-700">KES {{ number_format($booking->price, 2) }}</span>
                        </div>
                    @endif
                </div>
            </x-wirekit::card.header>

            <x-wirekit::card.body>

                {{-- ============================================================ --}}
                {{-- STATE: STK Push Initiated — polling for result               --}}
                {{-- ============================================================ --}}
                @if($isInitiated)
                    <div
                        wire:poll.4000ms="checkPaymentStatus"
                        class="space-y-4"
                    >
                        {{-- Current payment status --}}
                        @if($currentPayment && $currentPayment->status === 'successful')
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                                <div class="flex items-center gap-2 font-semibold text-green-900 mb-1">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Payment Confirmed!
                                </div>
                                <p>Your M-PESA payment of <strong>KES {{ number_format($booking?->price, 2) }}</strong> has been received.</p>
                            </div>
                            <div class="flex justify-end pt-2">
                                <x-wirekit::button type="button" intent="neutral" wire:click="$dispatch('wirekit-modal-close', { name: 'payment-modal' })">
                                    Close
                                </x-wirekit::button>
                            </div>

                        @elseif($currentPayment && in_array($currentPayment->status, ['cancelled', 'failed']))
                            <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
                                <span class="font-semibold block mb-1">
                                    {{ $currentPayment->status === 'cancelled' ? 'Payment Cancelled' : 'Payment Failed' }}
                                </span>
                                {{ $currentPayment->error_message ?: 'The payment was not completed. You may try again.' }}
                            </div>
                            <div class="flex justify-end gap-2 pt-2 border-t">
                                <x-wirekit::button type="button" intent="neutral" wire:click="$dispatch('wirekit-modal-close', { name: 'payment-modal' })">
                                    Close
                                </x-wirekit::button>
                                <x-wirekit::button type="button" intent="primary" wire:click="resetPayment">
                                    Try Again
                                </x-wirekit::button>
                            </div>

                        @else
                            {{-- Still waiting — spinner --}}
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 text-sm">
                                <div class="flex items-center gap-2 font-semibold text-blue-900 mb-1">
                                    <svg class="w-5 h-5 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                    </svg>
                                    Waiting for your M-PESA PIN…
                                </div>
                                <p>A payment prompt has been sent to <strong>{{ $phone }}</strong>.</p>
                                <p class="mt-1 text-blue-700">Enter your M-PESA PIN on your phone to pay <strong>KES {{ number_format($booking?->price, 2) }}</strong>.</p>
                                <p class="mt-2 text-xs text-blue-500">This page will update automatically when your response is received.</p>
                            </div>
                            <div class="flex justify-end pt-2 border-t">
                                <x-wirekit::button type="button" intent="neutral" wire:click="$dispatch('wirekit-modal-close', { name: 'payment-modal' })">
                                    Close
                                </x-wirekit::button>
                            </div>
                        @endif
                    </div>

                {{-- ============================================================ --}}
                {{-- STATE: Input form — before STK is sent                        --}}
                {{-- ============================================================ --}}
                @elseif($currentPayment && $currentPayment->status === 'successful')
                    {{-- ============================================================ --}}
                    {{-- STATE: Payment completed successfully                        --}}
                    {{-- ============================================================ --}}
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                        <div class="flex items-center gap-2 font-semibold text-green-900 mb-1">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Payment Confirmed!
                        </div>
                        <p>Your M-PESA payment of <strong>KES {{ number_format($booking?->price, 2) }}</strong> has been received.</p>
                        <p class="mt-1 text-green-700 text-xs">Your booking is now confirmed. You can track it in My Bookings.</p>
                    </div>
                    <div class="flex justify-end pt-2">
                        <x-wirekit::button type="button" intent="primary" wire:click="$dispatch('wirekit-modal-close', { name: 'payment-modal' })">
                            Done
                        </x-wirekit::button>
                    </div>

                @elseif($currentPayment && in_array($currentPayment->status, ['cancelled', 'failed']))
                    {{-- ============================================================ --}}
                    {{-- STATE: Payment failed or cancelled — offer retry             --}}
                    {{-- ============================================================ --}}
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
                        <span class="font-semibold block mb-1">
                            {{ $currentPayment->status === 'cancelled' ? 'Payment Cancelled' : 'Payment Failed' }}
                        </span>
                        {{ $currentPayment->error_message ?: 'The payment was not completed. You may try again.' }}
                    </div>
                    <div class="flex justify-end gap-2 pt-2 border-t">
                        <x-wirekit::button type="button" intent="neutral" wire:click="$dispatch('wirekit-modal-close', { name: 'payment-modal' })">
                            Close
                        </x-wirekit::button>
                        <x-wirekit::button type="button" intent="primary" wire:click="resetPayment">
                            Try Again
                        </x-wirekit::button>
                    </div>

                @else
                    {{-- ============================================================ --}}
                    {{-- STATE: Input form — before STK is sent                       --}}
                    {{-- ============================================================ --}}
                    @if($errorMessage)
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm mb-4">
                            <span class="font-semibold block mb-1">Payment Failed:</span>
                            {{ $errorMessage }}
                        </div>
                    @endif

                    <x-wirekit::form wire:submit="initiatePayment" class="space-y-4">
                        <div>
                            <x-wirekit::label for="payment-phone">M-PESA Phone Number</x-wirekit::label>
                            <x-wirekit::input
                                id="payment-phone"
                                wire:model="phone"
                                type="tel"
                                placeholder="0712345678 or 254712345678"
                                required
                            />
                            <p class="text-xs text-gray-500 mt-1">The Safaricom number that will receive the payment prompt.</p>
                            @error('phone') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end gap-2 pt-4 border-t">
                            <x-wirekit::button type="button" intent="neutral" wire:click="$dispatch('wirekit-modal-close', { name: 'payment-modal' })">
                                Cancel
                            </x-wirekit::button>
                            <x-wirekit::button type="submit" intent="primary">
                                Pay KES {{ number_format($booking?->price, 2) }}
                            </x-wirekit::button>
                        </div>
                    </x-wirekit::form>
                @endif

            </x-wirekit::card.body>
        </x-wirekit::card>
    </x-wirekit::modal>
</div>
