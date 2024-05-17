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
            // Primary Key
            $table->uuid('id')->primary();

            // Foreign Key: customer_id references id in customers table
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            // Foreign Key: flight_id references id in flights table
            $table->uuid('flight_id');
            $table->foreign('flight_id')->references('id')->on('flights')->onDelete('cascade');

            // Other Fields
            $table->unsignedInteger('passenger_count'); // Number of passengers
            $table->uuid('seat_id')->nullable(); // FK referencing id in seats table
            $table->foreign('seat_id')->references('id')->on('seats')->onDelete('cascade');
            $table->decimal('total_price', 10, 2); // Total price of the booking
            $table->string('status')->default('pending'); // Booking status
            $table->string('payment_status')->default('pending'); // Payment status
            $table->timestamp('booking_date')->useCurrent(); // Date and time of booking
            $table->string('booking_reference')->unique()->nullable(); // Unique booking reference
           
            $table->text('notes')->nullable(); // Additional notes
            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the bookings table if it exists
        Schema::dropIfExists('bookings');
    }
};