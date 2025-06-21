<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SolutionFeedback extends Model
{
    use HasFactory;

    protected $fillable = ['solution_id', 'user_id', 'liked', 'rating', 'feedback'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function solution()
    {
        return $this->belongsTo(Solution::class);
    }
}
