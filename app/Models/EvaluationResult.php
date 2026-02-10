<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'score',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getMcScoreAttribute()
    {
        $answers = EvaluationAnswer::where('user_id', $this->user_id)
            ->whereHas('evaluation', function ($query) {
                $query->where('type', 'multiple_choice');
            })->get();

        if ($answers->count() == 0) return 0;
        return round($answers->avg('score'));
    }

    public function getEssayScoreAttribute()
    {
        $answers = EvaluationAnswer::where('user_id', $this->user_id)
            ->whereHas('evaluation', function ($query) {
                $query->where('type', 'essay');
            })->get();

        if ($answers->count() == 0) return 0;
        return round($answers->avg('score'));
    }
}
