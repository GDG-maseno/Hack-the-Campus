<div>
    <x-wirekit::card class="max-w-xl mx-auto mt-8">
        <x-wirekit::card.header>
            <x-wirekit::heading size="lg">Services</x-wirekit::heading>
            <x-wirekit::text variant="muted">Select the services you offer.</x-wirekit::text>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <x-wirekit::form wire:submit="addService" class="space-y-4">
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <x-wirekit::combobox
                            name="selectedServiceSlug"
                            label="Add a service"
                            placeholder="Search services..."
                            wire:model="selectedServiceSlug"
                            :options="$availableServicesOptions"
                        />
                    </div>
                    <x-wirekit::button type="submit" intent="primary">Add</x-wirekit::button>
                </div>
            </x-wirekit::form>

            <div class="mt-6">
                <h4 class="font-medium text-sm text-gray-700 mb-2">Your Current Services</h4>
                @if($providerServices->isEmpty())
                    <x-wirekit::empty-state 
                        title="No services selected" 
                        description="Add services above to start appearing in local search results."
                        icon="briefcase"
                    />
                @else
                    <x-wirekit::data-list>
                        @foreach($providerServices as $service)
                            <x-wirekit::data-list.item>
                                <x-slot:label>{{ $service['name'] }}</x-slot:label>
                                <x-slot:value>
                                    <button wire:click="removeService('{{ $service['slug'] }}')" class="text-red-500 text-sm font-medium hover:underline">
                                        Remove
                                    </button>
                                </x-slot:value>
                            </x-wirekit::data-list.item>
                        @endforeach
                    </x-wirekit::data-list>
                @endif
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>
</div>
