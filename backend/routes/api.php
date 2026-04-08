<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\EnrollmentController;
use App\Http\Controllers\API\LectureController;
use App\Http\Controllers\API\TeacherController;
use App\Http\Controllers\API\TestController;
use Illuminate\Support\Facades\Route;

// Публичные роуты
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Курсы - публичные
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);

// Курсы - защищенные
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
});

// Enrollment - публичная проверка (auth нужен внутри контроллера)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/courses/{id}/enroll', [EnrollmentController::class, 'enroll']);
    Route::delete('/courses/{id}/unenroll', [EnrollmentController::class, 'unenroll']);
    Route::get('/courses/{id}/enrollment', [EnrollmentController::class, 'checkEnrollment']);
    Route::get('/my/courses', [EnrollmentController::class, 'myCourses']);
    Route::get('/my/dashboard', [EnrollmentController::class, 'myDashboard']);
    Route::post('/lectures/{id}/complete', [LectureController::class, 'completeLecture']);
});

// Лекции - публичные
Route::get('/courses/{courseId}/lectures', [LectureController::class, 'index']);
Route::get('/lectures/{id}', [LectureController::class, 'show']);
Route::get('/lectures/{id}/materials', [LectureController::class, 'materials']);

// Лекции - защищенные
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/courses/{courseId}/lectures', [LectureController::class, 'store']);
    Route::put('/lectures/{id}', [LectureController::class, 'update']);
    Route::delete('/lectures/{id}', [LectureController::class, 'destroy']);
    Route::post('/courses/{courseId}/lectures/reorder', [LectureController::class, 'reorder']);
    
    // Материалы лекций
    Route::post('/lectures/{lectureId}/materials', [LectureController::class, 'uploadMaterial']);
    Route::delete('/materials/{materialId}', [LectureController::class, 'deleteMaterial']);
});

// Тесты - публичные
Route::get('/courses/{courseId}/tests', [TestController::class, 'index']);
Route::get('/tests/{id}', [TestController::class, 'show']);

// Учитель
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/teacher/stats', [TeacherController::class, 'stats']);
    Route::get('/teacher/courses', [TeacherController::class, 'myCourses']);
    Route::get('/teacher/courses/{courseId}/students', [TeacherController::class, 'courseStudents']);
});

// Тесты - защищенные
Route::middleware('auth:sanctum')->group(function () {
    // CRUD тестов
    Route::post('/courses/{courseId}/tests', [TestController::class, 'store']);
    Route::put('/tests/{id}', [TestController::class, 'update']);
    Route::delete('/tests/{id}', [TestController::class, 'destroy']);
    
    // Вопросы тестов
    Route::post('/tests/{testId}/questions', [TestController::class, 'addQuestion']);
    Route::put('/questions/{questionId}', [TestController::class, 'updateQuestion']);
    Route::delete('/questions/{questionId}', [TestController::class, 'deleteQuestion']);
    Route::post('/tests/{testId}/questions/bulk', [TestController::class, 'bulkAddQuestions']);
    
    // Прохождение тестов
    Route::post('/tests/{id}/attempts', [TestController::class, 'startAttempt']);
    Route::get('/attempts/{attemptId}', [TestController::class, 'getAttempt']);
    Route::post('/attempts/{attemptId}/draft', [TestController::class, 'saveAttemptDraft']);
    Route::post('/attempts/{attemptId}/submit', [TestController::class, 'submitAttempt']);
    Route::post('/tests/{id}/submit', [TestController::class, 'submit']);
    Route::get('/tests/{testId}/results', [TestController::class, 'getUserResults']);
});
