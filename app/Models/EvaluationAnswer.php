<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'evaluation_result_id',
        'evaluation_id',
        'answer',
        'score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function result()
    {
        return $this->belongsTo(EvaluationResult::class, 'evaluation_result_id');
    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }
}
