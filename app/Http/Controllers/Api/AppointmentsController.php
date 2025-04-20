<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAppointmentsRequest;
use App\Models\Appointments;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppointmentsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
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
            'appointment_datetime' => 'required|date',
            'service_type' => 'required|string',
            'visit_reason' => 'nullable|string',
            'reminder_sent' => 'boolean',
            'remind_me_at' => 'nullable|date',
        ];

        $messages = [
            'appointment_datetime.required' => 'La fecha y hora de la cita son obligatorias.',
            'appointment_datetime.date' => 'La fecha y hora de la cita no son válidas.',
            'service_type.required' => 'El tipo de servicio es obligatorio.',
            'service_type.string' => 'El tipo de servicio debe ser una cadena de texto.',
            'visit_reason.string' => 'El motivo de la visita debe ser una cadena de texto.',
            'reminder_sent.boolean' => 'El recordatorio enviado debe ser verdadero o falso.',
            'remind_me_at.date' => 'La fecha y hora del recordatorio no son válidas.',
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
                'appointment_datetime' => $request->input('appointment_datetime'),
                'duration_minutes' => $request->input('duration_minutes', 30), // Default to 30 minutes if not provided
                'appointment_status' => 'pending', // Default status
                'service_type' => $request->input('service_type'),
                'visit_reason' => $request->input('visit_reason'),
                'patient_notes' => $request->input('patient_notes'),
                'professional_notes' => $request->input('professional_notes'),
                'cancellation_reason' => null,
                'reminder_sent' => $request->input('reminder_sent', false),
                'remind_me_at' => $request->input('remind_me_at'),
            ]);

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
