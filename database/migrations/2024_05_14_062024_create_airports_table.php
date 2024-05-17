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
        Schema::create('airports', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Primary Key

            $table->string('name'); // Name of the airport
            $table->string('city'); // City where the airport is located
            $table->string('country'); // Country where the airport is located
            $table->decimal('latitude', 10, 7); // Latitude coordinate of the airport's location
            $table->decimal('longitude', 10, 7); // Longitude coordinate of the airport's location
            $table->string('timezone'); // Timezone of the airport
            $table->unsignedInteger('elevation'); // Elevation of the airport above sea level

            $table->timestamps(); // Timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the airports table if it exists
        Schema::dropIfExists('airports');
    }
};
