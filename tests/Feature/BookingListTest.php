<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingListTest extends TestCase
{
    use RefreshDatabase;
    
    public function testInvalidDateFields()
    {
        $response = $this->json('GET', '/api/booking-list/', [
            'dateFrom' => 'invalid-date',
            'dateTo' => 'invalid-date'
        ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['dateFrom', 'dateTo']);
    }


    // Test for Required Fields
    public function testRequiresDateFields()
    {
        $response = $this->json('GET', '/api/booking-list');

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['dateFrom', 'dateTo']);
    }

    // Test for Valid Date Formats
    public function testRequiresValidDates()
    {
        $response = $this->json('GET', '/api/booking-list/', [
            'dateFrom' => 'invalid-date', 
            'dateTo' => 'invalid-date'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['dateFrom', 'dateTo']);
    }

    // Test dateTo is After dateFrom
    public function testDateToShouldBeAfterDateFrom()
    {
        $response = $this->json('GET', '/api/booking-list/', [
            'dateFrom' => '2024-01-10', 
            'dateTo' => '2024-01-09'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['dateTo']);
    }


    // Test Successful Response Structure
    public function testSuccessfulResponseStructure()
    {
        $response = $this->json('GET', '/api/booking-list/', [
            'dateFrom' => '2024-01-01', 
            'dateTo' => '2024-01-05']);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'dateFrom',
                'dateTo',
                'availability' => [
                    '*' => [
                        'date',
                        'slots'
                    ]
                ]
            ]);
    }

    // Test Data Accuracy
    // public function testReturnsCorrectAvailabilityData()
    // {
    //     // Assuming you have a seeder or factory setup
    //     // Seed data here...

    //     $response = $this->json('GET', '/api/booking-list/', [
    //         'dateFrom' => '2024-01-01', 
    //         'dateTo' => '2024-01-05'
    //     ]);

    //     // Verify the response data...
    // }

    




}
