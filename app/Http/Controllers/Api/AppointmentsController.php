<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAppointmentsRequest;
use App\Mail\ConfirmAppointmentMail;
use App\Mail\ContactFormMail;
use App\Models\Appointments;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AppointmentsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse {
        if (!Auth::check() || Auth::user()->user_rol !== 'profesional' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        try {
            $clinic_id = DB::table('appointment_users')
                ->join('medical_clinics', 'appointment_users.clinic_id', '=', 'medical_clinics.id')
                ->join('professional_profiles', 'medical_clinics.professional_id', '=', 'professional_profiles.id')
                ->join('users', 'professional_profiles.user_id', '=', 'users.id')
                ->where('users.id', Auth::user()->id)
                ->value('clinic_id');

            $appointments = Appointments::getAllUserAppointments(Auth::user()->user_rol, $clinic_id);

            if ($appointments->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron citas'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $appointments
            ], 200);

        } catch (Exception $e) {
            Log::error('Error al obtener citas: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener citas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function myAppointments(): JsonResponse {
        if (!Auth::check() || Auth::user()->user_rol !== 'paciente' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        try {
            $pivot_id = DB::table('patient_profiles')
                ->where('user_id', Auth::user()->id)
                ->value('id');

            $appointments = Appointments::getAllUserAppointments(Auth::user()->user_rol, $pivot_id);

            if ($appointments->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron citas'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $appointments
            ], 200);

        } catch (Exception $e) {
            Log::error('Error al obtener citas: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener citas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllPatientAppointments(): JsonResponse {
        if (!Auth::check() || Auth::user()->user_rol !== 'paciente' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $patient_id = DB::table('patient_profiles')
            ->where('user_id', Auth::user()->id)
            ->value('id');

        try {
            $appointments = Appointments::getCompletedAppointments($patient_id);

            if ($appointments->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron citas completadas'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $appointments
            ], 201);

        } catch (Exception $e) {
            Log::error('Error al obtener citas: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener citas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse {
        if (!Auth::check() && Auth::user()->role !== 'paciente' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $rules = [
            'appointment_date' => 'required|date',
            'service_type' => 'required|string',
            'visit_reason' => 'nullable|string',
        ];

        $messages = [
            'appointment_date.required' => 'El día de la cita es obligatoria.',
            'service_type.required' => 'El tipo de servicio es obligatorio.',
            'service_type.string' => 'El tipo de servicio debe ser una cadena de texto.',
            'visit_reason.string' => 'El motivo de la visita debe ser una cadena de texto.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validation->errors(),
                'status' => false
            ], 422);
        }

        try {
            $appointment = Appointments::create([
                'appointment_date' => $request->appointment_date,
                'duration_minutes' => 0, // Default to 30 minutes if not provided
                'appointment_status' => 'pendiente_confirmacion', // Default status
                'service_type' => $request->service_type,
                'visit_reason' => $request->visit_reason,
                'patient_notes' => null,
                'professional_notes' => null,
                'cancellation_reason' => null,
                'reminder_sent' => false,
                'remind_me_at' => 0,
            ]);

            $patient_user_id = DB::table('patient_profiles')
                ->where('user_id', Auth::user()->id)
                ->value('id');

            $appointment_users = DB::table('appointment_users')
                ->insert([
                    'appointment_id' => $appointment->id,
                    'clinic_id' => $request->clinic_id,
                    'patient_user_id' => $patient_user_id,
                ]);

            if (!$appointment_users) {
                return response()->json([
                    'message' => 'Error al asignar la cita al usuario',
                    'status' => false
                ], 500);
            }

            return response()->json([
                'message' => 'Cita creada con éxito',
                'appointment' => $appointment,
                'status' => true
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al crear la cita',
                'error' => $e->getMessage(),
                'status' => false
            ], 500);
        }
    }

    public function confirm(Request $request, $appointment_id): JsonResponse {
        if (!Auth::check() || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $rules = [
            'appointment_status' => 'required|in:programada,cancelada_profesional',
            'service_type' => 'required|in:consultorio,domicilio',
            'appointment_date' => 'required|date',
            'clinic_name' => 'required|string',
            'patient_name' => 'required|string',
            'doctor_name' => 'required|string',
            'clinic_address' => 'required|string',
            'email' => 'required|email',
        ];

        $messages = [
            'appointment_status.required' => 'El estado de la cita es obligatorio.',
            'appointment_status.in' => 'El estado de la cita debe ser uno de los siguientes: programada,cancelada_profesional.',
            'service_type.required' => 'El tipo de servicio es obligatorio.',
            'service_type.string' => 'El tipo de servicio debe ser una cadena de texto.',
            'appointment_date.required' => 'La fecha de la cita es obligatoria.',
            'appointment_date.date' => 'La fecha de la cita debe ser una fecha válida.',
            'clinic_name.required' => 'El nombre de la clínica es obligatorio.',
            'clinic_name.string' => 'El nombre de la clínica debe ser una cadena de texto.',
            'patient_name.required' => 'El nombre completo es obligatorio.',
            'patient_name.string' => 'El nombre completo debe ser una cadena de texto.',
            'doctor_name.required' => 'El nombre del doctor es obligatorio.',
            'doctor_name.string' => 'El nombre del doctor debe ser una cadena de texto.',
            'clinic_address.required' => 'La dirección de la clínica es obligatoria.',
            'clinic_address.string' => 'La dirección de la clínica debe ser una cadena de texto.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección de correo electrónico válida.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validation->errors()->all(),
                'status' => false
            ], 422);
        }

        try {
            $appointment = DB::table('appointments')
                ->where('id', $appointment_id)
                ->update([
                    'appointment_status' => $request->appointment_status,
                    'service_type' => $request->service_type,
                    'appointment_date' => $request->appointment_date,
                ]);

            if (!$appointment) {
                return response()->json([
                    'message' => 'Error al aprobar la cita',
                    'status' => false
                ], 500);
            }

            $this->sendMail($request, "Confirmación de cita");

            return response()->json([
                'message' => 'Cita aprobada con éxito',
                'status' => true
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al aprobar la cita',
                'error' => $e->getMessage(),
                'status' => false
            ], 500);
        }
    }

    public function cancel(Request $request, $appointment_id): JsonResponse {
        if (!Auth::check() || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        Auth::user()->user_rol == 'paciente' ?
            $request->appointment_status = 'cancelada_paciente' : $request->appointment_status = 'cancelada_profesional';

        $rules = [
            'cancellation_reason' => 'required|string',
            'service_type' => 'required|in:consultorio,domicilio',
            'appointment_date' => 'required|date',
            'clinic_name' => 'required|string',
            'patient_name' => 'required|string',
            'doctor_name' => 'required|string',
            'clinic_address' => 'required|string',
            'email' => 'required|email',
        ];

        $messages = [
            'cancellation_reason.required' => 'El motivo de la cancelación es obligatorio.',
            'cancellation_reason.string' => 'El motivo de la cancelación debe ser una cadena de texto.',
            'service_type.required' => 'El tipo de servicio es obligatorio.',
            'service_type.string' => 'El tipo de servicio debe ser una cadena de texto.',
            'appointment_date.required' => 'La fecha de la cita es obligatoria.',
            'appointment_date.date' => 'La fecha de la cita debe ser una fecha válida.',
            'clinic_name.required' => 'El nombre de la clínica es obligatorio.',
            'clinic_name.string' => 'El nombre de la clínica debe ser una cadena de texto.',
            'patient_name.required' => 'El nombre completo es obligatorio.',
            'patient_name.string' => 'El nombre completo debe ser una cadena de texto.',
            'doctor_name.required' => 'El nombre del doctor es obligatorio.',
            'doctor_name.string' => 'El nombre del doctor debe ser una cadena de texto.',
            'clinic_address.required' => 'La dirección de la clínica es obligatoria.',
            'clinic_address.string' => 'La dirección de la clínica debe ser una cadena de texto.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección de correo electrónico válida.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validation->errors()->all(),
                'status' => false
            ], 422);
        }

        try {
            $appointment = DB::table('appointments')
                ->where('id', $appointment_id)
                ->update([
                    'appointment_status' => $request->appointment_status,
                    'cancellation_reason' => $request->cancellation_reason,
                ]);

            if (!$appointment) {
                return response()->json([
                    'message' => 'Error al cancelar la cita',
                    'status' => false
                ], 500);
            }

            $this->sendMail($request, 'Cancelación de cita');

            return response()->json([
                'message' => 'Cita cancelada con éxito',
                'status' => true
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al editar la cita',
                'error' => $e->getMessage(),
                'status' => false
            ], 500);
        }
    }

    public function reschedule(Request $request, $appointment_id): JsonResponse {
        if (!Auth::check() || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $rules = [
            'appointment_date' => 'required|date|after:today',
            'reschedule_reason' => 'required',
        ];

        $messages = [
            'appointment_date.required' => 'La fecha de la cita es obligatoria.',
            'appointment_date.date' => 'La fecha de la cita debe ser una fecha válida.',
            'reschedule_reason.required' => 'El motivo de la reprogramación es obligatorio.',
            'reschedule_reason.string' => 'El motivo de la reprogramación debe ser una cadena de texto.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validation->errors(),
                'status' => false
            ], 422);
        }

        try {
            $appointment = DB::table('appointments')
                ->where('id', $appointment_id)
                ->update([
                    'appointment_date' => $request->appointment_date,
                    'appointment_status' => 'programada',
                    'reschedule_reason' => $request->reschedule_reason,
                ]);

            if (!$appointment) {
                return response()->json([
                    'message' => 'Error al actualizar la cita',
                    'status' => false
                ], 500);
            }

            $this->sendMail($request, 'Reprogramación de cita');

            return response()->json([
                'message' => 'Cita reprogramada con éxito',
                'appointment' => $appointment,
                'status' => true
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al reprogramar la cita',
                'error' => $e->getMessage(),
                'status' => false
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointments $appointments) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointments $appointments) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentsRequest $request, Appointments $appointments) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointments $appointments) {
        //
    }

    private function sendMail(Request $request, string $subject): void {
        $details = [
            'subject' => $subject,
            'patient_name' => $request->patient_name,
            'doctor_name' => $request->doctor_name,
            'appointment_date' => $request->appointment_date,
            'clinic_name' => $request->clinic_name,
            'clinic_address' => $request->clinic_address,
            'email' => $request->email,
            'message' => 'Un placer saludarte. Queremos informarte que tu cita programada para el día ' .
                $request->appointment_date . " ha sido " . $request->appointment_status . ".",
        ];

        Mail::to($request->email)->send(new ConfirmAppointmentMail($details));
    }

    public function getClinicPatients(): JsonResponse {
        if (!Auth::check() || Auth::user()->user_rol !== 'profesional' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        try {
            // Get the clinic IDs owned by the authenticated professional
            $clinicIds = DB::table('medical_clinics')
                ->join('professional_profiles', 'medical_clinics.professional_id', '=', 'professional_profiles.id')
                ->where('professional_profiles.user_id', Auth::user()->id)
                ->pluck('medical_clinics.id');
                
            if ($clinicIds->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron clínicas asociadas a este profesional'
                ], 404);
            }

            // Get unique patients who have appointments at these clinics
            $patients = DB::table('appointment_users')
                ->join('appointments', 'appointment_users.appointment_id', '=', 'appointments.id')
                ->join('patient_profiles', 'appointment_users.patient_user_id', '=', 'patient_profiles.id')
                ->join('users', 'patient_profiles.user_id', '=', 'users.id')
                ->select(
                    'users.id as user_id',
                    'patient_profiles.id as patient_id',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.phone',
                    'users.profile_photo_path',
                    'users.date_of_birth',
                    'users.gender',
                    'users.address',
                    'patient_profiles.emergency_contact_name',
                    'patient_profiles.emergency_contact_phone'
                )
                ->whereIn('appointment_users.clinic_id', $clinicIds)
                ->groupBy(
                    'users.id',
                    'patient_profiles.id',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.phone',
                    'users.profile_photo_path',
                    'users.date_of_birth',
                    'users.gender',
                    'users.address',
                    'patient_profiles.emergency_contact_name',
                    'patient_profiles.emergency_contact_phone'
                )
                ->get();

            if ($patients->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron pacientes con citas en sus clínicas'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $patients
            ], 200);

        } catch (Exception $e) {
            Log::error('Error al obtener pacientes de la clínica: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener pacientes de la clínica',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPatientClinics(): JsonResponse {
        if (!Auth::check() || Auth::user()->user_rol !== 'paciente' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        try {
            // Get the patient profile ID for the authenticated user
            $patientId = DB::table('patient_profiles')
                ->where('user_id', Auth::user()->id)
                ->value('id');
                
            if (!$patientId) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontró el perfil de paciente'
                ], 404);
            }

            // Get unique clinics where the patient has had appointments
            $clinics = DB::table('appointment_users')
                ->join('medical_clinics', 'appointment_users.clinic_id', '=', 'medical_clinics.id')
                ->join('professional_profiles', 'medical_clinics.professional_id', '=', 'professional_profiles.id')
                ->join('users', 'professional_profiles.user_id', '=', 'users.id')
                ->select(
                    'medical_clinics.id as clinic_id',
                    'medical_clinics.clinic_name',
                    'medical_clinics.description',
                    'medical_clinics.address',
                    'medical_clinics.city_id',
                    'medical_clinics.clinic_latitude as latitude',
                    'medical_clinics.clinic_longitude as longitude',
                    'medical_clinics.facade_photo',
                    'medical_clinics.waiting_room_photo',
                    'medical_clinics.office_photo',
                    'users.id as professional_id',
                    'users.first_name as professional_first_name',
                    'users.last_name as professional_last_name',
                    'users.profile_photo_path as professional_photo',
                    'users.email as professional_email',
                    'users.phone as professional_phone',
                    DB::raw('COUNT(DISTINCT appointment_users.appointment_id) as total_appointments')
                )
                ->where('appointment_users.patient_user_id', $patientId)
                ->groupBy(
                    'medical_clinics.id',
                    'medical_clinics.clinic_name',
                    'medical_clinics.description',
                    'medical_clinics.address',
                    'medical_clinics.city_id',
                    'medical_clinics.clinic_latitude', 
                    'medical_clinics.clinic_longitude',
                    'medical_clinics.facade_photo',
                    'medical_clinics.waiting_room_photo',
                    'medical_clinics.office_photo',
                    'users.id',
                    'users.first_name',
                    'users.last_name',
                    'users.profile_photo_path',
                    'users.email',
                    'users.phone'
                )
                ->get();

            if ($clinics->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron clínicas visitadas por este paciente'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'total' => $clinics->count(),
                'data' => $clinics
            ], 200);

        } catch (Exception $e) {
            Log::error('Error al obtener clínicas del paciente: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener clínicas del paciente',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
