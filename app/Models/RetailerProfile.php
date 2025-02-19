<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerProfile extends Model
{
    protected $table = 'retailer_profiles'; // Ensure this matches your database table name

    protected $fillable = [
        'user_id',
        'business_name',
        'phone',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}