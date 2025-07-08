<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = ['company', 'model', 'year'];

    public function problems()
    {
        return $this->hasMany(Problem::class);
    }

    public function clients()
    {
        return $this->belongsToMany(User::class, 'client_cars')->withTimestamps();
    }

    
}
