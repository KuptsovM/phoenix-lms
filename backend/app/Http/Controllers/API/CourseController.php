<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lecture;
use App\Models\LectureView;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $perPage = min($request->input('per_page', 15), 50);
        // Optional auth: works both for guest and authenticated requests
        $user = auth('sanctum')->user();
        
        $query = Course::with(['author:id,name,email'])
            ->withCount(['lectures', 'tests'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->search, fn($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->orderBy('created_at', 'desc');

        if (!$user) {
            $query->where('status', 'published');
        } elseif (!$request->status && !$user->can('manage courses')) {
            $query->where(function ($builder) use ($user) {
                $builder->where('status', 'published')
                    ->orWhere('author_id', $user->id);
            });
        }

        $courses = $query->paginate($perPage);

        // Batch enrollment + progress for authenticated user
        $enrollmentMap   = [];
        $progressMap     = [];
        if ($user) {
            $courseIds = $courses->pluck('id');

            $enrollments = Enrollment::where('user_id', $user->id)
                ->whereIn('course_id', $courseIds)
                ->get()->keyBy('course_id');

            if ($enrollments->count()) {
                $lectureMap = Lecture::whereIn('course_id', $enrollments->keys())
                    ->pluck('course_id', 'id');
                $viewedIds  = LectureView::where('user_id', $user->id)
                    ->whereIn('lecture_id', $lectureMap->keys())
                    ->pluck('lecture_id');
                $viewedPerCourse = [];
                foreach ($viewedIds as $lid) {
                    $cId = $lectureMap[$lid] ?? null;
                    if ($cId) $viewedPerCourse[$cId] = ($viewedPerCourse[$cId] ?? 0) + 1;
                }
                foreach ($enrollments as $cId => $enrollment) {
                    $enrollmentMap[$cId] = true;
                    $total = 0;
                    foreach ($courses as $c) {
                        if ($c->id == $cId) { $total = $c->lectures_count; break; }
                    }
                    $progressMap[$cId] = $total > 0 ? round((($viewedPerCourse[$cId] ?? 0) / $total) * 100) : 0;
                }
            }
        }

        return response()->json([
            'data' => $courses->map(fn($course) => [
                'id'             => $course->id,
                'title'          => $course->title,
                'slug'           => $course->slug,
                'description'    => $course->description,
                'status'         => $course->status,
                'is_featured'    => $course->is_featured,
                'author'         => $course->author ? ['id' => $course->author->id, 'name' => $course->author->name] : null,
                'lectures_count' => $course->lectures_count,
                'tests_count'    => $course->tests_count,
                'enrolled'       => $enrollmentMap[$course->id] ?? false,
                'progress'       => $progressMap[$course->id] ?? null,
                'published_at'   => $course->published_at?->toIso8601String(),
                'created_at'     => $course->created_at->toIso8601String(),
                'updated_at'     => $course->updated_at->toIso8601String(),
            ]),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ]
        ]);
    }

    public function show(Request $request, $id)
    {
        $course = Course::with(['author:id,name,email', 'lectures', 'tests'])->findOrFail($id);

        // Optional auth: works for both guest and authenticated requests
        $user = auth('sanctum')->user();

        if ($course->status !== 'published') {
            if (!$user) abort(404);
            if (!$this->canManageCourse($user, $course)) abort(403);
        }

        // Enrollment + progress for the current user
        $enrollmentData    = ['enrolled' => false, 'progress' => 0, 'viewed_lecture_ids' => []];
        if ($user) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)->first();
            if ($enrollment) {
                $viewedIds = LectureView::where('user_id', $user->id)
                    ->whereHas('lecture', fn($q) => $q->where('course_id', $course->id))
                    ->pluck('lecture_id')->toArray();
                $total    = $course->lectures->count();
                $progress = $total > 0 ? round((count($viewedIds) / $total) * 100) : 0;
                $enrollmentData = [
                    'enrolled'           => true,
                    'enrolled_at'        => $enrollment->enrolled_at->toIso8601String(),
                    'last_accessed_at'   => $enrollment->last_accessed_at?->toIso8601String(),
                    'completed_at'       => $enrollment->completed_at?->toIso8601String(),
                    'progress'           => $progress,
                    'viewed_lectures'    => count($viewedIds),
                    'viewed_lecture_ids' => $viewedIds,
                ];
            }
        }

        return response()->json([
            'id'          => $course->id,
            'title'       => $course->title,
            'slug'        => $course->slug,
            'description' => $course->description,
            'status'      => $course->status,
            'is_featured' => $course->is_featured,
            'author'      => $course->author ? ['id' => $course->author->id, 'name' => $course->author->name] : null,
            'enrollment'  => $enrollmentData,
            'lectures'    => $course->lectures->map(fn($l) => [
                'id' => $l->id,
                'course_id' => $l->course_id,
                'title' => $l->title,
                'description' => $l->description,
                'content' => $l->content,
                'status' => $l->status,
                'order' => $l->order,
            ]),
            'tests' => $course->tests->map(fn($t) => [
                'id' => $t->id,
                'course_id' => $t->course_id,
                'title' => $t->title,
                'description' => $t->description,
                'questions_count' => $t->questions_count,
                'duration' => $t->duration,
                'difficulty' => $t->difficulty,
                'status' => $t->status ?? 'draft',
            ]),
            'published_at' => $course->published_at?->toIso8601String(),
            'created_at'   => $course->created_at->toIso8601String(),
        ]);
    }

    public function store(StoreCourseRequest $request)
    {
        $validated = $request->validated();

        $slug = $this->generateUniqueSlug($validated['title']);

        $course = Course::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'status' => $validated['status'] ?? 'draft',
            'is_featured' => $validated['is_featured'] ?? false,
            'published_at' => ($validated['status'] ?? 'draft') === 'published' ? now() : null,
            'author_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Курс создан', 'data' => [
            'id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'status' => $course->status,
        ]], 201);
    }

    public function update(UpdateCourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        if (!$this->canManageCourse($request->user(), $course)) {
            abort(403);
        }

        $validated = $request->validated();

        if (isset($validated['title']) && $validated['title'] !== $course->title) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title'], $course->id);
        }

        if (isset($validated['status'])) {
            if ($validated['status'] === 'published' && $course->status !== 'published') {
                $validated['published_at'] = now();
            } elseif ($validated['status'] !== 'published') {
                $validated['published_at'] = null;
            }
        }

        $course->update($validated);
        return response()->json(['message' => 'Курс обновлен', 'data' => [
            'id' => $course->id,
            'title' => $course->title,
            'status' => $course->status,
        ]]);
    }

    public function destroy(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        if (!$this->canManageCourse($request->user(), $course)) {
            abort(403);
        }

        $lecturesCount = $course->lectures()->count();
        $testsCount = $course->tests()->count();

        if ($lecturesCount > 0 || $testsCount > 0) {
            return response()->json([
                'message' => 'Невозможно удалить курс с лекциями или тестами',
                'errors' => ['lectures_count' => $lecturesCount, 'tests_count' => $testsCount]
            ], 422);
        }

        $course->delete();
        return response()->json(['message' => 'Курс удален']);
    }

    private function canManageCourse($user, $course): bool
    {
        if ($course->author_id === $user->id) return true;
        return $user->can('manage courses');
    }

    private function generateUniqueSlug(string $title, int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $query = Course::where('slug', 'like', "{$slug}%");

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $exists = $query->exists();

        if ($exists) {
            $counter = 1;
            while (Course::where('slug', "{$slug}-{$counter}")->exists()) {
                $counter++;
            }
            return "{$slug}-{$counter}";
        }

        return $slug;
    }
}
