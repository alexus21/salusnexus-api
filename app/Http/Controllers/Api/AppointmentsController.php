<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAppointmentsRequest;
use App\Models\Appointments;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            $appointments = Appointments::getAllAppointments();

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
            $patient_id = DB::table('patient_profiles')
                ->where('user_id', Auth::user()->id)
                ->value('id');

            $appointments = Appointments::getAllUserAppointments($patient_id);

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

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
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

    public function confirmAppointment(Request $request, $appointment_id): JsonResponse {
        if (!Auth::check() && Auth::user()->role !== 'paciente' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $rules = [
            'appointment_status' => 'required|in:programada,cancelada_profesional',
            'service_type' => 'required|string',
            'appointment_date' => 'required|date',
        ];

        $messages = [
            'appointment_status.required' => 'El estado de la cita es obligatorio.',
            'appointment_status.in' => 'El estado de la cita debe ser uno de los siguientes: programada,cancelada_profesional.',
            'service_type.required' => 'El tipo de servicio es obligatorio.',
            'service_type.string' => 'El tipo de servicio debe ser una cadena de texto.',
            'appointment_date.required' => 'La fecha de la cita es obligatoria.',
            'appointment_date.date' => 'La fecha de la cita debe ser una fecha válida.',
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
                    'appointment_status' => $request->appointment_status,
                    'service_type' => $request->service_type,
                    'appointment_date' => $request->appointment_date,
                ]);

            if (!$appointment) {
                return response()->json([
                    'message' => 'Error al actualizar el estado de la cita',
                    'status' => false
                ], 500);
            }

            return response()->json([
                'message' => 'Cita cambiada con éxito',
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
}
