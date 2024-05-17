<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Flight; // Import the Flight Model

class Aircraft extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'manufacturer',
        'registration_number',
        'sitting_capacity',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the flights associated with the aircraft.
     *
     * @return HasMany
     */
    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class);
    }

    /**
     * This accessor method creates a new attribute named `model_information` by combining
     * the aircraft name and manufacturer. This provides a more descriptive way to
     * represent the aircraft model.
     */
    public function getModelInformationAttribute()
    {
        return sprintf('%s (%s)', $this->name, $this->manufacturer);
    }
}