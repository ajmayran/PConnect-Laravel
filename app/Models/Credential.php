<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'file_path', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}