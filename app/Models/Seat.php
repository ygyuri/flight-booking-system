<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Seat extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'seat_number',
        'seat_class',
        'availability_status',
        'price',
        'flight_id',
    ];

    /**
     * Relationships
     */

    /**
     * A seat belongs to a flight.
     *
     * @return BelongsTo
     */
    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    /**
     * A seat can have one booking (optional).
     *
     * @return HasOne
     */
    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }

    /**
     * Accessors and Methods
     */

    /**
     * Accessor: Get the formatted price of the seat.
     *
     * @return string
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Method: Reserve or book the seat depending on the availability status.
     *
     * @param bool $book
     * @return bool
     */
    public function reserveOrBook(bool $book = false): bool
    {
        if ($this->availability_status === 'available' || ($this->availability_status === 'reserved' && $book)) {
            $this->availability_status = $book ? 'booked' : 'reserved';
            return $this->save();
        }
        return false;
    }

    /**
     * Method: Check if the seat is available.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->availability_status === 'available';
    }

    /**
     * Method: Check if the seat is reserved.
     *
     * @return bool
     */
    public function isReserved(): bool
    {
        return $this->availability_status === 'reserved';
    }

    /**
     * Method: Check if the seat is booked.
     *
     * @return bool
     */
    public function isBooked(): bool
    {
        return $this->availability_status === 'booked';
    }
}