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
        Schema::create('aircrafts', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Primary Key

            $table->string('name'); // Name or model of the aircraft
            $table->string('manufacturer'); // Manufacturer of the aircraft
            $table->string('registration_number')->unique(); // Unique registration number of the aircraft
            $table->unsignedInteger('sitting_capacity'); // Total sitting capacity of the aircraft

            $table->timestamps(); // Timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the aircraft table if it exists
        Schema::dropIfExists('aircrafts');
    }
};