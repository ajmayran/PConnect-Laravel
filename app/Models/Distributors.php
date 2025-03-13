<?php

namespace App\Models;

use Illuminate\Database\Eloque
use Illuminate\Support\Facades\Storage;
ase\Eloquent\Relations\Has
use Illuminate\Database\Elo
class Distributors extends Model
{
  
        'company_name',
        'company_email',
        'company_address',
        'bir_form',
        'sec_document',
        'profile_completed',
        'region',
        'province',
        'city',
        'barangay',
        'street',
        'company_phone_number'
    ];
  
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
}
