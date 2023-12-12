<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Models\ParkingSlot;

class BookingStoreTest extends TestCase
{
    use RefreshDatabase;

    // Test for Required Fields
    public function testRequiresDateFields()
    {
        $user = User::factory()->create();
        $parking_slot = ParkingSlot::factory()->create();
        
        $response = $this->actingAs($user)->postJson("/api/bookings/", [
            'parking_slot_id' => $user->id,
            'user_id' => $parking_slot->id,
            'user_entry_date' => '2024-12-28', 
            // 'user_exit_date' => '2024-12-31',
        ]);

        $response
            ->assertStatus(422);
    }

    // Test for Valid Date Formats
    public function testRequiresValidDates()
    {
        $user = User::factory()->create();
        $parking_slot = ParkingSlot::factory()->create();
        
        $response = $this->actingAs($user)->postJson("/api/bookings/", [
            'parking_slot_id' => $user->id,
            'user_id' => $parking_slot->id,
            'user_entry_date' => 'invalid-date', 
            'user_exit_date' => 'invalid-date',
        ]);

        $response
            ->assertStatus(422);
    }
    
    // Test dateTo is After dateFrom
    public function testDateToShouldBeAfterDateFrom()
    {
        $user = User::factory()->create();
        $parking_slot = ParkingSlot::factory()->create();
        
        $response = $this->actingAs($user)->postJson("/api/bookings/", [
            'parking_slot_id' => $user->id,
            'user_id' => $parking_slot->id,
            'user_entry_date' => '2024-01-10', 
            'user_exit_date' => '2024-01-09',
        ]);

        $response
            ->assertStatus(422);
    }

    // Test Validation Rules
    public function testStoreRequiresValidInput()
    {
        // Simulate a logged-in user
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->json('POST', '/api/bookings/', [
            'parking_slot_id' => 1,
            'user_entry_date' => '2023-03-01',
            'user_exit_date' => '2024-03-02',
            'user_id' => 10
        ]);

        $response->assertStatus(422); // 422 Unprocessable Entity
        // $response->assertJsonValidationErrors(['user_id', 'parking_slot_id', 'user_entry_date', 'user_exit_date']);
    }

    // Test Successful Booking Creation
    public function testStoreCreatesBookingSuccessfully()
    {
        $user = User::factory()->create();
        $parking_slot = ParkingSlot::factory()->create();
        $this->actingAs($user);

        $response = $this->json('POST', '/api/bookings/', [
            'parking_slot_id' => $parking_slot->id,
            'user_entry_date' => '2024-03-01',
            'user_exit_date' => '2024-03-02',
            'user_id' => $user->id
        ]);

        $response->assertStatus(201); // 201 Created
    }

    // Test Successful Booking Creation
    public function testCreateBookingAfterToday()
    {
        $user = User::factory()->create();
        $parking_slot = ParkingSlot::factory()->create();
        $this->actingAs($user);

        $response = $this->json('POST', '/api/bookings/', [
            'parking_slot_id' => $parking_slot->id,
            'user_entry_date' => '2023-03-01',
            'user_exit_date' => '2024-03-02',
            'user_id' => $user->id
        ]);

        $response->assertStatus(422); // 201 Created
    }


}
