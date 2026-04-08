<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lecture;
use App\Models\LectureView;
use App\Models\TestResult;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * POST /api/courses/{id}/enroll
     */
    public function enroll(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        if ($course->status !== 'published') {
            abort(403, 'Курс недоступен для записи');
        }

        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $request->user()->id, 'course_id' => $courseId],
            ['enrolled_at' => now()]
        );

        return response()->json([
            'message' => 'Вы успешно записались на курс',
            'data' => [
                'enrolled'    => true,
                'enrolled_at' => $enrollment->enrolled_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * DELETE /api/courses/{id}/unenroll
     */
    public function unenroll(Request $request, $courseId)
    {
        Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $courseId)
            ->delete();

        return response()->json(['message' => 'Вы отписались от курса']);
    }

    /**
     * GET /api/courses/{id}/enrollment  — check enrollment status
     */
    public function checkEnrollment(Request $request, $courseId)
    {
        $enrollment = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return response()->json(['enrolled' => false]);
        }

        $course = Course::withCount('lectures')->findOrFail($courseId);
        $viewed = LectureView::where('user_id', $request->user()->id)
            ->whereHas('lecture', fn($q) => $q->where('course_id', $courseId))
            ->count();

        $progress = $course->lectures_count > 0
            ? round(($viewed / $course->lectures_count) * 100)
            : 0;

        return response()->json([
            'enrolled'         => true,
            'enrolled_at'      => $enrollment->enrolled_at->toIso8601String(),
            'last_accessed_at' => $enrollment->last_accessed_at?->toIso8601String(),
            'completed_at'     => $enrollment->completed_at?->toIso8601String(),
            'progress'         => $progress,
            'viewed_lectures'  => $viewed,
            'total_lectures'   => $course->lectures_count,
        ]);
    }

    /**
     * GET /api/my/courses  — enrolled courses with progress
     */
    public function myCourses(Request $request)
    {
        $userId = $request->user()->id;

        $enrollments = Enrollment::where('user_id', $userId)
            ->with(['course' => fn($q) => $q->with('author:id,name')->withCount(['lectures', 'tests'])])
            ->orderByDesc('last_accessed_at')
            ->orderByDesc('enrolled_at')
            ->get();

        // Batch progress calculation
        $courseIds = $enrollments->pluck('course_id');

        $lectureMap = Lecture::whereIn('course_id', $courseIds)
            ->pluck('course_id', 'id'); // lectureId => courseId

        $viewedLectureIds = LectureView::where('user_id', $userId)
            ->whereIn('lecture_id', $lectureMap->keys())
            ->pluck('lecture_id');

        $viewedPerCourse = [];
        foreach ($viewedLectureIds as $lectureId) {
            $cId = $lectureMap[$lectureId] ?? null;
            if ($cId) $viewedPerCourse[$cId] = ($viewedPerCourse[$cId] ?? 0) + 1;
        }

        $result = $enrollments->map(function ($enrollment) use ($viewedPerCourse) {
            $course = $enrollment->course;
            if (!$course) return null;

            $total    = $course->lectures_count;
            $viewed   = $viewedPerCourse[$course->id] ?? 0;
            $progress = $total > 0 ? round(($viewed / $total) * 100) : 0;

            return [
                'id'               => $course->id,
                'title'            => $course->title,
                'description'      => $course->description,
                'status'           => $course->status,
                'author'           => $course->author ? ['id' => $course->author->id, 'name' => $course->author->name] : null,
                'lectures_count'   => $total,
                'tests_count'      => $course->tests_count,
                'enrolled_at'      => $enrollment->enrolled_at->toIso8601String(),
                'last_accessed_at' => $enrollment->last_accessed_at?->toIso8601String(),
                'completed_at'     => $enrollment->completed_at?->toIso8601String(),
                'progress'         => $progress,
                'viewed_lectures'  => $viewed,
            ];
        })->filter()->values();

        return response()->json(['data' => $result]);
    }

    /**
     * GET /api/my/dashboard  — student dashboard data
     */
    public function myDashboard(Request $request)
    {
        $userId = $request->user()->id;

        $enrollments = Enrollment::where('user_id', $userId)
            ->with(['course:id,title,description,status'])
            ->orderByDesc('last_accessed_at')
            ->get();

        $courseIds = $enrollments->pluck('course_id');

        // Progress per course
        $lectureMap = Lecture::whereIn('course_id', $courseIds)->pluck('course_id', 'id');
        $viewedIds  = LectureView::where('user_id', $userId)
            ->whereIn('lecture_id', $lectureMap->keys())->pluck('lecture_id');
        $viewedPerCourse = [];
        foreach ($viewedIds as $lid) {
            $cId = $lectureMap[$lid] ?? null;
            if ($cId) $viewedPerCourse[$cId] = ($viewedPerCourse[$cId] ?? 0) + 1;
        }

        $totalLecturesPerCourse = Lecture::whereIn('course_id', $courseIds)
            ->selectRaw('course_id, count(*) as cnt')
            ->groupBy('course_id')
            ->pluck('cnt', 'course_id');

        // Build active courses with progress (top 4)
        $activeCourses = $enrollments->filter(fn($e) => !$e->completed_at)->take(4)->map(function ($e) use ($viewedPerCourse, $totalLecturesPerCourse) {
            $course  = $e->course;
            $total   = $totalLecturesPerCourse[$e->course_id] ?? 0;
            $viewed  = $viewedPerCourse[$e->course_id] ?? 0;
            $progress = $total > 0 ? round(($viewed / $total) * 100) : 0;
            return [
                'id'               => $course->id,
                'title'            => $course->title,
                'description'      => $course->description,
                'progress'         => $progress,
                'viewed_lectures'  => $viewed,
                'total_lectures'   => $total,
                'last_accessed_at' => $e->last_accessed_at?->toIso8601String(),
                'enrolled_at'      => $e->enrolled_at->toIso8601String(),
            ];
        })->values();

        // Recent test results
        $recentResults = TestResult::where('user_id', $userId)
            ->with('test:id,title,course_id')
            ->orderByDesc('completed_at')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'test_id'      => $r->test_id,
                'test_title'   => $r->test?->title,
                'course_id'    => $r->test?->course_id,
                'score'        => $r->score,
                'total_points' => $r->total_points,
                'percentage'   => $r->percentage,
                'passed'       => $r->percentage >= 70,
                'completed_at' => $r->completed_at?->toIso8601String(),
            ]);

        $avgScore = $recentResults->avg('percentage') ?: 0;

        // Continue learning (last accessed with unfinished progress)
        $continueLearning = $enrollments
            ->filter(fn($e) => !$e->completed_at)
            ->sortByDesc('last_accessed_at')
            ->first();

        return response()->json([
            'stats' => [
                'enrolled_courses' => $enrollments->count(),
                'in_progress'      => $enrollments->filter(fn($e) => !$e->completed_at)->count(),
                'completed'        => $enrollments->filter(fn($e) => !!$e->completed_at)->count(),
                'avg_test_score'   => round($avgScore),
                'test_attempts'    => TestResult::where('user_id', $userId)->count(),
            ],
            'continue_learning' => $continueLearning ? [
                'course_id'        => $continueLearning->course_id,
                'course_title'     => $continueLearning->course?->title,
                'progress'         => $viewedPerCourse[$continueLearning->course_id] ?? 0,
                'total_lectures'   => $totalLecturesPerCourse[$continueLearning->course_id] ?? 0,
                'last_accessed_at' => $continueLearning->last_accessed_at?->toIso8601String(),
            ] : null,
            'active_courses' => $activeCourses,
            'recent_results' => $recentResults,
        ]);
    }
}
