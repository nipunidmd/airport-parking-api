<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\ParkingSlot;
use App\Models\Booking;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParkingSlotAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function testSlotNotBooked()
    {
        $parkingSlot = ParkingSlot::factory()->create();
        $userEntryDate = '2024-01-01';
        $userExitDate = '2024-01-05';

        // No bookings exist for this slot yet
        $isBooked = (new BookingController())->checkSlotBookingStatus($userEntryDate, $userExitDate, $parkingSlot->id);
        
        $this->assertFalse($isBooked);
    }
    
    public function testSlotAlreadyBooked()
    {
        $parkingSlot = ParkingSlot::factory()->create();
        // dd($parkingSlot);
        $booking = Booking::factory()->create([
            'parking_slot_id' => $parkingSlot->id,
            'entry_date' => '2024-01-03',
            'exit_date' => '2024-01-06',
        ]);

        $userEntryDate = '2024-01-01';
        $userExitDate = '2024-01-04'; // Overlaps with existing booking

        $controller = new BookingController();
        $isBooked = $controller->checkSlotBookingStatus($userEntryDate, $userExitDate, $parkingSlot->id);
        
        $this->assertTrue($isBooked);
    }

}
