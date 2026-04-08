<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'status',
        'course_id',
    ];

    protected $casts = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function materials()
    {
        return $this->hasMany(LectureMaterial::class);
    }

    public function views()
    {
        return $this->hasMany(LectureView::class);
    }
}
