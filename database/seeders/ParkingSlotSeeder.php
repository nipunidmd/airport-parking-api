<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParkingSlotSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('parking_slots')->count() >= 10) {
            return; // Exit the seeder if 10 or more entries already exist
        }
        
        $parkingSlots = [
            // Define your 10 parking slot data
            ['winter_price' => '20', 'summer_price' => '25', 'weekday_price' => '35', 'weekend_price' => '30'],
            ['winter_price' => '20', 'summer_price' => '25', 'weekday_price' => '35', 'weekend_price' => '30'],
            ['winter_price' => '20', 'summer_price' => '25', 'weekday_price' => '35', 'weekend_price' => '30'],
            ['winter_price' => '20', 'summer_price' => '25', 'weekday_price' => '35', 'weekend_price' => '30'],
            ['winter_price' => '20', 'summer_price' => '25', 'weekday_price' => '35', 'weekend_price' => '30'],
            ['winter_price' => '25', 'summer_price' => '30', 'weekday_price' => '40', 'weekend_price' => '35'],
            ['winter_price' => '25', 'summer_price' => '30', 'weekday_price' => '40', 'weekend_price' => '35'],
            ['winter_price' => '25', 'summer_price' => '30', 'weekday_price' => '40', 'weekend_price' => '35'],
            ['winter_price' => '25', 'summer_price' => '30', 'weekday_price' => '40', 'weekend_price' => '35'],
            ['winter_price' => '25', 'summer_price' => '30', 'weekday_price' => '40', 'weekend_price' => '35'],
        ];

        DB::table('parking_slots')->insert($parkingSlots);
    }
}