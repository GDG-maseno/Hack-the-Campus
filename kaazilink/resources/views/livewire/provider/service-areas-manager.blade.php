<div>
    <x-wirekit::card class="max-w-xl mx-auto mt-8">
        <x-wirekit::card.header>
            <x-wirekit::heading size="lg">Service Areas</x-wirekit::heading>
            <x-wirekit::text variant="muted">Select the areas in Nairobi where you offer your services.</x-wirekit::text>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <x-wirekit::form wire:submit="addArea" class="space-y-4">
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <x-wirekit::combobox
                            name="selectedAreaId"
                            label="Add an area"
                            placeholder="Search areas..."
                            wire:model="selectedAreaId"
                            :options="$availableAreasOptions"
                        />
                    </div>
                    <x-wirekit::button type="submit" intent="primary">Add</x-wirekit::button>
                </div>
            </x-wirekit::form>

            <div class="mt-6">
                <h4 class="font-medium text-sm text-gray-700 mb-2">Your Current Areas</h4>
                @if($providerAreas->isEmpty())
                    <x-wirekit::empty-state 
                        title="No areas selected" 
                        description="Add areas above to start appearing in local search results."
                        icon="map-pin"
                    />
                @else
                    <x-wirekit::data-list>
                        @foreach($providerAreas as $area)
                            <x-wirekit::data-list.item>
                                <x-slot:label>{{ $area->name }}</x-slot:label>
                                <x-slot:value>
                                    <button wire:click="removeArea({{ $area->id }})" class="text-red-500 text-sm font-medium hover:underline">
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
