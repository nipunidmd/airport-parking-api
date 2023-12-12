<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->integer(column:'user_id')->constrained(table:'users');
            $table->unsignedBigInteger('parking_slot_id')->constrained(table:'parking_slots');
            $table->date(column:'entry_date');
            $table->date(column:'exit_date');
            $table->integer(column:'total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
