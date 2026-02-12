<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'score',
        'mc_score',
        'essay_score',
        'sub_categories',
        'completed_at',
        'archived_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
