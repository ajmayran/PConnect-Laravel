<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Distributors extends Model
{
    protected $fillable = [
        'user_id',
        'company_profile_image',
        'company_name',
        'company_email',
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
