<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Models\ParkingSlot;

class BookingUpdateTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test for Successful Update.
     */
    public function testUpdateBookingSuccessfully()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);
        $parking_slot = ParkingSlot::factory()->create();

        $response = $this->actingAs($user)->putJson("/api/bookings/{$booking->id}", [
            'user_id' => $user->id,
            'parking_slot_id' => $parking_slot->id,
            'user_entry_date' => '2024-04-01',
            'user_exit_date' => '2024-04-02',
        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Booking updated successfully!']);
    }


    public function testUpdateBookingWithInvalidData()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson("/api/bookings/{$booking->id}", [
            'user_id' => $user->id,
            'user_entry_date' => 'invalid-date',
            'user_exit_date' => 'invalid-date',
            'parking_slot_id' => 'invalid-text',
        ]);

        $response->assertStatus(422); // Validation error
    }



}
