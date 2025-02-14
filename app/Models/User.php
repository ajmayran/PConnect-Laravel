<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Add this line
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'credentials',
        'email',
        'password',
        'user_type',
        'facebook_id',
        'google_id',
        'status',
        'profile_completed',
    ];

    protected $casts = [
        'profile_completed' => 'boolean'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    public function distributor(): HasOne
    {
        return $this->hasOne(Distributors::class, 'user_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function credential()
    {
        return $this->hasOne(Credential::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'distributor_id');
    }

    public function retailerProfile()
    {
        return $this->hasOne(RetailerProfile::class);
    }
}
