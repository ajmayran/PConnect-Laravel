<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'distributor_id',
        'rating',
        'review'
    ];

    /**
     * Get the user who wrote this review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the distributor being reviewed.
     */
    public function distributor()
    {
        return $this->belongsTo(Distributors::class);
    }
}