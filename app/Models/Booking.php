<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',
        'parking_slot_id',
        'entry_date',
        'exit_date',
        'total_price'
    
    ];

    // Each booking belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Each booking belongs to a parking slot
    public function parking_slot()
    {
        return $this->belongsTo(User::class, 'parking_slot_id');
    }
    
}
