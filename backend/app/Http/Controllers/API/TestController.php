<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index($courseId)
    {
        $tests = Test::where('course_id', $courseId)->get()->map(function ($test) {
            return [
                'id' => $test->id,
                'title' => $test->title,
                'description' => $test->description,
                'questions_count' => $test->questions_count ?? 10,
                'duration' => $test->duration ?? 30,
                'difficulty' => $test->difficulty ?? 'medium',
                'course_id' => $test->course_id,
            ];
        });

        return response()->json($tests);
    }

    public function show($id)
    {
        $test = Test::with(['questions'])->findOrFail($id);
        
        return response()->json([
            'id' => $test->id,
            'title' => $test->title,
            'description' => $test->description,
            'questions_count' => $test->questions_count ?? 10,
            'duration' => $test->duration ?? 30,
            'difficulty' => $test->difficulty ?? 'medium',
            'course_id' => $test->course_id,
            'questions' => $test->questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'type' => $question->type ?? 'multiple_choice',
                    'options' => $question->options ?? [],
                    'correct_answer' => $question->correct_answer,
                ];
            }),
        ]);
    }

    public function submit(Request $request, $id)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $test = Test::with(['questions'])->findOrFail($id);
        $answers = $request->answers;
        
        $totalQuestions = $test->questions->count();
        $correctAnswers = 0;
        $totalPoints = 0;

        foreach ($test->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $isCorrect = $this->checkAnswer($question, $userAnswer);
            
            if ($isCorrect) {
                $correctAnswers++;
                $totalPoints += $question->points ?? 1;
            }
        }

        // Сохраняем результат теста (здесь должна быть логика сохранения в базу)
        
        return response()->json([
            'score' => $totalPoints,
            'total_points' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'percentage' => ($correctAnswers / $totalQuestions) * 100,
        ]);
    }

    private function checkAnswer($question, $userAnswer)
    {
        if ($userAnswer === null) return false;

        switch ($question->type) {
            case 'multiple_choice':
                return $userAnswer === $question->correct_answer;
            case 'boolean':
                return $userAnswer === $question->correct_answer;
            case 'text':
                // Простая проверка текстового ответа (можно усложнить)
                return strtolower(trim($userAnswer)) === strtolower(trim($question->correct_answer));
            default:
                return false;
        }
    }
}
