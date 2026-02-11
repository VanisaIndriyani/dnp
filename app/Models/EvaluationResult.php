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
        $userDivision = $this->user->division;
        $answers = EvaluationAnswer::where('user_id', $this->user_id)
            ->whereHas('evaluation', function ($query) use ($userDivision) {
                $query->withTrashed(); // Include soft deleted questions
                $query->where('type', 'multiple_choice');
                if ($userDivision) {
                    $query->where('category', $userDivision);
                } else {
                    $query->whereNull('category');
                }
            })->get();

        if ($answers->count() == 0) return 0;
        return round($answers->avg('score'));
    }

    public function getEssayScoreAttribute()
    {
        $userDivision = $this->user->division;
        $answers = EvaluationAnswer::where('user_id', $this->user_id)
            ->whereHas('evaluation', function ($query) use ($userDivision) {
                $query->withTrashed(); // Include soft deleted questions
                $query->where('type', 'essay');
                if ($userDivision) {
                    $query->where('category', $userDivision);
                } else {
                    $query->whereNull('category');
                }
            })->get();

        if ($answers->count() == 0) return 0;
        return round($answers->avg('score'));
    }
}
