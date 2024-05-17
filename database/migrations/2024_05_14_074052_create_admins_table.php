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
        Schema::create('admins', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Primary Key

            $table->string('name'); // Name of the admin
            $table->string('email')->unique(); // Email address of the admin
            $table->string('password'); // Encrypted password of the admin
            $table->rememberToken(); // Remember token for "remember me" functionality

            $table->timestamps(); // Timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the admins table if it exists
        Schema::dropIfExists('admins');
    }
};