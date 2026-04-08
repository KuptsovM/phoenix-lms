<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLectureRequest;
use App\Http\Requests\UpdateLectureRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lecture;
use App\Models\LectureMaterial;
use App\Models\LectureView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LectureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'materials']);
    }

    public function index(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        if ($course->status !== 'published') {
            if (!$request->user() || !$this->canManageCourse($request->user(), $course)) {
                abort(404);
            }
        }

        $lectures = Lecture::where('course_id', $courseId)
            ->withCount('materials')
            ->orderBy('order')
            ->get()
            ->map(fn($lecture) => [
                'id' => $lecture->id,
                'course_id' => $lecture->course_id,
                'title' => $lecture->title,
                'description' => $lecture->description,
                'content' => $lecture->content,
                'status' => $lecture->status,
                'order' => $lecture->order,
                'materials_count' => $lecture->materials_count,
                'created_at' => $lecture->created_at->toIso8601String(),
                'updated_at' => $lecture->updated_at->toIso8601String(),
            ]);

        return response()->json(['data' => $lectures]);
    }

    public function show(Request $request, $id)
    {
        $lecture = Lecture::with(['materials', 'course'])->findOrFail($id);

        if ($lecture->course->status !== 'published') {
            if (!$request->user() || !$this->canManageCourse($request->user(), $lecture->course)) {
                abort(404);
            }
        }

        return response()->json([
            'id' => $lecture->id,
            'title' => $lecture->title,
            'description' => $lecture->description,
            'content' => $lecture->content,
            'status' => $lecture->status,
            'order' => $lecture->order,
            'course' => ['id' => $lecture->course->id, 'title' => $lecture->course->title],
            'materials' => $lecture->materials->map(fn($m) => [
                'id' => $m->id,
                'title' => $m->title,
                'file_url' => $m->file_url,
                'file_size' => $m->file_size,
                'file_type' => $m->file_type,
                'created_at' => $m->created_at->toIso8601String(),
            ]),
        ]);
    }

    public function store(StoreLectureRequest $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        if (!$this->canManageCourse($request->user(), $course)) {
            abort(403, 'У вас нет права добавлять лекции');
        }

        $validated = $request->validated();

        if (!isset($validated['order'])) {
            $validated['order'] = Lecture::where('course_id', $courseId)->max('order') + 1;
        }

        $lecture = Lecture::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'status' => $validated['status'] ?? 'draft',
            'order' => $validated['order'],
            'course_id' => $courseId,
        ]);

        return response()->json(['message' => 'Лекция создана', 'data' => [
            'id' => $lecture->id,
            'course_id' => $lecture->course_id,
            'title' => $lecture->title,
            'description' => $lecture->description,
            'content' => $lecture->content,
            'status' => $lecture->status,
            'order' => $lecture->order,
        ]], 201);
    }

    public function update(UpdateLectureRequest $request, $id)
    {
        $lecture = Lecture::with('course')->findOrFail($id);
        if (!$this->canManageCourse($request->user(), $lecture->course)) {
            abort(403);
        }

        $validated = $request->validated();

        $lecture->update($validated);
        return response()->json(['message' => 'Лекция обновлена', 'data' => [
            'id' => $lecture->id,
            'course_id' => $lecture->course_id,
            'title' => $lecture->title,
            'description' => $lecture->description,
            'content' => $lecture->content,
            'status' => $lecture->status,
            'order' => $lecture->order,
        ]]);
    }

    public function destroy(Request $request, $id)
    {
        $lecture = Lecture::with('course')->findOrFail($id);
        if (!$this->canManageCourse($request->user(), $lecture->course)) {
            abort(403);
        }

        foreach ($lecture->materials as $material) {
            if ($material->file_url) {
                $path = str_replace('/storage/', '', parse_url($material->file_url, PHP_URL_PATH));
                Storage::disk('public')->delete($path);
            }
        }

        $lecture->delete();
        return response()->json(['message' => 'Лекция удалена']);
    }

    public function reorder(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        if (!$this->canManageCourse($request->user(), $course)) {
            abort(403);
        }

        $validated = $request->validate([
            'lectures' => 'required|array',
            'lectures.*.id' => 'required|integer|exists:lectures,id',
            'lectures.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['lectures'] as $item) {
            Lecture::where('id', $item['id'])->where('course_id', $courseId)->update(['order' => $item['order']]);
        }

        return response()->json(['message' => 'Порядок обновлен']);
    }

    public function materials(Request $request, $lectureId)
    {
        $lecture = Lecture::with('course')->findOrFail($lectureId);
        if ($lecture->course->status !== 'published') {
            if (!$request->user() || !$this->canManageCourse($request->user(), $lecture->course)) {
                abort(404);
            }
        }

        return response()->json(['data' => $lecture->materials->map(fn($m) => [
            'id' => $m->id,
            'title' => $m->title,
            'file_url' => $m->file_url,
            'file_size' => $m->file_size,
            'file_type' => $m->file_type,
        ])]);
    }

    public function uploadMaterial(Request $request, $lectureId)
    {
        $lecture = Lecture::with('course')->findOrFail($lectureId);
        if (!$this->canManageCourse($request->user(), $lecture->course)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:102400',
        ]);

        $file = $validated['file'];
        $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'mp3', 'webm'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedTypes)) {
            return response()->json(['message' => 'Недопустимый тип файла'], 422);
        }

        $path = $file->store("materials/lecture_{$lectureId}", 'public');

        $material = LectureMaterial::create([
            'title' => $validated['title'],
            'file_url' => Storage::disk('public')->url($path),
            'file_size' => $file->getSize(),
            'file_type' => $extension,
            'lecture_id' => $lectureId,
        ]);

        return response()->json(['message' => 'Материал загружен', 'data' => [
            'id' => $material->id,
            'title' => $material->title,
            'file_url' => $material->file_url,
            'file_size' => $material->file_size,
            'file_type' => $material->file_type,
        ]], 201);
    }

    /**
     * POST /api/lectures/{id}/complete — mark lecture as viewed
     */
    public function completeLecture(Request $request, $lectureId)
    {
        $lecture = Lecture::with('course')->findOrFail($lectureId);
        $user    = $request->user();

        $enrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $lecture->course_id)
            ->exists();

        if (!$enrolled && !$this->canManageCourse($user, $lecture->course)) {
            abort(403, 'Вы не записаны на этот курс');
        }

        LectureView::firstOrCreate(
            ['user_id' => $user->id, 'lecture_id' => $lectureId],
            ['viewed_at' => now()]
        );

        // Touch last_accessed_at
        Enrollment::where('user_id', $user->id)
            ->where('course_id', $lecture->course_id)
            ->update(['last_accessed_at' => now()]);

        // Calculate progress
        $totalPublished = Lecture::where('course_id', $lecture->course_id)
            ->where('status', 'published')->count();
        $viewed = LectureView::where('user_id', $user->id)
            ->whereHas('lecture', fn($q) => $q->where('course_id', $lecture->course_id)
                ->where('status', 'published'))->count();

        $progress       = $totalPublished > 0 ? round(($viewed / $totalPublished) * 100) : 0;
        $courseCompleted = $totalPublished > 0 && $viewed >= $totalPublished;

        if ($courseCompleted) {
            Enrollment::where('user_id', $user->id)
                ->where('course_id', $lecture->course_id)
                ->whereNull('completed_at')
                ->update(['completed_at' => now()]);
        }

        return response()->json([
            'message' => 'Лекция отмечена как прочитанная',
            'data'    => [
                'viewed_lectures'  => $viewed,
                'total_lectures'   => $totalPublished,
                'progress'         => $progress,
                'course_completed' => $courseCompleted,
            ],
        ]);
    }

    public function deleteMaterial(Request $request, $materialId)
    {
        $material = LectureMaterial::with('lecture.course')->findOrFail($materialId);
        if (!$this->canManageCourse($request->user(), $material->lecture->course)) {
            abort(403);
        }

        if ($material->file_url) {
            $path = str_replace('/storage/', '', parse_url($material->file_url, PHP_URL_PATH));
            Storage::disk('public')->delete($path);
        }

        $material->delete();
        return response()->json(['message' => 'Материал удален']);
    }

    private function canManageCourse($user, $course): bool
    {
        if (!$user) {
            return false;
        }

        if ($course->author_id === $user->id) return true;
        return $user->can('manage courses');
    }
}
