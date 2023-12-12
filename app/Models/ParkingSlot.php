<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class ParkingSlot extends Model
{
    use HasFactory;

    protected $guarded = [

        'winter_price',
        'summer_price',
        'weekday_price',
        'weekend_price',
    
    ];

    // A parking slot has many bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'parking_slot_id');
    }

}
