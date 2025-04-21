<?php

use App\Http\Controllers\Api\AppointmentsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CitiesController;
use App\Http\Controllers\Api\ClinicSchedulesController;
use App\Http\Controllers\Api\ClinicViewController;
use App\Http\Controllers\Api\DepartmentsController;
use App\Http\Controllers\Api\FavoritesController;
use App\Http\Controllers\Api\MedicalClinicController;
use App\Http\Controllers\Api\PatientProfilesController;
use App\Http\Controllers\Api\ProfessionalProfilesController;
use App\Http\Controllers\Api\SpecialitiesController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\SubscriptionsController;
use App\Http\Middleware\NoBrowserCacheMiddleware;
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

/* Rutas asociadas al manejo de los planes */
Route::post('/subscription-plan/filter', [SubscriptionPlanController::class, 'filterByPlan'])->name('subscription-plan.filterByPlan');

// Obtener todos los planes de suscripción con sus características y precios
Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index'])->name('subscription-plans.index');

// Obtener un plan de suscripción filtrado por tipo y sus características (por parámetro de ruta)
Route::get('/subscription-plan/{type}', [SubscriptionPlanController::class, 'showByTypeParam'])->name('subscription-plan.byTypeParam');

Route::middleware(['auth:api', NoBrowserCacheMiddleware::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/validate', [AuthController::class, 'validateToken'])->name('auth.validateToken');
    Route::post('/add-payment-method', [SubscriptionsController::class, 'create'])->name('subscriptions.create');
    Route::get('/userprofile', [AuthController::class, 'profile'])->name('auth.userProfile');
    Route::post('/verification/patient', [PatientProfilesController::class, 'verifyPatientAccount'])->name('patients.verifyAccount');
    Route::post('/verification/professionals', [ProfessionalProfilesController::class, 'verifyProfessionalAccount'])->name('professionals.verifyAccount');
    Route::get('/is-verified', [AuthController::class, 'isUserVerified'])->name('auth.isUserVerified');
    Route::get('/subscriptions', [SubscriptionsController::class, 'mySubscription'])->name('subscriptions.mySubscription');

    /* Rutas asociadas al manejo de las clínicas médicas */
    Route::get('/medical-clinics/view', [MedicalClinicController::class, 'index'])->name('medical-clinic.index');
    Route::get('/medical-clinics/show/{id}', [MedicalClinicController::class, 'show'])->name('medical-clinic.show');
    Route::post('/medical-clinics/add', [MedicalClinicController::class, 'store'])->name('medical-clinic.store');
    Route::patch('/medical-clinics/edit/{id}', [MedicalClinicController::class, 'edit'])->name('medical-clinic.edit');
    Route::delete('/medical-clinics/delete/{id}', [MedicalClinicController::class, 'delete'])->name('medical-clinic.delete');

    /* Rutas asociadas al manejo de las clínicas vistas por pacientes */
    Route::post('/clinic-view', [ClinicViewController::class, 'store'])->name('clinic-view.store');

    /* Rutas asociadas al manejo de los favoritos */
    Route::get('/favorites/get', [FavoritesController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/add', [FavoritesController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/delete', [FavoritesController::class, 'destroy'])->name('favorites.delete');

    /* Rutas asociadas al manejo de los horarios de las clínicas */
    Route::get('/schedules/get/all', [ClinicSchedulesController::class, 'index'])->name('clinic-schedules.index');
    Route::get('/schedules/get/{id}', [ClinicSchedulesController::class, 'show'])->name('clinic-schedules.show');
    Route::get('/schedules/get/clinic/{id}', [ClinicSchedulesController::class, 'showByClinic'])->name('clinic-schedules.showByClinic');
    Route::post('/schedules/add', [ClinicSchedulesController::class, 'store'])->name('clinic-schedules.store');
    Route::patch('/schedules/edit/{id}', [ClinicSchedulesController::class, 'update'])->name('clinic-schedules.update');
    Route::delete('/schedules/delete/{id}', [ClinicSchedulesController::class, 'destroy'])->name('clinic-schedules.delete');

    /* Rutas asociadas al manejo de las citas */
    Route::post('/appointments/add', [AppointmentsController::class, 'store'])->name('appointments.store');
});
