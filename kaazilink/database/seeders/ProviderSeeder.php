<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProviderService;
use App\Models\ServiceArea;
use App\Models\KycSubmission;
use App\Data\ServiceDirectory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Account
        User::firstOrCreate(
            ['email' => 'admin@kz.com'],
            [
                'name' => 'KaaziLink Admin',
                'phone' => '254700000000',
                'password' => Hash::make('1234'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Test Client Account
        User::firstOrCreate(
            ['email' => 'client@kz.com'],
            [
                'name' => 'Main Test Client',
                'phone' => '254711000000',
                'password' => Hash::make('1234'),
                'role' => 'client',
                'email_verified_at' => now(),
            ]
        );

        // Fetch area IDs by name
        $getAreaIds = function (array $names) {
            return ServiceArea::whereIn('name', $names)->pluck('id')->toArray();
        };

        $providers = [
            // --- ELECTRICIANS ---
            [
                'email' => 'eth@kz.com',
                'name' => 'Ethan Njuguna',
                'phone' => '254712345601',
                'category' => 'electrical',
                'bio' => 'EPRA certified residential & commercial electrician with 8+ years experience in domestic wiring, solar inverter setup, and fault diagnosis across Westlands & Kilimani.',
                'areas' => $getAreaIds(['Westlands', 'Kilimani', 'Lavington', 'Kileleshwa']),
                'services' => [
                    ['slug' => 'electrician', 'name' => 'Electrical Wiring & Circuit Installation', 'price' => 3500],
                    ['slug' => 'generator-mechanic', 'name' => 'Generator Maintenance & Backup Setup', 'price' => 4500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Electrical', 'price' => 1],
                ],
            ],
            [
                'email' => 'sam@kz.com',
                'name' => 'Samuel Waweru',
                'phone' => '254712345602',
                'category' => 'electrical',
                'bio' => 'Specialist in solar power system installation, backup inverter configurations, generator maintenance, and industrial electrical panel fitting.',
                'areas' => $getAreaIds(['Karen', 'Runda', 'Muthaiga', 'Gigiri']),
                'services' => [
                    ['slug' => 'electrician', 'name' => 'Solar Power & Inverter Installation', 'price' => 25000],
                    ['slug' => 'generator-mechanic', 'name' => 'Generator Maintenance', 'price' => 5000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Electrical', 'price' => 1],
                ],
            ],
            [
                'email' => 'vic@kz.com',
                'name' => 'Victor Ochieng',
                'phone' => '254712345603',
                'category' => 'electrical',
                'bio' => 'Experienced electrician focused on smart home automation, security lighting installation, and heavy appliance connections.',
                'areas' => $getAreaIds(['Nyali', 'Bamburi', 'Kisauni', 'Mvita']),
                'services' => [
                    ['slug' => 'electrician', 'name' => 'Security Lighting & Domestic Wiring', 'price' => 3000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Electrical', 'price' => 1],
                ],
            ],

            // --- PLUMBERS ---
            [
                'email' => 'jam@kz.com',
                'name' => 'James Kimani',
                'phone' => '254712345604',
                'category' => 'plumbing',
                'bio' => 'Master plumber with 10 years experience solving water pressure issues, solar water heater installations, pipe leak repairs, and full bathroom fitting.',
                'areas' => $getAreaIds(['South B', 'South C', 'Langata', 'Madaraka']),
                'services' => [
                    ['slug' => 'plumber', 'name' => 'Plumbing Repair & Leak Fixing', 'price' => 2500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Plumbing', 'price' => 1],
                ],
            ],
            [
                'email' => 'dan@kz.com',
                'name' => 'Daniel Kiprop',
                'phone' => '254712345605',
                'category' => 'plumbing',
                'bio' => 'Plumbing specialist skilled in water tank fitting, booster pump repair, sewer line clearing, and emergency leak stopping.',
                'areas' => $getAreaIds(['Eldoret CBD', 'Huruma (Eldoret)', 'Langas', 'Kapseret']),
                'services' => [
                    ['slug' => 'plumber', 'name' => 'Water Tank & Pipe Installation', 'price' => 4500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Plumbing', 'price' => 1],
                ],
            ],
            [
                'email' => 'kev@kz.com',
                'name' => 'Kevin Mutua',
                'phone' => '254712345606',
                'category' => 'plumbing',
                'bio' => 'Reliable plumber providing clean tap installations, shower mixer fittings, underground pipe repairs, and septic tank drainage services.',
                'areas' => $getAreaIds(['Milimani (Nakuru)', 'Nakuru CBD', 'Kaptembwa', 'Free Area']),
                'services' => [
                    ['slug' => 'plumber', 'name' => 'Bathroom & Kitchen Plumbing Services', 'price' => 2000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Plumbing', 'price' => 1],
                ],
            ],

            // --- CARPENTERS ---
            [
                'email' => 'joh@kz.com',
                'name' => 'John Maina',
                'phone' => '254712345607',
                'category' => 'carpentry',
                'bio' => 'Custom woodworking craftsman specializing in modern MDF/hardwood kitchen cabinets, custom wardrobes, door repairs, and furniture assembly.',
                'areas' => $getAreaIds(['Ruaka', 'Thindigua', 'Kiambu Town', 'Kikuyu Town']),
                'services' => [
                    ['slug' => 'carpenter', 'name' => 'Custom Kitchen Cabinets & Wardrobes', 'price' => 15000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Carpentry', 'price' => 1],
                ],
            ],
            [
                'email' => 'pet@kz.com',
                'name' => 'Peter Kamau',
                'phone' => '254712345608',
                'category' => 'carpentry',
                'bio' => 'Experienced timber artisan proficient in roof truss construction, wooden flooring, pergolas, and custom dining table sets.',
                'areas' => $getAreaIds(['Milimani (Kisumu)', 'Kisumu CBD', 'Nyalenda', 'Kondele']),
                'services' => [
                    ['slug' => 'carpenter', 'name' => 'Roof Truss & Custom Wood Furniture', 'price' => 12000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Carpentry', 'price' => 1],
                ],
            ],
            [
                'email' => 'dav@kz.com',
                'name' => 'David Omwenga',
                'phone' => '254712345609',
                'category' => 'carpentry',
                'bio' => 'Interior carpentry specialist doing acoustic ceiling panels, gyproc partitioning frames, office desk assembly, and door lock replacements.',
                'areas' => $getAreaIds(['Roysambu', 'Kasarani', 'Kahawa West', 'Zimmerman']),
                'services' => [
                    ['slug' => 'carpenter', 'name' => 'Door Locks, Gyproc & Office Furniture', 'price' => 3500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Carpentry', 'price' => 1],
                ],
            ],

            // --- PAINTERS ---
            [
                'email' => 'bri@kz.com',
                'name' => 'Brian Mwangi',
                'phone' => '254712345610',
                'category' => 'painting',
                'bio' => 'Professional interior & exterior decorator skilled in silk finishes, textured wall designs, water-proofing primer application, and wallpaper installation.',
                'areas' => $getAreaIds(['Parklands', 'Westlands', 'Lavington', 'Kilimani']),
                'services' => [
                    ['slug' => 'painter', 'name' => 'Interior & Exterior House Painting', 'price' => 8000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Painting', 'price' => 1],
                ],
            ],
            [
                'email' => 'cha@kz.com',
                'name' => 'Charles Otieno',
                'phone' => '254712345611',
                'category' => 'painting',
                'bio' => 'Expert house painter specializing in damp proofing treatments, epoxy floor coatings, timber varnish application, and roof painting.',
                'areas' => $getAreaIds(['Nyali', 'Bamburi', 'Shanzu', 'Mvita']),
                'services' => [
                    ['slug' => 'painter', 'name' => 'Epoxy Coating & Damp Proof Painting', 'price' => 9000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Painting', 'price' => 1],
                ],
            ],
            [
                'email' => 'geo@kz.com',
                'name' => 'George Njoroge',
                'phone' => '254712345612',
                'category' => 'painting',
                'bio' => 'Detail-oriented painter delivering flawless residential repaints, crown molding painting, gate painting, and budget-friendly apartment touch-ups.',
                'areas' => $getAreaIds(['Ruaka', 'Kiamumbi', 'Thika Town', 'Juja']),
                'services' => [
                    ['slug' => 'painter', 'name' => 'Residential & Metal Gate Painting', 'price' => 4500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Painting', 'price' => 1],
                ],
            ],

            // --- MASONRY ---
            [
                'email' => 'jos@kz.com',
                'name' => 'Joseph Kariuki',
                'phone' => '254712345613',
                'category' => 'masonry',
                'bio' => 'Seasoned mason with 12+ years expertise in ceramic & porcelain floor tiling, stone pitching, boundary wall construction, and plastering.',
                'areas' => $getAreaIds(['Karen', 'Runda', 'Lavington', 'Kitisuru']),
                'services' => [
                    ['slug' => 'mason-builder', 'name' => 'Floor Tiling & Boundary Wall Masonry', 'price' => 12000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Masonry', 'price' => 1],
                ],
            ],
            [
                'email' => 'pat@kz.com',
                'name' => 'Patrick Mutiso',
                'phone' => '254712345614',
                'category' => 'masonry',
                'bio' => 'Masonry contractor skilled in granite countertop fitting, concrete foundation slab pouring, paving block installation, and structural repairs.',
                'areas' => $getAreaIds(['South B', 'South C', 'Syokimau', 'Kitengela']),
                'services' => [
                    ['slug' => 'mason-builder', 'name' => 'Concrete Slab & Granite Fitting', 'price' => 15000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Masonry', 'price' => 1],
                ],
            ],
            [
                'email' => 'emm@kz.com',
                'name' => 'Emmanuel Kibet',
                'phone' => '254712345615',
                'category' => 'masonry',
                'bio' => 'Mason specializing in stone cladding, chimney construction, terrazzo polishing, and foundation underpinning.',
                'areas' => $getAreaIds(['Eldoret CBD', 'Elgon View (Eldoret)', 'Kapseret', 'Huruma (Eldoret)']),
                'services' => [
                    ['slug' => 'mason-builder', 'name' => 'Stone Cladding & Wall Plastering', 'price' => 8500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Masonry', 'price' => 1],
                ],
            ],

            // --- APPLIANCE REPAIR ---
            [
                'email' => 'den@kz.com',
                'name' => 'Dennis Odhiambo',
                'phone' => '254712345616',
                'category' => 'appliance-repair',
                'bio' => 'Specialized technician fixing automatic washing machines, domestic refrigerators, microwave ovens, and electric cookers across Nairobi estates.',
                'areas' => $getAreaIds(['Kilimani', 'Westlands', 'Kileleshwa', 'South C']),
                'services' => [
                    ['slug' => 'washing-machine-repair', 'name' => 'Automatic Washing Machine Repair', 'price' => 3500],
                    ['slug' => 'ac-fridge-repair', 'name' => 'Refrigerator Repair & Gas Refill', 'price' => 4500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Appliance Repair', 'price' => 1],
                ],
            ],
            [
                'email' => 'ste@kz.com',
                'name' => 'Stephen Mboya',
                'phone' => '254712345617',
                'category' => 'appliance-repair',
                'bio' => 'HVAC & refrigeration specialist handling inverter AC unit installation, commercial fridge servicing, cold room maintenance, and water dispenser fixes.',
                'areas' => $getAreaIds(['Nyali', 'Mvita', 'Bamburi', 'Kisauni']),
                'services' => [
                    ['slug' => 'ac-fridge-repair', 'name' => 'AC Unit & Refrigerator Servicing', 'price' => 6000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Appliance Repair', 'price' => 1],
                ],
            ],
            [
                'email' => 'fra@kz.com',
                'name' => 'Francis Wambua',
                'phone' => '254712345618',
                'category' => 'appliance-repair',
                'bio' => 'Electronics & home appliance expert fixing LED/Smart TVs, audio home theater systems, dishwasher units, and automatic washing machines.',
                'areas' => $getAreaIds(['Milimani (Nakuru)', 'Nakuru CBD', 'Free Area', 'Kaptembwa']),
                'services' => [
                    ['slug' => 'electronics-repair', 'name' => 'TV Mounting & Electronics Repair', 'price' => 2500],
                    ['slug' => 'washing-machine-repair', 'name' => 'Washing Machine Repair', 'price' => 3200],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Appliance Repair', 'price' => 1],
                ],
            ],

            // --- HOME CARE (5 PROVIDERS) ---
            [
                'email' => 'mer@kz.com',
                'name' => 'Mercy Wanjiru',
                'phone' => '254712345619',
                'category' => 'home-care',
                'bio' => 'Experienced house help & professional nanny with CPR certification and 6 years experience in infant care, housekeeping, and meal preparation.',
                'areas' => $getAreaIds(['Kilimani', 'Lavington', 'Kileleshwa', 'Westlands']),
                'services' => [
                    ['slug' => 'house-help-day-scholar', 'name' => 'House Help (Day Scholar)', 'price' => 1000],
                    ['slug' => 'nanny-babysitter', 'name' => 'Nanny & Babysitting Services', 'price' => 1500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Home Care', 'price' => 1],
                ],
            ],
            [
                'email' => 'fai@kz.com',
                'name' => 'Faith Chebet',
                'phone' => '254712345620',
                'category' => 'home-care',
                'bio' => 'Deep cleaning specialist providing upholstery extraction, carpet steaming, end-of-tenancy deep cleaning, and post-construction sanitization.',
                'areas' => $getAreaIds(['Eldoret CBD', 'Elgon View (Eldoret)', 'Langas']),
                'services' => [
                    ['slug' => 'deep-cleaning-services', 'name' => 'Residential Deep Cleaning', 'price' => 5000],
                    ['slug' => 'carpet-sofa-cleaning', 'name' => 'Carpet & Sofa Steam Cleaning', 'price' => 2500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Home Care', 'price' => 1],
                ],
            ],
            [
                'email' => 'mar@kz.com',
                'name' => 'Mary Achieng',
                'phone' => '254712345621',
                'category' => 'home-care',
                'bio' => 'Reliable Mama Fua providing meticulous hand washing, iron pressing, curtain washing, and wardrobe organization across Nairobi estates.',
                'areas' => $getAreaIds(['South B', 'South C', 'Madaraka']),
                'services' => [
                    ['slug' => 'mama-fua-laundry', 'name' => 'Mama Fua & Clothes Ironing', 'price' => 1000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Home Care', 'price' => 1],
                ],
            ],
            [
                'email' => 'ant@kz.com',
                'name' => 'Anthony Kamau',
                'phone' => '254712345622',
                'category' => 'home-care',
                'bio' => 'Private chef skilled in Swahili, Continental, and traditional Kenyan cuisine for family gatherings, meal prep, and private events.',
                'areas' => $getAreaIds(['Nyali', 'Bamburi', 'Shanzu']),
                'services' => [
                    ['slug' => 'cook-private-chef', 'name' => 'Private Chef & Catering Services', 'price' => 3500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Home Care', 'price' => 1],
                ],
            ],
            [
                'email' => 'gra@kz.com',
                'name' => 'Grace Muthoni',
                'phone' => '254712345623',
                'category' => 'home-care',
                'bio' => 'Compassionate senior caregiver providing elder assistance, mobility support, medication management, and daily companionship.',
                'areas' => $getAreaIds(['Karen', 'Runda', 'Muthaiga']),
                'services' => [
                    ['slug' => 'elderly-caregiver', 'name' => 'Elderly Caregiver & Nursing Assistance', 'price' => 2000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Home Care', 'price' => 1],
                ],
            ],

            // --- DAILY NEEDS & EMERGENCY (4 PROVIDERS) ---
            [
                'email' => 'bon@kz.com',
                'name' => 'Boniface Kiprono',
                'phone' => '254712345624',
                'category' => 'daily-needs-emergency-response',
                'bio' => 'Swift Boda Boda rider and errand runner handling parcel deliveries, document dispatch, and supermarket grocery shopping.',
                'areas' => $getAreaIds(['Roysambu', 'Kasarani', 'Kahawa West']),
                'services' => [
                    ['slug' => 'boda-boda-rider', 'name' => 'Express Boda Boda Delivery', 'price' => 300],
                    ['slug' => 'grocery-shopper', 'name' => 'Errand Runner & Grocery Shopper', 'price' => 500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Daily Needs', 'price' => 1],
                ],
            ],
            [
                'email' => 'has@kz.com',
                'name' => 'Hassan Ali',
                'phone' => '254712345625',
                'category' => 'daily-needs-emergency-response',
                'bio' => 'Licensed Tuk Tuk transport provider offering affordable local luggage transport, passenger trips, and market deliveries.',
                'areas' => $getAreaIds(['Mvita', 'Kisauni', 'Nyali']),
                'services' => [
                    ['slug' => 'tuk-tuk-taxi', 'name' => 'Tuk Tuk Taxi & Cargo Transport', 'price' => 400],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Daily Needs', 'price' => 1],
                ],
            ],
            [
                'email' => 'job@kz.com',
                'name' => 'Job Wanyama',
                'phone' => '254712345626',
                'category' => 'daily-needs-emergency-response',
                'bio' => 'Clean water delivery provider supplying fresh bowser water, bottled drinking water, and tank refill services for domestic use.',
                'areas' => $getAreaIds(['Pipeline', 'Umoja', 'Donholm']),
                'services' => [
                    ['slug' => 'water-delivery', 'name' => 'Clean Water Tanker Delivery', 'price' => 2500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Daily Needs', 'price' => 1],
                ],
            ],
            [
                'email' => 'pau@kz.com',
                'name' => 'Paul Nderitu',
                'phone' => '254712345627',
                'category' => 'daily-needs-emergency-response',
                'bio' => 'Emergency LPG cooking gas cylinder delivery technician (6kg & 13kg refills) with safety valve testing and regulator setup.',
                'areas' => $getAreaIds(['Ruaka', 'Thindigua', 'Kiambu Town']),
                'services' => [
                    ['slug' => 'gas-cylinder-delivery', 'name' => 'LPG Gas Cylinder Refill & Delivery', 'price' => 1500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Daily Needs', 'price' => 1],
                ],
            ],

            // --- BEAUTY & EVENTS (4 PROVIDERS) ---
            [
                'email' => 'bre@kz.com',
                'name' => 'Brenda Cherono',
                'phone' => '254712345628',
                'category' => 'beauty-events-social-support',
                'bio' => 'Mobile salonist & hair stylist specializing in knotless braids, faux locs, dreadlocks installation, and bridal hair styling.',
                'areas' => $getAreaIds(['Kilimani', 'Kileleshwa', 'Westlands']),
                'services' => [
                    ['slug' => 'mobile-hairdresser', 'name' => 'Mobile Hair Styling & Braiding', 'price' => 2500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Beauty & Events', 'price' => 1],
                ],
            ],
            [
                'email' => 'kevo@kz.com',
                'name' => 'Kevin Omondi',
                'phone' => '254712345629',
                'category' => 'beauty-events-social-support',
                'bio' => 'Executive mobile barber providing crisp fades, beard grooming, hot towel treatments, and kids haircuts at your doorstep.',
                'areas' => $getAreaIds(['CBD', 'Parklands', 'Westlands']),
                'services' => [
                    ['slug' => 'barber-kinyozi', 'name' => 'Mobile Barber & Beard Grooming', 'price' => 800],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Beauty & Events', 'price' => 1],
                ],
            ],
            [
                'email' => 'sta@kz.com',
                'name' => 'Stacy Njeri',
                'phone' => '254712345630',
                'category' => 'beauty-events-social-support',
                'bio' => 'Certified makeup artist and henna designer for weddings, traditional ceremonies (Ruracio), photoshoots, and special occasions.',
                'areas' => $getAreaIds(['Karen', 'Lavington', 'Runda']),
                'services' => [
                    ['slug' => 'makeup-artist', 'name' => 'Glam & Bridal Makeup Services', 'price' => 4000],
                    ['slug' => 'henna-artist', 'name' => 'Custom Henna Artwork', 'price' => 1500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Beauty & Events', 'price' => 1],
                ],
            ],
            [
                'email' => 'maro@kz.com',
                'name' => 'Mark Otieno',
                'phone' => '254712345631',
                'category' => 'beauty-events-social-support',
                'bio' => 'Professional event photographer & drone videographer covering weddings, birthday parties, corporate functions, and portraits.',
                'areas' => $getAreaIds(['Milimani (Nakuru)', 'Nakuru CBD', 'Free Area']),
                'services' => [
                    ['slug' => 'photographer-videographer', 'name' => 'Event Photography & Videography', 'price' => 15000],
                    ['slug' => 'event-mc-dj', 'name' => 'Event MC & Sound DJ Setup', 'price' => 12000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Beauty & Events', 'price' => 1],
                ],
            ],

            // --- AGRICULTURE & FARMING (3 PROVIDERS) ---
            [
                'email' => 'wyc@kz.com',
                'name' => 'Wycliffe Juma',
                'phone' => '254712345632',
                'category' => 'agriculture-rural-farming',
                'bio' => 'Hardworking farm hand and landscaper skilled in shamba weeding, crop harvesting, flower gardening, and live fence trimming.',
                'areas' => $getAreaIds(['Thika Town', 'Juja', 'Kiambu Town']),
                'services' => [
                    ['slug' => 'farm-laborer-shamba-boy', 'name' => 'Farm Laborer & Shamba Maintenance', 'price' => 800],
                    ['slug' => 'landscaper-gardener', 'name' => 'Compound Landscaping & Gardening', 'price' => 2000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Agriculture', 'price' => 1],
                ],
            ],
            [
                'email' => 'samr@kz.com',
                'name' => 'Samuel Rotich',
                'phone' => '254712345633',
                'category' => 'agriculture-rural-farming',
                'bio' => 'Tractor hire operator providing land ploughing, harrowing, rotavating, and agricultural transport across Rift Valley farms.',
                'areas' => $getAreaIds(['Eldoret CBD', 'Kapseret', 'Huruma (Eldoret)']),
                'services' => [
                    ['slug' => 'tractor-hire', 'name' => 'Tractor Land Ploughing & Harrowing', 'price' => 3500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Agriculture', 'price' => 1],
                ],
            ],
            [
                'email' => 'jan@kz.com',
                'name' => 'Jane Waithera',
                'phone' => '254712345634',
                'category' => 'agriculture-rural-farming',
                'bio' => 'Licensed Veterinary Officer providing livestock treatment, artificial insemination (AI), poultry vaccination, and crop disease consultation.',
                'areas' => $getAreaIds(['Kikuyu Town', 'Ruaka', 'Thindigua']),
                'services' => [
                    ['slug' => 'veterinary-officer', 'name' => 'Veterinary Treatment & Livestock Care', 'price' => 2500],
                    ['slug' => 'poultry-expert', 'name' => 'Poultry Farming Consultation', 'price' => 2000],
                    ['slug' => 'agronomist', 'name' => 'Agronomy & Crop Disease Advisory', 'price' => 3000],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Agriculture', 'price' => 1],
                ],
            ],

            // --- BUSINESS & EDUCATION (3 PROVIDERS) ---
            [
                'email' => 'ale@kz.com',
                'name' => 'Alex Kibet',
                'phone' => '254712345635',
                'category' => 'business-cyber-education-health',
                'bio' => 'Experienced private tutor offering CBC primary & secondary tuition in Mathematics, Sciences, and English home lessons.',
                'areas' => $getAreaIds(['South B', 'South C', 'Madaraka']),
                'services' => [
                    ['slug' => 'tutor', 'name' => 'Private Home Lessons & CBC Tuition', 'price' => 1200],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Business & Education', 'price' => 1],
                ],
            ],
            [
                'email' => 'chr@kz.com',
                'name' => 'Christine Musyoka',
                'phone' => '254712345636',
                'category' => 'business-cyber-education-health',
                'bio' => 'Business services agent providing KRA NIL & individual tax returns filing, BRS business name registration, and fast typing/data entry.',
                'areas' => $getAreaIds(['CBD', 'Westlands', 'Parklands']),
                'services' => [
                    ['slug' => 'kra-returns-assistant', 'name' => 'KRA Tax Filing & Returns Assistant', 'price' => 1000],
                    ['slug' => 'business-registration-agent', 'name' => 'Business Name Registration Agent', 'price' => 3000],
                    ['slug' => 'typist-data-entry', 'name' => 'Document Typing & Data Entry', 'price' => 800],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Business & Education', 'price' => 1],
                ],
            ],
            [
                'email' => 'ian@kz.com',
                'name' => 'Ian Mutua',
                'phone' => '254712345637',
                'category' => 'business-cyber-education-health',
                'bio' => 'Creative graphic designer crafting company logos, promotional flyers, social media banners, business cards, and brand identity packages.',
                'areas' => $getAreaIds(['Milimani (Kisumu)', 'Kisumu CBD', 'Kondele']),
                'services' => [
                    ['slug' => 'graphic-designer', 'name' => 'Logo & Brand Identity Design', 'price' => 2500],
                ],
                'test_service' => [
                    ['slug' => 'test-service-1kes', 'custom_name' => 'Test Service – Business & Education', 'price' => 1],
                ],
            ],
        ];

        $directoryServices = ServiceDirectory::getServices();

        foreach ($providers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('1234'),
                    'role' => 'provider',
                    'bio' => $data['bio'],
                    'kyc_status' => 'approved',
                    'email_verified_at' => now(),
                ]
            );

            // Sync Service Areas
            if (!empty($data['areas'])) {
                $user->serviceAreas()->syncWithoutDetaching($data['areas']);
            }

            // Sync/Create Standard Provider Services
            foreach ($data['services'] as $srv) {
                $matchedSrv = null;
                foreach ($directoryServices as $s) {
                    if ($s['slug'] === $srv['slug']) {
                        $matchedSrv = $s;
                        break;
                    }
                }

                ProviderService::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'service_slug' => $srv['slug'],
                    ],
                    [
                        'custom_name' => isset($srv['name']) ? $srv['name'] : ($matchedSrv ? $matchedSrv['name'] : $srv['slug']),
                        'custom_description' => $matchedSrv ? 'Professional ' . $matchedSrv['name'] . ' service' : 'Professional service',
                        'price' => $srv['price'],
                        'is_custom' => false,
                    ]
                );
            }

            // Create 1 KES Test Service
            foreach ($data['test_service'] as $testSrv) {
                ProviderService::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'service_slug' => $testSrv['slug'],
                    ],
                    [
                        'custom_name' => $testSrv['custom_name'],
                        'custom_description' => '1 KES test service for M-Pesa payment validation',
                        'price' => $testSrv['price'],
                        'is_custom' => true,
                    ]
                );
            }

            // Approved KYC Record
            KycSubmission::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'id_front_path' => 'kyc/dummy_id_front.jpg',
                    'id_back_path' => 'kyc/dummy_id_back.jpg',
                    'kra_pin_path' => 'kyc/dummy_kra_pin.pdf',
                    'status' => 'approved',
                ]
            );
        }
    }
}
