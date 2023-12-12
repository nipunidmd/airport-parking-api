<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use DateTime;

class BookingFactory extends Factory
{
    protected $model = Booking::class;
    public function definition(): array
    {  
        $entryDate = date('Y-m-d', strtotime('+1 week'));
        $exitDate = date('Y-m-d', strtotime('+2 week'));
        
        return [
            'user_id' => \App\Models\User::factory(), 
            'parking_slot_id' => $this->faker->numberBetween(1, 10),
            'entry_date' => $entryDate,
            'exit_date' => $exitDate,
            'total_price' => $this->faker->randomNumber(2) * 100, // Random total price
        ];
    }
}
