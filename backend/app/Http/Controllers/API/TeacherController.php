<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lecture;
use App\Models\LectureView;
use App\Models\Test;
use App\Models\TestResult;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function stats(Request $request)
    {
        $user = $request->user();

        $courses = Course::where('author_id', $user->id)
            ->withCount(['lectures', 'tests', 'enrollments'])
            ->orderBy('created_at', 'desc')
            ->get();

        $courseIds = $courses->pluck('id');

        $enrolledStudents = Enrollment::whereIn('course_id', $courseIds)
            ->distinct('user_id')
            ->count('user_id');

        $recentLectures = Lecture::whereIn('course_id', $courseIds)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'course_id', 'status', 'created_at']);

        $recentTests = Test::whereIn('course_id', $courseIds)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'course_id', 'status', 'created_at']);

        // Build unified recent activity (courses + lectures + tests), last 10
        $recentActivity = collect();
        foreach ($courses->take(3) as $c) {
            $recentActivity->push([
                'type'        => 'course',
                'title'       => 'Курс создан',
                'description' => $c->title,
                'created_at'  => $c->created_at->toIso8601String(),
            ]);
        }
        foreach ($recentLectures as $l) {
            $recentActivity->push([
                'type'        => 'lecture',
                'title'       => 'Добавлена лекция',
                'description' => $l->title,
                'created_at'  => $l->created_at->toIso8601String(),
            ]);
        }
        foreach ($recentTests as $t) {
            $recentActivity->push([
                'type'        => 'test',
                'title'       => 'Создан тест',
                'description' => $t->title,
                'created_at'  => $t->created_at->toIso8601String(),
            ]);
        }
        $recentActivity = $recentActivity
            ->sortByDesc('created_at')
            ->values()
            ->take(10)
            ->map(fn($a, $i) => array_merge($a, ['id' => $i + 1]));

        return response()->json([
            'stats' => [
                'courses'           => $courses->count(),
                'published_courses' => $courses->where('status', 'published')->count(),
                'lectures'          => $courses->sum('lectures_count'),
                'tests'             => $courses->sum('tests_count'),
                'enrolled_students' => $enrolledStudents,
            ],
            'recent_courses' => $courses->take(5)->map(fn($c) => [
                'id'               => $c->id,
                'title'            => $c->title,
                'status'           => $c->status,
                'lectures_count'   => $c->lectures_count,
                'tests_count'      => $c->tests_count,
                'enrollments_count'=> $c->enrollments_count,
                'created_at'       => $c->created_at->toIso8601String(),
            ]),
            'recent_activity' => $recentActivity,
        ]);
    }

    public function courseStudents(Request $request, $courseId)
    {
        $user = $request->user();
        $course = Course::findOrFail($courseId);

        // Only the author or admin can see students
        if ($course->author_id !== $user->id && !$user->hasRole(['admin', 'super-admin'])) {
            abort(403);
        }

        $lectures = Lecture::where('course_id', $courseId)
            ->orderBy('order')
            ->get(['id', 'title', 'order']);

        $totalLectures = $lectures->count();
        $lectureIds = $lectures->pluck('id');

        $enrollments = Enrollment::where('course_id', $courseId)
            ->with('user:id,name,email')
            ->orderBy('enrolled_at', 'desc')
            ->get();

        // Batch load lecture views for all enrolled students
        $viewCounts = LectureView::whereIn('lecture_id', $lectureIds)
            ->whereIn('user_id', $enrollments->pluck('user_id'))
            ->selectRaw('user_id, COUNT(*) as cnt')
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        // Batch load best test results per user for this course
        $testIds = Test::where('course_id', $courseId)->pluck('id');
        $testResults = TestResult::whereIn('test_id', $testIds)
            ->whereIn('user_id', $enrollments->pluck('user_id'))
            ->selectRaw('user_id, MAX(percentage) as best_pct, COUNT(*) as attempts')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $students = $enrollments->map(fn($e) => [
            'user_id'         => $e->user_id,
            'name'            => $e->user?->name ?? 'Unknown',
            'email'           => $e->user?->email ?? '',
            'enrolled_at'     => $e->enrolled_at?->toIso8601String(),
            'last_accessed_at'=> $e->last_accessed_at?->toIso8601String(),
            'completed_at'    => $e->completed_at?->toIso8601String(),
            'lectures_viewed' => (int) ($viewCounts[$e->user_id] ?? 0),
            'total_lectures'  => $totalLectures,
            'progress'        => $totalLectures > 0
                ? round((($viewCounts[$e->user_id] ?? 0) / $totalLectures) * 100)
                : 0,
            'best_test_score' => isset($testResults[$e->user_id])
                ? round($testResults[$e->user_id]->best_pct, 1)
                : null,
            'test_attempts'   => $testResults[$e->user_id]->attempts ?? 0,
        ]);

        return response()->json([
            'course'   => ['id' => $course->id, 'title' => $course->title],
            'students' => $students,
            'summary'  => [
                'total'     => $enrollments->count(),
                'completed' => $enrollments->whereNotNull('completed_at')->count(),
                'avg_progress' => $enrollments->count() > 0
                    ? round($students->avg('progress'))
                    : 0,
            ],
        ]);
    }

    public function myCourses(Request $request)
    {
        $user = $request->user();

        $courses = Course::where('author_id', $user->id)
            ->withCount(['lectures', 'tests', 'enrollments'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($c) => [
                'id'                => $c->id,
                'title'             => $c->title,
                'description'       => $c->description,
                'status'            => $c->status,
                'lectures_count'    => $c->lectures_count,
                'tests_count'       => $c->tests_count,
                'enrollments_count' => $c->enrollments_count,
                'created_at'        => $c->created_at->toIso8601String(),
                'updated_at'        => $c->updated_at->toIso8601String(),
            ]);

        return response()->json(['data' => $courses]);
    }
}
