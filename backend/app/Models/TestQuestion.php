<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'type',
        'options',
        'correct_answer',
        'points',
        'test_id',
    ];

    protected $casts = [
        'options' => 'array',
        'points' => 'integer',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
