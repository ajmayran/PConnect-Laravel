<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Yajra\Address\Entities\Barangay;
use Illuminate\Database\Eloquent\Model;

class RetailerProfile extends Model
{
    protected $table = 'retailer_profile';

    protected $fillable = [
        'user_id',
        'business_name',
        'phone',
        'region',
        'city',
        'province',
        'barangay',
        'street',
        'profile_picture',
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
