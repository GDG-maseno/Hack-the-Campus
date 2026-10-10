<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => User::factory()->create(['role' => 'client'])->id,
            'provider_id' => User::factory()->create(['role' => 'provider'])->id,
            'service_slug' => 'plumber',
            'status' => 'requested',
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }
}
