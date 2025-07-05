<?php

namespace App\Models;

use App\Models\Car;
use App\Models\User;
use App\Models\Solution;
use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    protected $fillable = ['car_id', 'title', 'description', 'client_id','assigned_expert_id','status'];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function solutions()
    {
        return $this->hasMany(Solution::class);
    }
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function assignedExpert()
    {
        return $this->belongsTo(User::class, 'assigned_expert_id');
    }

}
