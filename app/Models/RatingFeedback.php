<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RatingFeedback extends Model
{
    use HasFactory;

    protected $fillable = ['expert_id', 'user_id', 'liked', 'rating', 'feedback'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
