<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\LectureController;
use App\Http\Controllers\API\TestController;

// API роуты для Vue приложения
Route::prefix('api')->group(function () {
    // Аутентификация
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
    
    // Курсы
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::post('/courses', [CourseController::class, 'store'])->middleware('auth:sanctum');
    Route::put('/courses/{id}', [CourseController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->middleware('auth:sanctum');
    
    // Лекции
    Route::get('/courses/{courseId}/lectures', [LectureController::class, 'index']);
    Route::get('/lectures/{id}', [LectureController::class, 'show']);
    Route::get('/lectures/{id}/materials', [LectureController::class, 'materials']);
    
    // Тесты
    Route::get('/courses/{courseId}/tests', [TestController::class, 'index']);
    Route::get('/tests/{id}', [TestController::class, 'show']);
    Route::post('/tests/{id}/submit', [TestController::class, 'submit'])->middleware('auth:sanctum');
});
