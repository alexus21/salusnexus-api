<?php

use App\Http\Controllers\Api\HealthTipsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ruta para la demostración de consejos de salud
Route::get('/health-tips/demo', [HealthTipsController::class, 'demo'])->name('health-tips.demo');
