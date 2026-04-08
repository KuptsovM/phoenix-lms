<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LectureView extends Model
{
    protected $fillable = [
        'user_id',
        'lecture_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }
}
