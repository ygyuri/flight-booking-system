<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Flight extends Model
{
    use HasFactory, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'flight_number',
        'departure_airport_id',
        'arrival_airport_id',
        'departure_time',
        'arrival_time',
        'duration',
        'aircraft_id',
        'price',
        'status',
        'available_seats',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['departure_time', 'arrival_time'];

    /**
     * Relationships
     */

    /**
     * A flight belongs to a departure airport.
     *
     * @return BelongsTo
     */
    public function departureAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    /**
     * A flight belongs to an arrival airport.
     *
     * @return BelongsTo
     */
    public function arrivalAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }

    /**
     * A flight belongs to an aircraft.
     *
     * @return BelongsTo
     */
    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    /**
     * A flight has many bookings.
     *
     * @return HasMany
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * A flight has many seats.
     *
     * @return HasMany
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * A flight has one flight schedule.
     *
     * @return HasOne (consider using BelongsTo depending on your implementation)
     */
    public function schedule(): HasOne
    {
        return $this->hasOne(FlightSchedule::class); // Consider BelongsTo if a flight can have multiple schedules
    }

    /**
     * A flight has many payments.
     *
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Accessors and Methods
     */

    /**
     * This accessor method creates a new attribute named `formatted_departure_time`
     * by formatting the departure_time attribute in a user-friendly format (e.g., Y-m-d H:i).
     *
     * @return string
     */
    public function getFormattedDepartureTimeAttribute()
    {
        return $this->departure_time->format('Y-m-d H:i');
    }

    /**
     * This accessor method creates a new attribute named `formatted_arrival_time`
     * by formatting the arrival_time attribute in a user-friendly format (e.g., Y-m-d H:i).
     *
     * @return string
     */
    public function getFormattedArrivalTimeAttribute()
    {
        return $this->arrival_time->format('Y-m-d H:i');
    }

    /**
     * This method calculates the total flight duration in hours and minutes.
     *
     * @return string
     */
    public function getFlightDurationAttribute()
    {
        $duration = strtotime($this->arrival_time) - strtotime($this->departure_time);
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        return sprintf('%dh %dm', $hours, $minutes);
    }

    /**
     * This method checks if the flight is full.
     *
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->available_seats <= 0;
    }
}