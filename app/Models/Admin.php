<?php

namespace App\Models;


use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use  HasFactory, Notifiable, SoftDeletes, Uuids;

    protected $guard = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_name'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'enabled' => 'boolean'
    ];

    /**
     * Get the admin's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name}";
    }


}