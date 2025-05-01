<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    protected $fillable = [
        'addressable_id',
        'addressable_type',
        'region',
        'province',
        'city',
        'barangay',
        'street',
        'is_default',
        'label'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $appends = ['barangay_name', 'formatted_address'];

    /**
     * Get the parent addressable model.
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the barangay name from the code.
     */
    public function getBarangayNameAttribute()
    {
        if (!$this->barangay) {
            return 'N/A';
        }

        static $barangays = [];

        if (!isset($barangays[$this->barangay])) {
            $barangay = DB::table('barangays')->where('code', $this->barangay)->first();
            if (!$barangay) {
                $barangay = DB::table('barangays')->where('name', $this->barangay)->first();
            }
            
            $barangays[$this->barangay] = $barangay ? $barangay->name : $this->barangay;
        }

        return $barangays[$this->barangay];
    }

    /**
     * Get full formatted address.
     */
    public function getFormattedAddressAttribute()
    {
        $parts = [];
        
        if ($this->barangay) {
            $parts[] = $this->barangay_name;
        }
        
        if ($this->street) {
            $parts[] = $this->street;
        }
        
        return implode(', ', $parts);
    }

    protected function getBarangayName()
    {
        if (!$this->barangay) {
            return 'Unknown';
        }

        $barangay = DB::table('barangays')
            ->where('code', $this->barangay)
            ->first();
            
        return $barangay ? $barangay->name : 'Unknown';
    }
}