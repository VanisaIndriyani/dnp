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
        'passing_grade',
        'sub_categories',
        'status',
        'is_published',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(EvaluationAnswer::class, 'evaluation_result_id');
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

    public function getSubCategoriesAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        return $this->answers()
            ->with(['evaluation' => function ($query) {
                $query->withTrashed();
            }])
            ->get()
            ->pluck('evaluation.sub_category')
            ->unique()
            ->filter()
            ->implode(', ');
    }
}
