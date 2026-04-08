<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\SubmitTestRequest;
use App\Http\Requests\UpdateTestRequest;
use App\Models\Course;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestQuestion;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        if ($course->status !== 'published') {
            if (!$request->user() || !$this->canManageCourse($request->user(), $course)) {
                abort(404);
            }
        }

        $tests = Test::where('course_id', $courseId)
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($test) => [
                'id' => $test->id,
                'course_id' => $test->course_id,
                'title' => $test->title,
                'description' => $test->description,
                'questions_count' => $test->questions_count,
                'duration' => $test->duration,
                'difficulty' => $test->difficulty,
                'status' => $test->status ?? 'draft',
            ]);

        return response()->json(['data' => $tests]);
    }

    public function show(Request $request, $id)
    {
        $test = Test::with(['course', 'questions'])->findOrFail($id);

        if ($test->course->status !== 'published') {
            if (!$request->user() || !$this->canManageCourse($request->user(), $test->course)) {
                abort(404);
            }
        }

        $isTeacher = $request->user() && ($this->canManageCourse($request->user(), $test->course) || $request->user()->hasRole('admin'));

        $questions = $test->questions->map(fn($q) => [
            'id' => $q->id,
            'question' => $q->question,
            'type' => $q->type,
            'options' => $q->options,
            'points' => $q->points,
            'correct_answer' => $isTeacher ? $q->correct_answer : null,
        ]);

        return response()->json([
            'id' => $test->id,
            'course_id' => $test->course_id,
            'title' => $test->title,
            'description' => $test->description,
            'questions_count' => $test->questions_count,
            'duration' => $test->duration,
            'difficulty' => $test->difficulty,
            'status' => $test->status ?? 'draft',
            'course' => ['id' => $test->course->id, 'title' => $test->course->title],
            'questions' => $questions,
        ]);
    }

    public function store(StoreTestRequest $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        if (!$this->canManageCourse($request->user(), $course)) {
            abort(403);
        }

        $validated = $request->validated();

        $test = Test::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'duration' => $validated['duration'],
            'difficulty' => $validated['difficulty'],
            'status' => $validated['status'] ?? 'draft',
            'questions_count' => 0,
            'course_id' => $courseId,
        ]);

        return response()->json(['message' => 'Тест создан', 'data' => [
            'id' => $test->id,
            'course_id' => $test->course_id,
            'title' => $test->title,
            'description' => $test->description,
            'duration' => $test->duration,
            'difficulty' => $test->difficulty,
            'status' => $test->status,
            'questions_count' => $test->questions_count,
        ]], 201);
    }

    public function update(UpdateTestRequest $request, $id)
    {
        $test = Test::with('course')->findOrFail($id);
        if (!$this->canManageCourse($request->user(), $test->course)) {
            abort(403);
        }

        $validated = $request->validated();

        $test->update($validated);
        return response()->json(['message' => 'Тест обновлен', 'data' => [
            'id' => $test->id,
            'course_id' => $test->course_id,
            'title' => $test->title,
            'description' => $test->description,
            'duration' => $test->duration,
            'difficulty' => $test->difficulty,
            'status' => $test->status,
            'questions_count' => $test->questions_count,
        ]]);
    }

    public function destroy(Request $request, $id)
    {
        $test = Test::with('course')->findOrFail($id);
        if (!$this->canManageCourse($request->user(), $test->course)) {
            abort(403);
        }

        DB::transaction(function () use ($test) {
            TestQuestion::where('test_id', $test->id)->delete();
            TestResult::where('test_id', $test->id)->delete();
            $test->delete();
        });

        return response()->json(['message' => 'Тест удален']);
    }

    public function addQuestion(Request $request, $testId)
    {
        $test = Test::with('course')->findOrFail($testId);
        if (!$this->canManageCourse($request->user(), $test->course)) {
            abort(403);
        }

        $validated = $this->validateQuestionPayload($request, true);

        $question = TestQuestion::create([
            'question' => $validated['question'],
            'type' => $validated['type'],
            'options' => $validated['options'],
            'correct_answer' => $validated['correct_answer'],
            'points' => $validated['points'] ?? 1,
            'test_id' => $testId,
        ]);

        $test->update(['questions_count' => $test->questions()->count()]);

        return response()->json(['message' => 'Вопрос добавлен', 'data' => [
            'id' => $question->id,
            'question' => $question->question,
            'type' => $question->type,
            'options' => $question->options,
            'correct_answer' => $question->correct_answer,
            'points' => $question->points,
        ]], 201);
    }

    public function updateQuestion(Request $request, $questionId)
    {
        $question = TestQuestion::with('test.course')->findOrFail($questionId);
        if (!$this->canManageCourse($request->user(), $question->test->course)) {
            abort(403);
        }

        $validated = $this->validateQuestionPayload($request, false, $question);
        $question->update($validated);

        return response()->json(['message' => 'Вопрос обновлен', 'data' => [
            'id' => $question->id,
            'question' => $question->question,
            'type' => $question->type,
            'options' => $question->options,
            'correct_answer' => $question->correct_answer,
            'points' => $question->points,
        ]]);
    }

    public function deleteQuestion(Request $request, $questionId)
    {
        $question = TestQuestion::with('test.course')->findOrFail($questionId);
        if (!$this->canManageCourse($request->user(), $question->test->course)) {
            abort(403);
        }

        $test = $question->test;
        $question->delete();
        $test->update(['questions_count' => $test->questions()->count()]);

        return response()->json(['message' => 'Вопрос удален']);
    }

    public function bulkAddQuestions(Request $request, $testId)
    {
        $test = Test::with('course')->findOrFail($testId);
        if (!$this->canManageCourse($request->user(), $test->course)) {
            abort(403);
        }

        $validated = $request->validate([
            'questions' => 'required|array|min:1|max:100',
        ]);

        $created = 0;
        foreach ($validated['questions'] as $q) {
            $normalized = $this->normalizeQuestionInput($q);
            TestQuestion::create([
                'question' => $normalized['question'],
                'type' => $normalized['type'],
                'options' => $normalized['options'],
                'correct_answer' => $normalized['correct_answer'],
                'points' => $normalized['points'] ?? 1,
                'test_id' => $testId,
            ]);
            $created++;
        }

        $test->update(['questions_count' => $test->questions()->count()]);

        return response()->json(['message' => "Добавлено {$created} вопросов", 'questions_count' => $test->fresh()->questions_count]);
    }

    public function submit(SubmitTestRequest $request, $id)
    {
        $test = Test::with(['questions', 'course'])->findOrFail($id);

        if ($test->status !== 'published') {
            abort(403, 'Тест недоступен для прохождения');
        }

        $validated = $request->validated();

        $totalPoints = 0;
        $maxPoints = 0;
        $correctAnswers = 0;

        foreach ($test->questions as $question) {
            $maxPoints += $question->points;
            $userAnswer = $validated['answers'][$question->id] ?? null;
            
            if ($this->checkAnswer($question, $userAnswer)) {
                $totalPoints += $question->points;
                $correctAnswers++;
            }
        }

        $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 2) : 0;
        $passed = $percentage >= 70;

        $result = TestResult::create([
            'user_id' => $request->user()->id,
            'test_id' => $id,
            'score' => $totalPoints,
            'total_points' => $maxPoints,
            'percentage' => $percentage,
            'answers' => $validated['answers'],
            'completed_at' => now(),
        ]);

        return response()->json([
            'message' => $passed ? 'Тест пройден!' : 'Тест не пройден',
            'result' => [
                'id' => $result->id,
                'score' => $totalPoints,
                'max_score' => $maxPoints,
                'percentage' => $percentage,
                'passed' => $passed,
                'correct_answers' => $correctAnswers,
                'total_questions' => $test->questions->count(),
                'time_spent' => $validated['time_spent'],
            ]
        ]);
    }

    private function checkAnswer(TestQuestion $question, $userAnswer): bool
    {
        if ($userAnswer === null) return false;

        $correct = $question->correct_answer;

        switch ($question->type) {
            case 'multiple_choice':
                return (string) $userAnswer === (string) $correct;
            case 'boolean':
                return ($userAnswer === 'true' || $userAnswer === true) === ($correct === 'true' || $correct === true);
            case 'text':
                return strtolower(trim($userAnswer)) === strtolower(trim($correct));
            default:
                return false;
        }
    }

    public function getUserResults(Request $request, $testId)
    {
        $results = TestResult::where('test_id', $testId)
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'score' => $r->score,
                'max_score' => $r->total_points,
                'percentage' => $r->percentage,
                'passed' => $r->percentage >= 70,
                'time_spent' => null,
                'created_at' => $r->created_at->toIso8601String(),
            ]);

        return response()->json(['data' => $results]);
    }

    public function startAttempt(Request $request, $id)
    {
        $test = Test::with('questions')->findOrFail($id);
        if ($test->status !== 'published') {
            abort(403, 'Тест недоступен для прохождения');
        }

        $attempt = TestAttempt::where('user_id', $request->user()->id)
            ->where('test_id', $test->id)
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();

        if (!$attempt) {
            $attempt = TestAttempt::create([
                'user_id' => $request->user()->id,
                'test_id' => $test->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Попытка готова',
            'data' => [
                'attempt_id' => $attempt->id,
                'test_id' => $test->id,
                'status' => $attempt->status,
                'answers' => $attempt->answers ?? [],
                'started_at' => $attempt->started_at?->toIso8601String(),
            ],
        ], 201);
    }

    public function submitAttempt(SubmitTestRequest $request, $attemptId)
    {
        $attempt = TestAttempt::with(['test.questions', 'test.course'])->findOrFail($attemptId);
        if ($attempt->user_id !== $request->user()->id) {
            abort(403);
        }
        if ($attempt->status !== 'in_progress') {
            abort(422, 'Попытка уже завершена');
        }

        $validated = $request->validated();
        $evaluation = $this->evaluateTestAnswers($attempt->test->questions, $validated['answers']);

        $attempt->update([
            'status' => 'submitted',
            'answers' => $validated['answers'],
            'score' => $evaluation['total_points'],
            'max_score' => $evaluation['max_points'],
            'percentage' => $evaluation['percentage'],
            'submitted_at' => now(),
            'time_spent' => $validated['time_spent'],
        ]);

        $this->persistLegacyResult($request->user()->id, $attempt->test_id, $validated['answers'], $evaluation);

        return response()->json([
            'message' => $evaluation['passed'] ? 'Тест пройден!' : 'Тест не пройден',
            'result' => [
                'attempt_id' => $attempt->id,
                'score' => $evaluation['total_points'],
                'max_score' => $evaluation['max_points'],
                'percentage' => $evaluation['percentage'],
                'passed' => $evaluation['passed'],
                'correct_answers' => $evaluation['correct_answers'],
                'total_questions' => $attempt->test->questions->count(),
                'time_spent' => $validated['time_spent'],
            ],
        ]);
    }

    public function getAttempt(Request $request, $attemptId)
    {
        $attempt = TestAttempt::with(['test:id,title,description,duration,difficulty,status', 'test.questions'])->findOrFail($attemptId);
        if ($attempt->user_id !== $request->user()->id) {
            abort(403);
        }

        return response()->json([
            'data' => [
                'id' => $attempt->id,
                'test_id' => $attempt->test_id,
                'status' => $attempt->status,
                'answers' => $attempt->answers ?? [],
                'score' => $attempt->score,
                'max_score' => $attempt->max_score,
                'percentage' => $attempt->percentage,
                'time_spent' => $attempt->time_spent,
                'started_at' => $attempt->started_at?->toIso8601String(),
                'submitted_at' => $attempt->submitted_at?->toIso8601String(),
                'test' => [
                    'id' => $attempt->test->id,
                    'title' => $attempt->test->title,
                    'description' => $attempt->test->description,
                    'duration' => $attempt->test->duration,
                    'difficulty' => $attempt->test->difficulty,
                    'status' => $attempt->test->status,
                    'questions' => $attempt->test->questions->map(fn($q) => [
                        'id' => $q->id,
                        'question' => $q->question,
                        'type' => $q->type,
                        'options' => $q->options,
                        'points' => $q->points,
                    ]),
                ],
            ],
        ]);
    }

    public function saveAttemptDraft(Request $request, $attemptId)
    {
        $attempt = TestAttempt::findOrFail($attemptId);
        if ($attempt->user_id !== $request->user()->id) {
            abort(403);
        }
        if ($attempt->status !== 'in_progress') {
            abort(422, 'Сохранять можно только активную попытку');
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'time_spent' => 'nullable|integer|min:0',
        ]);

        $attempt->update([
            'answers' => $validated['answers'],
            'time_spent' => $validated['time_spent'] ?? $attempt->time_spent,
        ]);

        return response()->json([
            'message' => 'Черновик попытки сохранен',
            'data' => [
                'attempt_id' => $attempt->id,
                'status' => $attempt->status,
                'updated_at' => $attempt->updated_at?->toIso8601String(),
            ],
        ]);
    }

    private function canManageCourse($user, $course): bool
    {
        if (!$user) {
            return false;
        }

        if ($course->author_id === $user->id) return true;
        return $user->can('manage courses');
    }

    private function validateQuestionPayload(Request $request, bool $isCreate, ?TestQuestion $existing = null): array
    {
        $baseRules = [
            'question' => ($isCreate ? 'required' : 'sometimes') . '|string|min:1|max:2000',
            'type' => ($isCreate ? 'required' : 'sometimes') . '|in:multiple_choice,boolean,text',
            'points' => 'sometimes|integer|min:1|max:100',
        ];

        if ($isCreate) {
            $baseRules['correct_answer'] = 'required';
        } else {
            $baseRules['correct_answer'] = 'sometimes';
            $baseRules['options'] = 'sometimes|array|min:2';
        }

        $validated = $request->validate($baseRules);

        $type = $validated['type'] ?? $existing?->type;
        if (!$type) {
            abort(422, 'Не указан тип вопроса');
        }

        if ($type === 'multiple_choice') {
            $options = $validated['options'] ?? $existing?->options;
            if (!is_array($options) || count($options) < 2) {
                abort(422, 'Для multiple_choice требуется минимум 2 варианта');
            }
            $validated['options'] = array_values(array_map(fn($option) => trim((string) $option), $options));
        } else {
            $validated['options'] = null;
        }

        if (array_key_exists('correct_answer', $validated)) {
            $validated['correct_answer'] = $this->normalizeCorrectAnswer($type, $validated['correct_answer']);
        } elseif ($isCreate) {
            abort(422, 'Для вопроса требуется правильный ответ');
        }

        return $validated;
    }

    private function normalizeQuestionInput(array $question): array
    {
        if (!isset($question['question'], $question['type'], $question['correct_answer'])) {
            abort(422, 'Некорректная структура вопроса для bulk добавления');
        }

        $type = $question['type'];
        if (!in_array($type, ['multiple_choice', 'boolean', 'text'], true)) {
            abort(422, 'Некорректный тип вопроса');
        }

        $options = null;
        if ($type === 'multiple_choice') {
            if (!isset($question['options']) || !is_array($question['options']) || count($question['options']) < 2) {
                abort(422, 'Для multiple_choice требуется минимум 2 варианта');
            }
            $options = array_values(array_map(fn($option) => trim((string) $option), $question['options']));
        }

        return [
            'question' => trim((string) $question['question']),
            'type' => $type,
            'options' => $options,
            'correct_answer' => $this->normalizeCorrectAnswer($type, $question['correct_answer']),
            'points' => isset($question['points']) ? (int) $question['points'] : 1,
        ];
    }

    private function normalizeCorrectAnswer(string $type, mixed $rawAnswer): string
    {
        return match ($type) {
            'boolean' => ($rawAnswer === 'true' || $rawAnswer === true || $rawAnswer === 1 || $rawAnswer === '1') ? 'true' : 'false',
            default => trim((string) $rawAnswer),
        };
    }

    private function evaluateTestAnswers($questions, array $answers): array
    {
        $totalPoints = 0;
        $maxPoints = 0;
        $correctAnswers = 0;

        foreach ($questions as $question) {
            $maxPoints += $question->points;
            $userAnswer = $answers[$question->id] ?? null;
            if ($this->checkAnswer($question, $userAnswer)) {
                $totalPoints += $question->points;
                $correctAnswers++;
            }
        }

        $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 2) : 0;

        return [
            'total_points' => $totalPoints,
            'max_points' => $maxPoints,
            'correct_answers' => $correctAnswers,
            'percentage' => $percentage,
            'passed' => $percentage >= 70,
        ];
    }

    private function persistLegacyResult(int $userId, int $testId, array $answers, array $evaluation): void
    {
        TestResult::create([
            'user_id' => $userId,
            'test_id' => $testId,
            'score' => $evaluation['total_points'],
            'total_points' => $evaluation['max_points'],
            'percentage' => $evaluation['percentage'],
            'answers' => $answers,
            'completed_at' => now(),
        ]);
    }
}
