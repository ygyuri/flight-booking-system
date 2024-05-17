<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Flight; // Import the Flight Model
use Illuminate\Database\Eloquent\Relations\HasMany;

class Airport extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    // Define fillable attributes (alternative to guarded)
    protected $fillable = [
        'name',
        'city',
        'country',
        'latitude',
        'longitude',
        'timezone',
        'elevation',
    ];

    // Relationship with Flight Model (one Airport has many Flights)
    /**
     * This method defines a one-to-many relationship between the Airport and Flight models.
     * An airport can have many departing flights. The foreign key on the Flight table
     * is 'departure_airport_id' which references the ID of the Airport.
     */
    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class, 'departure_airport_id');
    }

    /**
     * This method defines another one-to-many relationship for arrival flights.
     * An airport can have many arriving flights. The foreign key on the Flight table
     * is 'arrival_airport_id' which references the ID of the Airport.
     */
    public function arrivalFlights(): HasMany
    {
        return $this->hasMany(Flight::class, 'arrival_airport_id');
    }

    // Custom methods and accessors

    /**
     * This accessor method creates a new attribute named `full_location` by combining
     * the city, country, and airport name. This provides a more user-friendly
     * representation of the airport's location.
     */
    public function getFullLocationAttribute(): string
    {
        return sprintf('%s, %s, %s', $this->city, $this->country, $this->name);
    }

    /**
     * This accessor method formats the latitude and longitude values to two decimal places
     * and creates a new attribute named `formatted_coordinates`. This provides a more
     * readable format for displaying the airport's coordinates.
     */
    public function getFormattedCoordinatesAttribute(): string
    {
        return sprintf('Lat: %.2f, Lon: %.2f', $this->latitude, $this->longitude);
    }

    /**
     * This static method defines a scope that can be used to filter the query results
     * to only include non-deleted (active) airports. You can use this scope in your queries
     * to retrieve only active airports.
     */
    public function scopeActiveAirports($query)
    {
        return $query->whereNull('deleted_at');
    }
}