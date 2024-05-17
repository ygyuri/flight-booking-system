<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'flight_id',
        'passenger_count',
        'seat_id',
        'total_price',
        'status',
        'payment_status',
        'booking_date',
        'booking_reference',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'booking_date' => 'datetime',
    ];

    /**
     * Relationships
     */

    /**
     * A booking belongs to a customer.
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * A booking belongs to a flight.
     *
     * @return BelongsTo
     */
    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    /**
     * A booking belongs to a seat.
     *
     * @return BelongsTo
     */
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }
}