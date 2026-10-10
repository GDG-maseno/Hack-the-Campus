<?php

namespace App\Data;

class ServiceDirectory
{
    /**
     * Returns a static dataset of all Kaazilink service categories.
     * This mimics the structure of a future `service_categories` table.
     *
     * @return array
     */
    public static function getCategories(): array
    {
        return [
            [
                'id' => 1,
                'slug' => 'home-care',
                'name' => 'Home Care',
                'description' => 'Reliable household support for cleaning, caregiving, and maintenance.',
                'icon' => 'home', // placeholder for UI icon class
            ],
            [
                'id' => 2,
                'slug' => 'fundi-technical-repairs',
                'name' => 'Fundi & Technical Repairs',
                'description' => 'Skilled tradesmen for construction, repairs, and installations.',
                'icon' => 'wrench',
            ],
            [
                'id' => 3,
                'slug' => 'daily-needs-emergency-response',
                'name' => 'Daily Needs & Emergency Response',
                'description' => 'Fast delivery and urgent assistance when you need it most.',
                'icon' => 'truck',
            ],
            [
                'id' => 4,
                'slug' => 'beauty-events-social-support',
                'name' => 'Beauty / Events / Social Support',
                'description' => 'Professionals to make you look good and make your events memorable.',
                'icon' => 'sparkles',
            ],
            [
                'id' => 5,
                'slug' => 'agriculture-rural-farming',
                'name' => 'Agriculture / Rural / Farming',
                'description' => 'Expert hands and equipment for your shamba and livestock.',
                'icon' => 'leaf',
            ],
            [
                'id' => 6,
                'slug' => 'business-cyber-education-health',
                'name' => 'Business / Cyber / Education / Health',
                'description' => 'Professional services for your business, learning, and well-being.',
                'icon' => 'briefcase',
            ],
        ];
    }

    /**
     * Returns a static dataset of all 51 Kaazilink services.
     * This mimics the structure of a future `services` table.
     *
     * @return array
     */
    public static function getServices(): array
    {
        return [
            // Category 1: Home Care
            ['id' => 1, 'category_id' => 1, 'slug' => 'mama-fua-laundry', 'name' => 'Mama Fua / Laundry', 'price_range' => 'KES 500 - 1,500 per day'],
            ['id' => 2, 'category_id' => 1, 'slug' => 'house-help-day-scholar', 'name' => 'House Help (Day Scholar)', 'price_range' => 'KES 500 - 1,200 per day'],
            ['id' => 3, 'category_id' => 1, 'slug' => 'nanny-babysitter', 'name' => 'Nanny / Babysitter', 'price_range' => 'KES 800 - 2,000 per day'],
            ['id' => 4, 'category_id' => 1, 'slug' => 'cook-private-chef', 'name' => 'Cook / Private Chef', 'price_range' => 'KES 1,500 - 5,000 per event'],
            ['id' => 5, 'category_id' => 1, 'slug' => 'elderly-caregiver', 'name' => 'Elderly Caregiver', 'price_range' => 'KES 1,000 - 3,000 per shift'],
            ['id' => 6, 'category_id' => 1, 'slug' => 'deep-cleaning-services', 'name' => 'Deep Cleaning Services', 'price_range' => 'KES 3,000 - 10,000 per job'],
            ['id' => 7, 'category_id' => 1, 'slug' => 'exterminator-pest-control', 'name' => 'Exterminator / Pest Control', 'price_range' => 'KES 2,000 - 6,000 per job'],
            ['id' => 8, 'category_id' => 1, 'slug' => 'carpet-sofa-cleaning', 'name' => 'Carpet & Sofa Cleaning', 'price_range' => 'KES 1,500 - 5,000 per job'],
            ['id' => 9, 'category_id' => 1, 'slug' => 'pool-maintenance', 'name' => 'Pool Maintenance', 'price_range' => 'KES 2,500 - 8,000 per visit'],
            ['id' => 10, 'category_id' => 1, 'slug' => 'garbage-collection', 'name' => 'Garbage Collection', 'price_range' => 'KES 300 - 1,000 per month'],

            // Category 2: Fundi & Technical Repairs
            ['id' => 11, 'category_id' => 2, 'slug' => 'plumber', 'name' => 'Plumber', 'price_range' => 'KES 1,000 - 5,000 per job'],
            ['id' => 12, 'category_id' => 2, 'slug' => 'electrician', 'name' => 'Electrician', 'price_range' => 'KES 1,000 - 8,000 per job'],
            ['id' => 13, 'category_id' => 2, 'slug' => 'carpenter', 'name' => 'Carpenter', 'price_range' => 'KES 1,500 - 10,000 per job'],
            ['id' => 14, 'category_id' => 2, 'slug' => 'welder-metalworker', 'name' => 'Welder / Metalworker', 'price_range' => 'KES 2,000 - 15,000 per job'],
            ['id' => 15, 'category_id' => 2, 'slug' => 'mason-builder', 'name' => 'Mason / Builder', 'price_range' => 'KES 1,000 - 2,500 per day'],
            ['id' => 16, 'category_id' => 2, 'slug' => 'painter', 'name' => 'Painter', 'price_range' => 'KES 1,000 - 3,000 per day'],
            ['id' => 17, 'category_id' => 2, 'slug' => 'electronics-repair', 'name' => 'Electronics Repair (TV/Radio)', 'price_range' => 'KES 1,000 - 5,000 per job'],
            ['id' => 18, 'category_id' => 2, 'slug' => 'ac-fridge-repair', 'name' => 'AC / Fridge Repair', 'price_range' => 'KES 2,500 - 8,000 per job'],
            ['id' => 19, 'category_id' => 2, 'slug' => 'washing-machine-repair', 'name' => 'Washing Machine Repair', 'price_range' => 'KES 2,000 - 6,000 per job'],
            ['id' => 20, 'category_id' => 2, 'slug' => 'generator-mechanic', 'name' => 'Generator Mechanic', 'price_range' => 'KES 3,000 - 10,000 per job'],

            // Category 3: Daily Needs & Emergency Response
            ['id' => 21, 'category_id' => 3, 'slug' => 'gas-cylinder-delivery', 'name' => 'Gas Cylinder Delivery', 'price_range' => 'KES 1,200 - 3,500 per refill'],
            ['id' => 22, 'category_id' => 3, 'slug' => 'water-delivery', 'name' => 'Water Delivery (Bowser/Mkokoteni)', 'price_range' => 'KES 50 - 5,000 per delivery'],
            ['id' => 23, 'category_id' => 3, 'slug' => 'grocery-shopper', 'name' => 'Grocery Shopper / Errand Runner', 'price_range' => 'KES 300 - 1,500 per errand'],
            ['id' => 24, 'category_id' => 3, 'slug' => 'boda-boda-rider', 'name' => 'Boda Boda / Rider', 'price_range' => 'KES 50 - 500 per trip'],
            ['id' => 25, 'category_id' => 3, 'slug' => 'tuk-tuk-taxi', 'name' => 'Tuk Tuk / Taxi', 'price_range' => 'KES 150 - 1,000 per trip'],
            ['id' => 26, 'category_id' => 3, 'slug' => 'emergency-towing', 'name' => 'Emergency Towing', 'price_range' => 'KES 3,000 - 10,000 per tow'],
            ['id' => 27, 'category_id' => 3, 'slug' => 'locksmith', 'name' => 'Locksmith', 'price_range' => 'KES 1,500 - 5,000 per job'],
            ['id' => 28, 'category_id' => 3, 'slug' => 'ambulance-medical-transport', 'name' => 'Ambulance / Medical Transport', 'price_range' => 'KES 3,500 - 15,000 per trip'],
            ['id' => 29, 'category_id' => 3, 'slug' => 'firewood-charcoal-delivery', 'name' => 'Firewood / Charcoal Delivery', 'price_range' => 'KES 500 - 3,000 per sack'],

            // Category 4: Beauty/Events/Social Support
            ['id' => 30, 'category_id' => 4, 'slug' => 'mobile-hairdresser', 'name' => 'Mobile Hairdresser (Salon)', 'price_range' => 'KES 800 - 5,000 per session'],
            ['id' => 31, 'category_id' => 4, 'slug' => 'barber-kinyozi', 'name' => 'Barber (Kinyozi)', 'price_range' => 'KES 200 - 1,000 per cut'],
            ['id' => 32, 'category_id' => 4, 'slug' => 'makeup-artist', 'name' => 'Makeup Artist', 'price_range' => 'KES 1,500 - 10,000 per face'],
            ['id' => 33, 'category_id' => 4, 'slug' => 'henna-artist', 'name' => 'Henna Artist', 'price_range' => 'KES 500 - 3,000 per session'],
            ['id' => 34, 'category_id' => 4, 'slug' => 'event-mc-dj', 'name' => 'Event MC / DJ', 'price_range' => 'KES 5,000 - 50,000 per event'],
            ['id' => 35, 'category_id' => 4, 'slug' => 'photographer-videographer', 'name' => 'Photographer / Videographer', 'price_range' => 'KES 5,000 - 40,000 per event'],
            ['id' => 36, 'category_id' => 4, 'slug' => 'event-decorator', 'name' => 'Event Decorator', 'price_range' => 'KES 10,000 - 100,000 per event'],
            ['id' => 37, 'category_id' => 4, 'slug' => 'caterer-outside-catering', 'name' => 'Caterer / Outside Catering', 'price_range' => 'KES 500 - 2,500 per plate'],
            ['id' => 38, 'category_id' => 4, 'slug' => 'tent-chair-rental', 'name' => 'Tent & Chair Rental', 'price_range' => 'KES 1,000 - 20,000 per event'],

            // Category 5: Agriculture/Rural/Farming
            ['id' => 39, 'category_id' => 5, 'slug' => 'farm-laborer-shamba-boy', 'name' => 'Farm Laborer / Shamba Boy', 'price_range' => 'KES 300 - 1,000 per day'],
            ['id' => 40, 'category_id' => 5, 'slug' => 'tractor-hire', 'name' => 'Tractor Hire', 'price_range' => 'KES 2,500 - 5,000 per acre'],
            ['id' => 41, 'category_id' => 5, 'slug' => 'veterinary-officer', 'name' => 'Veterinary Officer / Daktari wa Mifugo', 'price_range' => 'KES 1,000 - 5,000 per visit'],
            ['id' => 42, 'category_id' => 5, 'slug' => 'agronomist', 'name' => 'Agronomist', 'price_range' => 'KES 2,000 - 10,000 per consultation'],
            ['id' => 43, 'category_id' => 5, 'slug' => 'landscaper-gardener', 'name' => 'Landscaper / Gardener', 'price_range' => 'KES 1,000 - 5,000 per job'],
            ['id' => 44, 'category_id' => 5, 'slug' => 'tree-cutter', 'name' => 'Tree Cutter', 'price_range' => 'KES 2,000 - 10,000 per tree'],
            ['id' => 45, 'category_id' => 5, 'slug' => 'poultry-expert', 'name' => 'Poultry Expert', 'price_range' => 'KES 1,000 - 4,000 per visit'],
            ['id' => 46, 'category_id' => 5, 'slug' => 'borehole-driller', 'name' => 'Borehole Driller', 'price_range' => 'KES 100,000 - 1,500,000 per project'],

            // Category 6: Business/Cyber/Education/Health
            ['id' => 47, 'category_id' => 6, 'slug' => 'graphic-designer', 'name' => 'Graphic Designer', 'price_range' => 'KES 1,000 - 10,000 per design'],
            ['id' => 48, 'category_id' => 6, 'slug' => 'kra-returns-assistant', 'name' => 'KRA Returns Assistant', 'price_range' => 'KES 500 - 3,000 per return'],
            ['id' => 49, 'category_id' => 6, 'slug' => 'typist-data-entry', 'name' => 'Typist / Data Entry', 'price_range' => 'KES 500 - 2,000 per document'],
            ['id' => 50, 'category_id' => 6, 'slug' => 'tutor', 'name' => 'Tutor (Primary/Secondary)', 'price_range' => 'KES 500 - 2,000 per hour'],
            ['id' => 51, 'category_id' => 6, 'slug' => 'business-registration-agent', 'name' => 'Business Registration Agent', 'price_range' => 'KES 2,000 - 10,000 per registration'],
        ];
    }
}
