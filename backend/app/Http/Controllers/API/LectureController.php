<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    public function index($courseId)
    {
        $lectures = Lecture::where('course_id', $courseId)
            ->withCount('materials')
            ->get()
            ->map(function ($lecture) {
            return [
                'id' => $lecture->id,
                'title' => $lecture->title,
                'description' => $lecture->description,
                'content' => $lecture->content,
                'status' => $lecture->status,
                'course_id' => $lecture->course_id,
                'materials_count' => $lecture->materials_count,
            ];
        });

        return response()->json($lectures);
    }

    public function show($id)
    {
        $lecture = Lecture::with(['materials'])->findOrFail($id);
        
        return response()->json([
            'id' => $lecture->id,
            'title' => $lecture->title,
            'description' => $lecture->description,
            'content' => $lecture->content,
            'status' => $lecture->status,
            'course_id' => $lecture->course_id,
            'materials' => $lecture->materials->map(function ($material) {
                return [
                    'id' => $material->id,
                    'title' => $material->title,
                    'file_url' => $material->file_url,
                    'file_size' => $material->file_size,
                    'file_type' => $material->file_type,
                ];
            }),
        ]);
    }

    public function materials($lectureId)
    {
        $lecture = Lecture::with(['materials'])->findOrFail($lectureId);
        
        $materials = $lecture->materials->map(function ($material) {
            return [
                'id' => $material->id,
                'title' => $material->title,
                'file_url' => $material->file_url,
                'file_size' => $material->file_size,
                'file_type' => $material->file_type,
            ];
        });

        return response()->json($materials);
    }
}
