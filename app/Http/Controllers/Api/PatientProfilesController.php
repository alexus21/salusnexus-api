<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientProfilesRequest;
use App\Http\Requests\UpdatePatientProfilesRequest;
use App\Models\PatientProfiles;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
            $emergency_contact_phone = $this->formatPhoneNumber($request);

            $patient = PatientProfiles::create([
                'date_of_birth' => $request->date_of_birth,
                'gender' => strtolower($request->gender),
                'home_address_1' => $request->home_address_1,
                'home_latitude' => $request->home_latitude,
                'home_longitude' => $request->home_longitude,
                'home_address_reference' => $request->home_address_2,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $emergency_contact_phone,
                'user_id' => $user_id,
            ]);

            return response()->json([
                'message' => 'Perfil de paciente creado correctamente',
                'status' => true,
                'data' => $patient,
            ], 201); // Código HTTP 201: Recurso creado
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al crear el perfil de paciente',
                'status' => false,
                'error' => $e->getMessage(),
            ], 500); // Código HTTP 500: Error interno del servidor
        }
    }

    private function formatPhoneNumber(Request $request): array|JsonResponse|string|null {
        // Asegurarse de que el teléfono comience con el código de país "+503" (El Salvador)
        $emergency_contact_phone = str_starts_with($request->emergency_contact_phone, "+503") ? $request->emergency_contact_phone : "+503 " . $request->emergency_contact_phone;
        // Formatear el teléfono al estilo "+503 XXXX-XXXX"
        $emergency_contact_phone = preg_replace('/(\+503)\s?(\d{4})(\d{4})/', '$1 $2-$3', $emergency_contact_phone);

        // Verificar si el teléfono ya está registrado en la base de datos
        $user = DB::table('patient_profiles')
            ->select('emergency_contact_phone')
            ->where('emergency_contact_phone', $emergency_contact_phone)
            ->first();

        // Si el teléfono ya existe, devolver error
        if ($user) {
            return response()->json([
                'message' => 'El teléfono de emergencia ingresado ya está en uso',
                'status' => false,
                'errors' => ['telefono' => ['El teléfono de emergencia ingresado ya está en uso']]
            ], 400); // Código HTTP 400: Solicitud incorrecta
        }

        return $emergency_contact_phone;
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
