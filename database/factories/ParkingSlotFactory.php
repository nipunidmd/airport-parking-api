<?php

namespace Database\Factories;

use App\Models\ParkingSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ParkingSlot>
 */
class ParkingSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'winter_price' => $this->faker->numberBetween(10, 50),
            'summer_price' => $this->faker->numberBetween(10, 50),
            'weekday_price' => $this->faker->numberBetween(10, 50),
            'weekend_price' => $this->faker->numberBetween(10, 50),
        ];
    }
}
