<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_url',
        'file_size',
        'file_type',
        'lecture_id',
    ];

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }
}
