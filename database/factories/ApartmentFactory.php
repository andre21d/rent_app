<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApartmentFactory extends Factory
{
    public function definition(): array
    {
        $cities = ['New York', 'Los Angeles', 'Chicago', 'Miami', 'Seattle', 'Boston', 'Austin', 'Denver'];
        $states = ['NY', 'CA', 'IL', 'FL', 'WA', 'MA', 'TX', 'CO'];
        
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraphs(2, true),
            'total_area' => (string) $this->faker->numberBetween(50, 300),
            'price_per_day' => $this->faker->randomFloat(2, 50, 500),
            'price_per_month' => $this->faker->randomFloat(2, 1500, 8000),
            'state' => $this->faker->randomElement($states),
            'city' => $this->faker->randomElement($cities),
            'street' => $this->faker->streetName(),
            'building_number' => $this->faker->buildingNumber(),
            'level' => (string) $this->faker->numberBetween(1, 20),
            'is_available' => $this->faker->boolean(80),
            'owner_id' => User::factory(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }

    public function available()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_available' => true,
            ];
        });
    }

    public function unavailable()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_available' => false,
            ];
        });
    }
}