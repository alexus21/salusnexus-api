<?php

use App\Http\Controllers\Api\AppointmentsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CitiesController;
use App\Http\Controllers\Api\ClinicSchedulesController;
use App\Http\Controllers\Api\ClinicViewController;
use App\Http\Controllers\Api\DeepSeekController;
use App\Http\Controllers\Api\DepartmentsController;
use App\Http\Controllers\Api\DiseaseController;
use App\Http\Controllers\Api\FavoritesController;
use App\Http\Controllers\Api\HealthTipsController;
use App\Http\Controllers\Api\MedicalClinicController;
use App\Http\Controllers\Api\PatientProfilesController;
use App\Http\Controllers\Api\ProfessionalProfilesController;
use App\Http\Controllers\Api\ReviewsController;
use App\Http\Controllers\Api\SpecialitiesController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\SubscriptionsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\NoBrowserCacheMiddleware;
use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return response()->json([
        'message' => 'Página no encontrada. Si tienes dudas contacta con el administrador del sitio.',
    ], 404);
});

Route::get('/diseases', [DiseaseController::class, 'index'])->name('diseases.index');

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
    /* Rutas asociadas al manejo de los usuarios */
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::patch('/patients/update/{id}', [PatientProfilesController::class, 'update'])->name('patients.update');
    Route::patch('/patients/update-health-tips-preference', [PatientProfilesController::class, 'updateNotificationsPreferences'])->name('patients.updateHealthTipsPreference');
    Route::get('/patients/health-tips-preference', [PatientProfilesController::class, 'getHealthTipsPreference'])->name('patients.getHealthTipsPreference');
    Route::patch('/professionals/update/{id}', [ProfessionalProfilesController::class, 'update'])->name('professionals.update');
    Route::put('/update-password/{id}', [UserController::class, 'updatePassword'])->name('users.updatePassword');
    Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');

    /* Rutas asociadas a la verificación de los perfiles */
    Route::get('/validate', [AuthController::class, 'validateToken'])->name('auth.validateToken');
    Route::post('/verification/patient', [PatientProfilesController::class, 'verifyPatientAccount'])->name('patients.verifyAccount');
    Route::post('/verification/professionals', [ProfessionalProfilesController::class, 'verifyProfessionalAccount'])->name('professionals.verifyAccount');
    Route::get('/is-verified', [AuthController::class, 'isUserVerified'])->name('auth.isUserVerified');

    /* Rutas asociadas al manejo de los perfiles de los usuarios */
    Route::get('/userprofile', [AuthController::class, 'profile'])->name('auth.userProfile');
    Route::get('/userprofile/{id}', [UserController::class, 'show'])->name('auth.show');

    /* Rutas asociadas al manejo de las suscripciones y métodos de pago */
    Route::post('/add-payment-method', [SubscriptionsController::class, 'create'])->name('subscriptions.create');
    Route::post('/change-subscription-plan', [SubscriptionsController::class, 'changePlan'])->name('subscriptions.changePlan');
    Route::get('/subscriptions/me', [SubscriptionsController::class, 'mySubscription'])->name('subscriptions.mySubscription');
    Route::get('/subscriptions', [SubscriptionsController::class, 'subscriptions'])->name('subscriptions.subscriptions');

    /* Rutas asociadas al manejo de las clínicas médicas */
    Route::get('/medical-clinics/view', [MedicalClinicController::class, 'index'])->name('medical-clinic.index');
    Route::get('/medical-clinics/show/{id}', [MedicalClinicController::class, 'show'])->name('medical-clinic.show');
    Route::get('/medical-clinics/me', [MedicalClinicController::class, 'showMyClinic'])->name('medical-clinic.myClinic');
    Route::post('/medical-clinics/add', [MedicalClinicController::class, 'store'])->name('medical-clinic.store');
    Route::patch('/medical-clinics/edit/{id}', [MedicalClinicController::class, 'edit'])->name('medical-clinic.edit');
    Route::delete('/medical-clinics/delete/{id}', [MedicalClinicController::class, 'delete'])->name('medical-clinic.delete');

    /* Rutas asociadas al manejo de las clínicas vistas por pacientes */
    Route::post('/clinic-view', [ClinicViewController::class, 'store'])->name('clinic-view.store');

    /* Rutas asociadas al manejo de los favoritos */
    Route::get('/favorites/get', [FavoritesController::class, 'index'])->name('favorites.index');
    Route::get('/favorites/me', [FavoritesController::class, 'getMyFavorites'])->name('favorites.myFavorites');
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
    Route::get('/appointments/get', [AppointmentsController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/me', [AppointmentsController::class, 'myAppointments'])->name('appointments.myAppointments');
    Route::get('/appointments/by-patient', [AppointmentsController::class, 'getAllPatientAppointments'])->name('appointments.byPatient');
    Route::get('/appointments/clinic-patients', [AppointmentsController::class, 'getClinicPatients'])->name('appointments.clinicPatients');
    Route::post('/appointments/add', [AppointmentsController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/confirm/{id}', [AppointmentsController::class, 'confirm'])->name('appointments.confirm');
    Route::patch('/appointments/cancel/{id}', [AppointmentsController::class, 'cancel'])->name('appointments.cancel');
    Route::patch('/appointments/reschedule/{id}', [AppointmentsController::class, 'reschedule'])->name('appointments.reschedule');

    /* Rutas asociadas al manejo de los perfiles de los pacientes */
    Route::get('/patients/get/ages', [PatientProfilesController::class, 'getPatientsAge'])->name('patients.getAges');
    Route::get('/patients/get/closer', [PatientProfilesController::class, 'getPatientsCloseToArea'])->name('patients.closer');

    /* Rutas asociadas al manejo de las preferencias de notificaciones */
    Route::patch('/patients/update-notifications-preferences', [PatientProfilesController::class, 'updateNotificationsPreferences'])->name('patients.updateNotificationsPreference');

    /* Rutas asociadas al manejo de enfermedades */
    Route::get('/diseases/me', [DiseaseController::class, 'getMyDiseases'])->name('diseases.getMyDiseases');
    Route::post('/diseases/assign', [DiseaseController::class, 'assignDiseases'])->name('diseases.assignDiseases');
    Route::get('/diseases/patient/{patientId}', [DiseaseController::class, 'getPatientDiseases'])->name('diseases.getPatientDiseases');
    Route::get('/diseases/{id}', [DiseaseController::class, 'show'])->name('diseases.show');

    /* Rutas asociadas al manejo de las reviews */
    Route::get('/reviews/get', [ReviewsController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/get/{id}', [ReviewsController::class, 'show'])->name('reviews.show');
    Route::get('/reviews/get/by-appointment/{id}', [ReviewsController::class, 'showByAppointment'])->name('reviews.byAppointment');
    Route::get('/reviews/get/by-clinic/{id}', [ReviewsController::class, 'showByClinic'])->name('reviews.byClinic');
    Route::get('/reviews/get/average/{id}', [ReviewsController::class, 'getAverage'])->name('reviews.average');
    Route::post('/reviews/add', [ReviewsController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/reply', [ReviewsController::class, 'addReply'])->name('reviews.reply');
    Route::patch('/reviews/edit/review/{id}', [ReviewsController::class, 'updateReview'])->name('reviews.updateReview');
    Route::patch('/reviews/edit/reply/{id}', [ReviewsController::class, 'updateReply'])->name('reviews.updateReply');
    Route::delete('/reviews/delete/{id}', [ReviewsController::class, 'destroy'])->name('reviews.delete');
});

// DeepSeek AI API route
Route::post('/deepseek', [DeepSeekController::class, 'chat'])->name('deepseek.chat');
Route::post('/deepseek/status', [DeepSeekController::class, 'checkStatus'])->name('deepseek.status');

// Ruta para generar consejos de salud usando IA
Route::post('/health-tips/generate', [HealthTipsController::class, 'generateTip'])->name('health-tips.generate');
