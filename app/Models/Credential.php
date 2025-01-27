<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    protected $fillable = ['user_id', 'file_path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
