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
        Schema::create('seats', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Primary Key

            $table->string('seat_number'); // The unique identifier or number assigned to the seat
            $table->string('seat_class'); // The class of the seat (economy, business, first class)
            $table->string('availability_status')->default('available'); // The availability status of the seat (e.g., available, reserved, booked)
            $table->decimal('price', 10, 2); // The price of the seat

            $table->uuid('flight_id'); // Foreign Key: flight_id references id in flights table
            $table->foreign('flight_id')->references('id')->on('flights')->onDelete('cascade');

            $table->timestamps(); // Timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the seats table if it exists
        Schema::dropIfExists('seats');
    }
};
