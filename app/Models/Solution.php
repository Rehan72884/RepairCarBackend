<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Solution extends Model
{
    use HasFactory;

    protected $fillable = [
        'problem_id',
        'title',
        'description',
        'expert_id',
    ];

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }

    public function steps()
    {
        return $this->hasMany(Step::class)->orderBy('order');
    }
    public function expert()
    {
        return $this->belongsTo(User::class, 'expert_id');
    }
}