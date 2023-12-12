<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\ParkingSlot;
use App\Models\Booking;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CalculateTotalPriceTest extends TestCase
{
    use RefreshDatabase;

    public function testPriceCalculationForWeekdays()
    {
        $parking_slot = ParkingSlot::factory()->create([
            'winter_price' => 30,
            'summer_price' => 10,
            'weekday_price' => 15,
            'weekend_price' => 20,
        ]);

        $controller = new BookingController();

        // Example: Test for 5 weekdays
        $totalPrice = $controller->calculateTotalPrice('2024-03-04', '2024-03-08', $parking_slot->id);
        $this->assertEquals(5 * 15, $totalPrice); // 5 weekdays at 15 each
    }

    public function testPriceCalculationForWeekends()
    {
        $parking_slot = ParkingSlot::factory()->create([
            'winter_price' => 30,
            'summer_price' => 10,
            'weekday_price' => 15,
            'weekend_price' => 20,
        ]);

        $controller = new BookingController();

        // Example: Test for 5 weekdays
        $totalPrice = $controller->calculateTotalPrice('2024-03-02', '2024-03-03', $parking_slot->id);
        $this->assertEquals(2 * 20, $totalPrice); // 2 weekends at 20 each
    }

    public function testPriceCalculationForSummerDays()
    {
        {
            $parking_slot = ParkingSlot::factory()->create([
                'winter_price' => 30,
                'summer_price' => 10,
                'weekday_price' => 15,
                'weekend_price' => 20,
            ]);
    
            $controller = new BookingController();
    
            // Example: Test for 5 weekdays
            $totalPrice = $controller->calculateTotalPrice('2024-06-02', '2024-06-05', $parking_slot->id);
            $this->assertEquals(4 * 10, $totalPrice); // 4 summer days at 10 each
        }
    }

    public function testPriceCalculationForWinterDays()
    {
        {
            $parking_slot = ParkingSlot::factory()->create([
                'winter_price' => 30,
                'summer_price' => 10,
                'weekday_price' => 15,
                'weekend_price' => 20,
            ]);
    
            $controller = new BookingController();
    
            // Test for 5 winter days
            $totalPrice = $controller->calculateTotalPrice('2023-12-25', '2023-12-29', $parking_slot->id);
            $this->assertEquals(5 * 30, $totalPrice); // 5 winter days at 30 each
        }
    }

    public function testPriceCalculationForMixedDays()
    {
        {
            $parking_slot = ParkingSlot::factory()->create([
                'winter_price' => 30,
                'summer_price' => 10,
                'weekday_price' => 15,
                'weekend_price' => 20,
            ]);
    
            $controller = new BookingController();
    
            // Test for 2 winter days, 2 weekends & 3 weekdays 
            $totalPrice = $controller->calculateTotalPrice('2024-02-28', '2024-03-05', $parking_slot->id);
            $this->assertEquals(2*30 + 2*20 + 3*15, $totalPrice); 
        }
    }

}
