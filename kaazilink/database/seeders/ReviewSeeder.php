<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Booking;
use App\Models\Review;
use App\Models\ProviderService;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $client = User::where('email', 'client@kz.com')->first();
        if (!$client) {
            return;
        }

        $providers = User::where('role', 'provider')->get();

        $reviewTemplates = [
            'electrical' => [
                ['rating' => 5, 'comment' => 'Arrived on time and diagnosed the wiring fault within 20 minutes. Very professional and polite!'],
                ['rating' => 5, 'comment' => 'Excellent work on the solar inverter installation. Everything works cleanly and neat cabling.'],
                ['rating' => 4, 'comment' => 'Replaced all burnt circuit breakers quickly. Honest pricing and good advice on surge protectors.'],
                ['rating' => 5, 'comment' => 'Fixed our security lights and outdoor sockets. Highly recommended electrician for residential work.'],
            ],
            'plumbing' => [
                ['rating' => 5, 'comment' => 'Unblocked our main drainage line in less than an hour! Cleaned up the site properly before leaving.'],
                ['rating' => 5, 'comment' => 'Installed our solar water heater effortlessly. No leaks and hot water pressure is great!'],
                ['rating' => 4, 'comment' => 'Repaired the leaking sink mixer tap. Good work quality and friendly customer service.'],
                ['rating' => 5, 'comment' => 'Prompt emergency response for a burst pipe at 7 PM. Saved us from serious water damage!'],
            ],
            'carpentry' => [
                ['rating' => 5, 'comment' => 'The kitchen cabinet fitting surpassed our expectations. Precise alignment and sturdy finishing!'],
                ['rating' => 4, 'comment' => 'Assembled our complex flat-pack wardrobe seamlessly. Clean craftsmanship.'],
                ['rating' => 5, 'comment' => 'Repaired three sticky wooden doors and replaced broken locks. Very skilled woodworker.'],
                ['rating' => 5, 'comment' => 'Built a custom wooden dining table for our patio. Exceptional grain finish and solid construction.'],
            ],
            'painting' => [
                ['rating' => 5, 'comment' => 'Transformed our 3-bedroom living room with textured paint. Flawless coverage and zero mess!'],
                ['rating' => 5, 'comment' => 'Applied damp proofing primer and repainted our exterior balcony. Looks brand new!'],
                ['rating' => 4, 'comment' => 'Neat wallpaper installation in the nursery. Courteous team and finished on schedule.'],
                ['rating' => 5, 'comment' => 'Great attention to detail on the ceiling crown moldings. Will definitely hire again.'],
            ],
            'masonry' => [
                ['rating' => 5, 'comment' => 'Laid porcelain tiles in our living room with perfect levelling and tight grout lines. Outstanding job!'],
                ['rating' => 5, 'comment' => 'Constructed a strong 50-meter perimeter block wall. Solid foundation and clean mortar work.'],
                ['rating' => 4, 'comment' => 'Granite countertop installation in the kitchen was done with high precision.'],
                ['rating' => 5, 'comment' => 'Smooth wall plastering and screeding ahead of painting. High quality masonry.'],
            ],
            'appliance-repair' => [
                ['rating' => 5, 'comment' => 'Fixed our washing machine drain pump issue on the first visit. Saved us buying a new machine!'],
                ['rating' => 5, 'comment' => 'Re-gassed and serviced our double-door Samsung fridge. Freezing perfectly now.'],
                ['rating' => 4, 'comment' => 'Mounted our 65-inch Smart TV securely on the concrete wall with hidden wire trunking.'],
                ['rating' => 5, 'comment' => 'Quick diagnosis and heating element replacement for our microwave oven. Very fair charges.'],
            ],
        ];

        foreach ($providers as $provider) {
            // Pick a non-test provider service
            $providerService = ProviderService::where('user_id', $provider->id)
                ->where('is_custom', false)
                ->first();

            $serviceSlug = $providerService ? $providerService->service_slug : 'house-rewiring';
            $price = $providerService ? $providerService->price : 2500;

            // Guess category from first service slug or default to electrical
            $catKey = 'electrical';
            if (str_contains($serviceSlug, 'leak') || str_contains($serviceSlug, 'drain') || str_contains($serviceSlug, 'water') || str_contains($serviceSlug, 'tap') || str_contains($serviceSlug, 'pipe')) {
                $catKey = 'plumbing';
            } elseif (str_contains($serviceSlug, 'cabinet') || str_contains($serviceSlug, 'wardrobe') || str_contains($serviceSlug, 'door') || str_contains($serviceSlug, 'furniture') || str_contains($serviceSlug, 'truss') || str_contains($serviceSlug, 'flooring')) {
                $catKey = 'carpentry';
            } elseif (str_contains($serviceSlug, 'painting') || str_contains($serviceSlug, 'finish') || str_contains($serviceSlug, 'coating') || str_contains($serviceSlug, 'varnish')) {
                $catKey = 'painting';
            } elseif (str_contains($serviceSlug, 'tiling') || str_contains($serviceSlug, 'wall') || str_contains($serviceSlug, 'plastering') || str_contains($serviceSlug, 'paving') || str_contains($serviceSlug, 'slab') || str_contains($serviceSlug, 'cladding') || str_contains($serviceSlug, 'terrazzo')) {
                $catKey = 'masonry';
            } elseif (str_contains($serviceSlug, 'repair') || str_contains($serviceSlug, 'tv') || str_contains($serviceSlug, 'machine') || str_contains($serviceSlug, 'conditioner') || str_contains($serviceSlug, 'dispenser')) {
                $catKey = 'appliance-repair';
            }

            $templates = $reviewTemplates[$catKey];
            $selectedTemplates = array_slice($templates, 0, 3);

            foreach ($selectedTemplates as $index => $t) {
                // Create completed ghost booking
                $booking = Booking::create([
                    'client_id' => $client->id,
                    'provider_id' => $provider->id,
                    'service_slug' => $serviceSlug,
                    'price' => $price,
                    'scheduled_at' => now()->subDays(rand(5, 60)),
                    'status' => 'completed',
                ]);

                // Create Review
                Review::create([
                    'booking_id' => $booking->id,
                    'client_id' => $client->id,
                    'provider_id' => $provider->id,
                    'rating' => $t['rating'],
                    'comment' => $t['comment'],
                    'created_at' => now()->subDays(rand(1, 40)),
                ]);
            }
        }
    }
}
