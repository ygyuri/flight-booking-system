<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'payment_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payment_date' => 'datetime',
    ];

    /**
     * Relationships
     */

    /**
     * A payment belongs to a booking.
     *
     * @return BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}