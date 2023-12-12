<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;

class BookingDeleteTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function testBookingCanBeDeleted()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'entry_date' => now()->addDays(2)->toDateString(), // Future date
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }

    public function testDeletingNonExistingBookingReturnsNotFound()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/api/bookings/999'); // Non-existing ID

        $response->assertStatus(404);
    }

    public function testBookingCannotBeDeletedOnOrAfterStartDate()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'entry_date' => now()->toDateString(), // Today's date
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(403);
    }


}
