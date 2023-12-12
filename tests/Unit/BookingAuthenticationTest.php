<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Booking;
use App\Models\ParkingSlot;
use PHPUnit\Framework\TestCase;
use Mockery;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Support\Facades\Auth;

class BookingAuthenticationTest extends TestCase
{
    use RefreshDatabase;


    public function testCheckAuthenticationWithAuthenticatedUser()
    {
        Auth::shouldReceive('check')->once()->andReturn(true);
    
        // Create an instance of the BookingController
        $controller = new BookingController();

        // Call the checkAuthentication method on the controller instance
        $response = $controller->checkAuthentication();
    
        // Assert that no response is returned for authenticated user
        $this->assertNull($response);
    }
    
    
}

