<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    protected $fillable = ['car_id', 'title', 'description'];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
