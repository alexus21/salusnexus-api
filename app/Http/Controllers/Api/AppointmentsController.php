<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAppointmentsRequest;
use App\Models\Appointments;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        log::info('Request data: ', $request->all());

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
                'appointment_status' => 'programada', // Default status
                'service_type' => $request->service_type,
                'visit_reason' => $request->visit_reason,
                'patient_notes' => null,
                'professional_notes' => null,
                'cancellation_reason' => null,
                'reminder_sent' => false,
                'remind_me_at' => 0,
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
