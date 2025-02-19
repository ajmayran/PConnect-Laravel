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
        'profile_picture', // Assuming this is the column for the profile picture
        'bir_image', // Add this field to the fillable array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}