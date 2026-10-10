<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ServiceArea;
use Illuminate\Support\Facades\Hash;

class QaDataSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Admin Account
        $admin = User::firstOrCreate(
            ['email' => 'admin@kaazilink.com'],
            [
                'name' => 'QA Admin',
                'password' => $password,
                'email_verified_at' => now(),
                'role' => 'admin',
                'kyc_status' => 'approved'
            ]
        );
        $admin->update(['role' => 'admin']);

        // 2. Client Account
        $client = User::firstOrCreate(
            ['email' => 'client@kaazilink.com'],
            [
                'name' => 'QA Client',
                'password' => $password,
                'email_verified_at' => now(),
                'role' => 'client',
                'kyc_status' => 'pending'
            ]
        );

        // 3. Approved Provider with multiple areas
        $provider1 = User::firstOrCreate(
            ['email' => 'approved@kaazilink.com'],
            [
                'name' => 'Onyango Ndubi (Approved)',
                'password' => $password,
                'email_verified_at' => now(),
                'role' => 'provider',
                'kyc_status' => 'approved',
                'phone' => '0712345678',
                'bio' => 'Experienced plumber across Nairobi.'
            ]
        );
        $provider1->update(['role' => 'provider', 'kyc_status' => 'approved']);
        $areas1 = ServiceArea::take(3)->pluck('id')->toArray();
        $provider1->serviceAreas()->sync($areas1);
        \App\Models\ProviderService::firstOrCreate(
            ['user_id' => $provider1->id, 'service_slug' => 'plumber'],
            ['price' => 2000.00]
        );
        \App\Models\ProviderService::firstOrCreate(
            ['user_id' => $provider1->id, 'service_slug' => 'mama-fua-laundry'],
            ['price' => 1500.00]
        );

        // 4. Pending Provider
        $provider2 = User::firstOrCreate(
            ['email' => 'pending@kaazilink.com'],
            [
                'name' => 'Akinyi Omondi (Pending)',
                'password' => $password,
                'email_verified_at' => now(),
                'role' => 'provider',
                'kyc_status' => 'pending',
                'phone' => '0722345678',
                'bio' => 'New electrician pending verification.'
            ]
        );
        $provider2->update(['role' => 'provider', 'kyc_status' => 'pending']);
        $areas2 = ServiceArea::skip(3)->take(2)->pluck('id')->toArray();
        $provider2->serviceAreas()->sync($areas2);

        // 5. Extra approved providers for realistic distance data
        $names = ['Wanjiku Mbugua', 'Kiprotich Sang', 'Fatuma Hassan', 'Mutua Kioko', 'Atieno Otieno'];
        foreach ($names as $idx => $name) {
            $email = 'provider_extra' . $idx . '@kaazilink.com';
            $p = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name . ' (Approved)',
                    'password' => $password,
                    'email_verified_at' => now(),
                    'role' => 'provider',
                    'kyc_status' => 'approved',
                ]
            );
            $p->update(['role' => 'provider', 'kyc_status' => 'approved']);
            // Assign 1-3 random areas
            $areaIds = ServiceArea::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();
            $p->serviceAreas()->sync($areaIds);
            
            // Assign some random services with realistic Kenyan market prices
            $defaultPrices = [
                'plumber' => 2000.00,
                'electrician' => 2500.00,
                'carpenter' => 2200.00,
                'mama-fua-laundry' => 1200.00,
                'house-help-day-scholar' => 1800.00,
                'nanny-babysitter' => 2500.00,
            ];
            $randomServices = array_keys($defaultPrices);
            shuffle($randomServices);
            foreach (array_slice($randomServices, 0, rand(1, 3)) as $svc) {
                \App\Models\ProviderService::firstOrCreate(
                    ['user_id' => $p->id, 'service_slug' => $svc],
                    ['price' => $defaultPrices[$svc] ?? 1500.00]
                );
            }
        }
    }
}
