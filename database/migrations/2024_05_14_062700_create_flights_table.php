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
        Schema::create('flights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('flight_number');
            $table->uuid('departure_airport_id');
            $table->uuid('arrival_airport_id');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->string('duration');
            $table->uuid('aircraft_id');
            $table->decimal('price', 10, 2);
            $table->string('status')->default('scheduled'); // Adding status field with default value
            $table->string('available_seats');
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('departure_airport_id')->references('id')->on('airports')->onDelete('cascade');
            $table->foreign('arrival_airport_id')->references('id')->on('airports')->onDelete('cascade');
            $table->foreign('aircraft_id')->references('id')->on('aircrafts')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};