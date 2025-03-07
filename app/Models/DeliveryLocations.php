<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DeliveryLocations extends Model
{
    protected $fillable = [
        'region',
        'province',
        'city',
        'barangay',
        'street',
        'truck_id' 
    ];

    public function trucks()
    {
        return $this->belongsTo(Trucks::class, 'truck_id');
    }

    public function getBarangayNameAttribute()
    {
        if (!$this->barangay) {
            return null;
        }

        static $barangays = [];

        if (!isset($barangays[$this->barangay])) {
            $barangayRecord = DB::table('barangays')
                ->where('code', $this->barangay)
                ->first();

            $barangays[$this->barangay] = $barangayRecord ? $barangayRecord->name : $this->barangay;
        }

        return $barangays[$this->barangay];
    }
}
