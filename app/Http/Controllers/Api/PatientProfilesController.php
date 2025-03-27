<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientProfilesRequest;
use App\Http\Requests\UpdatePatientProfilesRequest;
use App\Models\PatientProfiles;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PatientProfilesController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $user_id): JsonResponse {
        try {
            $patient = PatientProfiles::create([
                'date_of_birth' => $request->date_of_birth,
                'gender' => strtolower($request->gender),
                'home_address_1' => $request->home_address_1,
                'home_latitude' => $request->home_latitude,
                'home_longitude' => $request->home_longitude,
                'home_address_reference' => $request->home_address_2,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'user_id' => $user_id,
            ]);

            return response()->json([
                'message' => 'Perfil de paciente creado correctamente',
                'status' => true,
                'data' => $patient,
            ], 201); // Código HTTP 201: Recurso creado
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el perfil de paciente',
                'status' => false,
                'error' => $e->getMessage(),
            ], 500); // Código HTTP 500: Error interno del servidor
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientProfilesRequest $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PatientProfiles $patientProfiles) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PatientProfiles $patientProfiles) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientProfilesRequest $request, PatientProfiles $patientProfiles) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PatientProfiles $patientProfiles) {
        //
    }
}
