<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CitiesController;
use App\Http\Controllers\Api\DepartmentsController;
use App\Http\Controllers\Api\SpecialitiesController;
use App\Http\Controllers\Api\UserVerificationController;
use App\Http\Middleware\NoBrowserCacheMiddleware;
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
Route::get('/specialities', [SpecialitiesController::class, 'index'])->name('specialities.index');

Route::middleware(['auth:api', NoBrowserCacheMiddleware::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/validate', [AuthController::class, 'validateToken'])->name('auth.validateToken');
    Route::get('/userprofile', [AuthController::class, 'profile'])->name('auth.userProfile');
    Route::post('/user-verification', [UserVerificationController::class, 'verifyAccount'])->name('userVerification.verifyAccount');
    Route::get('/is-verified', [AuthController::class, 'isUserVerified'])->name('auth.isUserVerified');
});
