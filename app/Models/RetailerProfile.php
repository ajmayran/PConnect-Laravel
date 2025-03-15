<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Yajra\Address\Entities\Barangay;
use Illuminate\Database\Eloquent\Model;

class RetailerProfile extends Model
{
    protected $table = 'retailer_profiles'; // Ensure this matches your database table name

    protected $fillable = [
        'user_id',
        'business_name',
        'phone',
        'address',
        'profile_picture', 
        'bir_image', 
        'region',
        'city',
        'province',
        'barangay',
        'street',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
  
    public function getBarangayNameAttribute()
    {
        if (!$this->barangay) {
            return 'N/A';
        }


        static $barangays = [];

        if (!isset($barangays[$this->barangay])) {
            $barangay = DB::table('barangays')->where('code', $this->barangay)->first();
            $barangays[$this->barangay] = $barangay ? $barangay->name : 'Unknown';
        }

        return $barangays[$this->barangay];
    }
}