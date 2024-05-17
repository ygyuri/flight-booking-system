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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Primary Key

            $table->uuid('booking_id'); // Foreign Key: booking_id references id in bookings table
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');

            $table->decimal('amount', 10, 2); // The amount of the payment
            $table->string('payment_method'); // The method used for payment
            $table->string('status')->default('pending'); // The status of the payment
            $table->string('transaction_id')->unique(); // Unique identifier for the payment transaction
            $table->timestamp('payment_date')->useCurrent(); // Date and time when the payment was made

            $table->timestamps(); // Timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the payments table if it exists
        Schema::dropIfExists('payments');
    }
};
