<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ParkingSlot;

class ParkingSlotTest extends TestCase
{
    use RefreshDatabase;

    public function testMoreThanTenParkingSlots()
    {
        // Create 11 parking slots
        ParkingSlot::factory()->count(11)->create();

        // Retrieve the count of parking slots from the database
        $slotsCount = ParkingSlot::count();

        // Assert that the count is greater than 10
        $this->assertTrue($slotsCount > 10);
    }
}
