<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProblem extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'car_id',
        'title',
        'description',
        'status',
        'assigned_expert_id',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function expert()
    {
        return $this->belongsTo(User::class, 'assigned_expert_id');
    }
}

