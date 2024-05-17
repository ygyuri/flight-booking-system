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
        Schema::create('flights_schedule', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Primary Key

            $table->uuid('flight_id'); // Foreign Key: flight_id references id in flights table
            $table->foreign('flight_id')->references('id')->on('flights')->onDelete('cascade');

            $table->dateTime('departure_time'); // Date and time of departure for the flight
            $table->dateTime('arrival_time'); // Date and time of arrival for the flight

            $table->string('status')->default('scheduled'); // Status of the flight schedule (e.g., scheduled, canceled, delayed)

            $table->timestamps(); // Timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the flights_schedule table if it exists
        Schema::dropIfExists('flights_schedule');
    }
};
