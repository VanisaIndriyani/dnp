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

    public function answers()
    {
        return $this->hasMany(EvaluationAnswer::class, 'user_id', 'user_id');
    }

    public function getMcScoreAttribute()
    {
        $answers = $this->answers()->whereHas('evaluation', function ($query) {
            $query->withTrashed(); // Include soft deleted questions
            $query->where('type', 'multiple_choice');
        })->get();

        if ($answers->count() == 0) return 0;
        return round($answers->avg('score'));
    }

    public function getEssayScoreAttribute()
    {
        $answers = $this->answers()->whereHas('evaluation', function ($query) {
            $query->withTrashed(); // Include soft deleted questions
            $query->where('type', 'essay');
        })->get();

        if ($answers->count() == 0) return 0;
        return round($answers->avg('score'));
    }

    public function getSubCategoriesAttribute()
    {
        return $this->answers()
            ->with('evaluation')
            ->get()
            ->pluck('evaluation.sub_category')
            ->unique()
            ->filter()
            ->implode(', ');
    }
}
