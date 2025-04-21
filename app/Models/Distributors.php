<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Distributors extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_email',
        'company_phone_number',
        'profile_completed',
        'region',
        'province',
        'city',
        'barangay',
        'street',
        'company_profile_image',
        'cut_off_time',
    ];


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
                
                // If found by name, we should update the record to use the code instead
                if ($barangay) {
                    \Illuminate\Support\Facades\Log::info("Barangay found by name: {$this->barangay}, code should be: {$barangay->code}");
                    // You could update the record here if needed
                }
            }
            
            $barangays[$this->barangay] = $barangay ? $barangay->name : $this->barangay;
        }
    
        return $barangays[$this->barangay];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'distributor_id');
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo
            ? Storage::url($this->company_profile_image)
            : asset('img/default-profile.png');
    }

    public function trucks()
    {
        return $this->hasMany(Trucks::class, 'distributor_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'distributor_id');
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }

    public function credentials()
    {
        return $this->hasMany(Credential::class, 'user_id', 'id');
    }

    public function followers()
    {
        return $this->hasMany(DistributorFollower::class, 'distributor_id');
    }

    public function getFollowersCountAttribute()
    {
        return $this->followers->count();
    }

    public function getFormattedCutOffTimeAttribute()
    {
        if (!$this->cut_off_time) {
            return 'Not set';
        }

        return date('h:i A', strtotime($this->cut_off_time));
    }
}
