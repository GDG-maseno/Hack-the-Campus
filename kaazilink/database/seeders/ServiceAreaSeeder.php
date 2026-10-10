<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceArea;

class ServiceAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            // Nairobi & Surrounding
            ['name' => 'CBD', 'lat' => -1.283, 'lng' => 36.816],
            ['name' => 'Westlands', 'lat' => -1.266, 'lng' => 36.804],
            ['name' => 'Kilimani', 'lat' => -1.288, 'lng' => 36.784],
            ['name' => 'Kileleshwa', 'lat' => -1.275, 'lng' => 36.782],
            ['name' => 'Lavington', 'lat' => -1.282, 'lng' => 36.766],
            ['name' => 'Karen', 'lat' => -1.332, 'lng' => 36.711],
            ['name' => 'Langata', 'lat' => -1.339, 'lng' => 36.764],
            ['name' => 'Madaraka', 'lat' => -1.304, 'lng' => 36.812],
            ['name' => 'South B', 'lat' => -1.311, 'lng' => 36.833],
            ['name' => 'South C', 'lat' => -1.321, 'lng' => 36.820],
            ['name' => 'Eastleigh', 'lat' => -1.274, 'lng' => 36.852],
            ['name' => 'Buruburu', 'lat' => -1.281, 'lng' => 36.871],
            ['name' => 'Donholm', 'lat' => -1.291, 'lng' => 36.883],
            ['name' => 'Umoja', 'lat' => -1.282, 'lng' => 36.888],
            ['name' => 'Pipeline', 'lat' => -1.316, 'lng' => 36.892],
            ['name' => 'Kasarani', 'lat' => -1.222, 'lng' => 36.898],
            ['name' => 'Roysambu', 'lat' => -1.218, 'lng' => 36.883],
            ['name' => 'Muthaiga', 'lat' => -1.250, 'lng' => 36.834],
            ['name' => 'Ruaka', 'lat' => -1.200, 'lng' => 36.782],
            ['name' => 'Rongai', 'lat' => -1.396, 'lng' => 36.761],
            ['name' => 'Syokimau', 'lat' => -1.364, 'lng' => 36.936],
            ['name' => 'Kitengela', 'lat' => -1.482, 'lng' => 36.960],
            ['name' => 'Kitisuru', 'lat' => -1.237, 'lng' => 36.766],
            ['name' => 'Gigiri', 'lat' => -1.233, 'lng' => 36.812],
            ['name' => 'Parklands', 'lat' => -1.262, 'lng' => 36.818],
            ['name' => 'Zimmerman', 'lat' => -1.209, 'lng' => 36.894],
            ['name' => 'Kahawa West', 'lat' => -1.185, 'lng' => 36.898],

            // Mombasa
            ['name' => 'Nyali', 'lat' => -4.043, 'lng' => 39.702],
            ['name' => 'Bamburi', 'lat' => -3.987, 'lng' => 39.718],
            ['name' => 'Kisauni', 'lat' => -4.018, 'lng' => 39.685],
            ['name' => 'Mvita', 'lat' => -4.055, 'lng' => 39.664],
            ['name' => 'Shanzu', 'lat' => -3.954, 'lng' => 39.751],

            // Nakuru
            ['name' => 'Milimani (Nakuru)', 'lat' => -0.278, 'lng' => 36.062],
            ['name' => 'Nakuru CBD', 'lat' => -0.283, 'lng' => 36.066],
            ['name' => 'Kaptembwa', 'lat' => -0.301, 'lng' => 36.042],
            ['name' => 'Free Area', 'lat' => -0.271, 'lng' => 36.098],

            // Kisumu
            ['name' => 'Milimani (Kisumu)', 'lat' => -0.108, 'lng' => 34.752],
            ['name' => 'Kisumu CBD', 'lat' => -0.091, 'lng' => 34.767],
            ['name' => 'Nyalenda', 'lat' => -0.114, 'lng' => 34.772],
            ['name' => 'Kondele', 'lat' => -0.082, 'lng' => 34.778],

            // Eldoret
            ['name' => 'Eldoret CBD', 'lat' => 0.514, 'lng' => 35.269],
            ['name' => 'Elgon View (Eldoret)', 'lat' => 0.498, 'lng' => 35.281],
            ['name' => 'Langas', 'lat' => 0.478, 'lng' => 35.262],
            ['name' => 'Kapseret', 'lat' => 0.435, 'lng' => 35.212],
            ['name' => 'Huruma (Eldoret)', 'lat' => 0.531, 'lng' => 35.248],

            // Kiambu
            ['name' => 'Thindigua', 'lat' => -1.189, 'lng' => 36.832],
            ['name' => 'Kiambu Town', 'lat' => -1.171, 'lng' => 36.835],
            ['name' => 'Kikuyu Town', 'lat' => -1.243, 'lng' => 36.662],
            ['name' => 'Kiamumbi', 'lat' => -1.178, 'lng' => 36.872],
            ['name' => 'Thika Town', 'lat' => -1.038, 'lng' => 37.073],
            ['name' => 'Juja', 'lat' => -1.102, 'lng' => 37.013],
        ];

        foreach ($areas as $area) {
            ServiceArea::firstOrCreate(
                ['name' => $area['name']],
                [
                    'latitude' => $area['lat'],
                    'longitude' => $area['lng'],
                ]
            );
        }
    }
}
