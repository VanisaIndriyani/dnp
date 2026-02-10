<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'is_approved',
        'approved_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
