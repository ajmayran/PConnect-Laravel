<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyRetailerEmail;
use App\Notifications\VerifyDistributorEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
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
        'email',
        'password',
        'user_type',
        'status',
        'profile_completed',
        'rejection_reason',
        'has_seen_subscription_page',
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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function credential()
    {
        return $this->hasMany(Credential::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function retailerProfile()
    {
        return $this->hasOne(RetailerProfile::class, 'user_id');
    }
    public function retailers()
    {
        return $this->hasOne(retailers::class, 'user_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function credentials()
    {
        return $this->hasMany(Credential::class, 'user_id', 'id');
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        if ($this->user_type === 'distributor') {
            $this->notify(new VerifyDistributorEmail($this));
        } else if ($this->user_type === 'retailer') {
            $this->notify(new VerifyRetailerEmail($this));
        } else {
            parent::sendEmailVerificationNotification();
        }
    }

    public function followedDistributors()
    {
        return $this->hasMany(DistributorFollower::class, 'retailer_id')
            ->with('distributor');
    }

    public function isFollowing($distributorId)
    {
        return $this->followedDistributors()
            ->where('distributor_id', $distributorId)
            ->exists();
    }

    public function blockedRetailers()
    {
        return $this->hasMany(BlockedRetailer::class, 'distributor_id');
    }

    public function blockedByDistributors()
    {
        return $this->hasMany(BlockedRetailer::class, 'retailer_id');
    }

    public function blockedMessages()
    {
        return $this->hasMany(BlockedMessage::class, 'retailer_id');
    }

    public function blockedMessagesBy()
    {
        return $this->hasMany(BlockedMessage::class, 'distributor_id');
    }
}
