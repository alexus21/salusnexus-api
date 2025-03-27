<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CitiesController;
use App\Http\Controllers\Api\DepartmentsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return response()->json([
        'message' => 'Página no encontrada. Si tienes dudas contacta con el administrador del sitio.',
    ], 404);
});

Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

Route::get('/departments', [DepartmentsController::class, 'index'])->name('departments.index');
Route::get('/cities/{department_id}', [CitiesController::class, 'getByDepartment'])->name('cities.getByDepartment');

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
