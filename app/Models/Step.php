<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Step extends Model
{
    use HasFactory;

    protected $fillable = [
        'solution_id',
        'description',
        'image',
        'order',
    ];

    public function solution()
    {
        return $this->belongsTo(Solution::class);
    }
}
