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
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\NoBrowserCacheMiddleware;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return response()->json([
        'message' => 'Página no encontrada. Si tienes dudas contacta con el administrador del sitio.',
    ], 404);
});

/*Route::get('/test-welcome-email', function () {
    $testEmail = env('MAIL_TEST_ADDRESS', 'test@example.com');

    $details = [
        'subject' => 'Bienvenido a SalusNexus, Angel Vasquez.',
        'name' => 'SalusNexus',
        'email' => $testEmail,
        'message' => 'Es un gusto tenerte con nosotros.
        Estamos aquí para ayudarte a cuidar de tu salud y bienestar.
        Si tienes alguna pregunta o necesitas asistencia, no dudes en contactarnos.',
    ];

    Mail::to($testEmail)->send(new ContactFormMail($details));

    return response()->json([
        'message' => 'Correo de prueba enviado con éxito',
        'status' => true
    ]);
});*/

Route::get('/test-reset-password', function () {
    $testEmail = env('MAIL_TEST_ADDRESS', 'test@example.com');

    $details = [
        'subject' => 'Restablecimiento de Contraseña - SalusNexus',
        'email' => $testEmail,
        'reset_link' => 'https://salusnexus.online/reset-password',
        'message' => 'Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en SalusNexus.

        Haz clic en el botón de abajo para crear una nueva contraseña. Si no has solicitado este cambio, puedes ignorar este correo y tu contraseña seguirá siendo la misma.',
    ];

    $mailable = new \Illuminate\Mail\Mailable();
    $mailable->subject($details['subject']);
    $mailable->view('reset-password', ['details' => $details]);
    Mail::to($testEmail)->send($mailable);

    return response()->json([
        'message' => 'Correo de restablecimiento de contraseña enviado con éxito',
        'status' => true
    ]);
});

/*Route::get('/test-appointment-email', function () {
    $testEmail = env('MAIL_TEST_ADDRESS', 'test@example.com');

    $details = [
        'subject' => 'Confirmación de Cita - SalusNexus',
        'patient_name' => 'Angel Vasquez',
        'doctor_name' => 'Dr. Carlos Mendoza',
        'specialty' => 'Medicina General',
        'appointment_date' => '15 de Julio de 2023',
        'appointment_time' => '10:30 AM',
        'clinic_address' => 'Clínica Médica Central, Calle Principal #123, San Salvador',
        'notes' => 'Por favor traer su carnet de vacunación y llegar 15 minutos antes.',
        'calendar_link' => 'https://salusnexus.online/calendar/add?id=123456',
        'reschedule_link' => 'https://salusnexus.online/appointments/reschedule?id=123456',
        'map_image_url' => 'https://maps.googleapis.com/maps/api/staticmap?center=San+Salvador&zoom=14&size=600x300&markers=color:red|label:A|San+Salvador&key=' . env('GOOGLE_MAPS_API_KEY'),
        'map_link' => 'https://maps.google.com/?q=San+Salvador+El+Salvador'
    ];

    $mailable = new \Illuminate\Mail\Mailable();
    $mailable->subject($details['subject']);
    $mailable->view('appointment-confirmation', ['details' => $details]);
    Mail::to($testEmail)->send($mailable);

    return response()->json([
        'message' => 'Correo de confirmación de cita enviado con éxito',
        'status' => true
    ]);
});*/


Route::get('/test-subscription-email', function () {
    $testEmail = env('MAIL_TEST_ADDRESS', 'test@example.com');

    $details = [
        'subject' => 'Suscripción Premium Activada - SalusNexus',
        'customer_name' => 'Angel Vasquez',
        'email' => $testEmail,
        'plan_name' => 'Plan Premium Paciente',
        'plan_price' => '$4.99/mes (Facturado anualmente a $59.88)',
        'start_date' => '12 de Julio de 2023',
        'next_billing_date' => '12 de Julio de 2024',
        'payment_method' => 'Visa terminada en 4242',
        'dashboard_link' => 'https://salusnexus.online/dashboard'
    ];

    $mailable = new \Illuminate\Mail\Mailable();
    $mailable->subject($details['subject']);
    $mailable->view('subscription-confirmation', ['details' => $details]);
    Mail::to($testEmail)->send($mailable);

    return response()->json([
        'message' => 'Correo de confirmación de suscripción premium enviado con éxito',
        'status' => true
    ]);
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
    /* Rutas asociadas al manejo de los usuarios */
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::patch('/patients/update/{id}', [PatientProfilesController::class, 'update'])->name('patients.update');
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
    Route::get('/subscriptions/me', [SubscriptionsController::class, 'mySubscription'])->name('subscriptions.mySubscription');

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
    Route::post('/appointments/add', [AppointmentsController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/confirm/{id}', [AppointmentsController::class, 'confirm'])->name('appointments.confirm');
    Route::patch('/appointments/cancel/{id}', [AppointmentsController::class, 'cancel'])->name('appointments.cancel');
    Route::patch('/appointments/reschedule/{id}', [AppointmentsController::class, 'reschedule'])->name('appointments.reschedule');

    /* Rutas asociadas al manejo de los perfiles de los pacientes */
    Route::get('/patients/get/ages', [PatientProfilesController::class, 'getPatientsAge'])->name('patients.getAges');
    Route::get('/patients/get/closer', [PatientProfilesController::class, 'getPatientsCloseToArea'])->name('patients.closer');
});
