<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['author', 'lectures', 'tests']);
        
        // Фильтрация по статусу
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $courses = $query->get()->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'status' => $course->status,
                'author' => [
                    'id' => $course->author->id,
                    'name' => $course->author->name,
                ],
                'lectures_count' => $course->lectures->count(),
                'tests_count' => $course->tests->count(),
                'created_at' => $course->created_at,
                'updated_at' => $course->updated_at,
            ];
        });

        return response()->json($courses);
    }

    public function show($id)
    {
        $course = Course::with(['author', 'lectures', 'tests'])->findOrFail($id);
        
        return response()->json([
            'id' => $course->id,
            'title' => $course->title,
            'description' => $course->description,
            'status' => $course->status,
            'author' => [
                'id' => $course->author->id,
                'name' => $course->author->name,
            ],
            'lectures' => $course->lectures->map(function ($lecture) {
                return [
                    'id' => $lecture->id,
                    'title' => $lecture->title,
                    'description' => $lecture->description,
                    'content' => $lecture->content,
                    'status' => $lecture->status,
                ];
            }),
            'tests' => $course->tests->map(function ($test) {
                return [
                    'id' => $test->id,
                    'title' => $test->title,
                    'description' => $test->description,
                    'questions_count' => $test->questions_count ?? 10,
                    'duration' => $test->duration ?? 30,
                    'difficulty' => $test->difficulty ?? 'medium',
                ];
            }),
            'created_at' => $course->created_at,
            'updated_at' => $course->updated_at,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:draft,published,archived',
        ]);

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'author_id' => $request->user()->id,
            'slug' => \Str::slug($request->title),
        ]);

        return response()->json($course, 201);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:draft,published,archived',
        ]);

        $course->update($request->all());

        return response()->json($course);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json(['message' => 'Курс удален']);
    }
}
