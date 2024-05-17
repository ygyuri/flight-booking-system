<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightSchedule extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'flight_id',
        'departure_time',
        'arrival_time',
        'status',
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
     * A flight schedule belongs to a flight.
     *
     * @return BelongsTo
     */
    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    /**
     * Accessors and Methods
     */

    /**
     * Method: Check if the flight schedule is on time.
     *
     * @return bool
     */
    public function isOnTime(): bool
    {
        return $this->status === 'scheduled';
    }
}