<?php

use Illuminate\Support\Facades\Route;

// Главная страница - Vue приложение
Route::get('/{vue?}', function () {
    return view('app');
})->where('vue', '.*');
