<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'difficulty',
        'status',
        'questions_count',
        'course_id',
    ];

    protected $casts = [
        'duration' => 'integer',
        'questions_count' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(TestQuestion::class);
    }

    public function results()
    {
        return $this->hasMany(TestResult::class);
    }

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class);
    }
}
