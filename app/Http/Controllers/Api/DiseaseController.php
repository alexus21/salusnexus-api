<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use App\Models\PatientDisease;
use App\Models\PatientProfiles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DiseaseController extends Controller {
    /**
     * Obtener todas las enfermedades.
     */
    public function index(): JsonResponse {
        $diseases = Disease::all();

        return response()->json([
            'status' => true,
            'message' => 'Enfermedades obtenidas correctamente.',
            'diseases' => $diseases
        ]);
    }

    /**
     * Obtener una enfermedad específica por ID.
     */
    public function show($id): JsonResponse {
        $disease = Disease::find($id);

        if (!$disease) {
            return response()->json([
                'status' => false,
                'message' => 'Enfermedad no encontrada.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Enfermedad obtenida correctamente.',
            'disease' => $disease
        ]);
    }

    /**
     * Obtener las enfermedades del paciente autenticado.
     */
    public function getMyDiseases(): JsonResponse {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'No autorizado',
            ], 401);
        }

        $user = Auth::user();

        // Verificar que sea un paciente
        if ($user->user_rol !== 'paciente') {
            return response()->json([
                'status' => false,
                'message' => 'Solo los pacientes pueden acceder a esta información.',
            ], 403);
        }

        $patientProfile = PatientProfiles::where('user_id', $user->id)->first();

        if (!$patientProfile) {
            return response()->json([
                'status' => false,
                'message' => 'Perfil de paciente no encontrado.',
            ], 404);
        }

        // Obtener enfermedades con la fecha de reporte
        $diseases = $patientProfile->diseases()->with('patients')->get();

        return response()->json([
            'status' => true,
            'message' => 'Enfermedades del paciente obtenidas correctamente.',
            'diseases' => $diseases
        ]);
    }

    /**
     * Asignar enfermedades a un paciente.
     */
    public function assignDiseases(Request $request): JsonResponse {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'No autorizado',
            ], 401);
        }

        $rules = [
            'disease_ids' => 'required|array',
            'disease_ids.*' => 'exists:diseases,id',
        ];

        $messages = [
            'disease_ids.required' => 'Es necesario proporcionar al menos una enfermedad.',
            'disease_ids.array' => 'Las enfermedades deben enviarse como un arreglo.',
            'disease_ids.*.exists' => 'Una o más enfermedades seleccionadas no existen.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación.',
                'errors' => $validation->errors(),
            ], 422);
        }

        $user = Auth::user();

        // Verificar que sea un paciente
        if ($user->user_rol !== 'paciente') {
            return response()->json([
                'status' => false,
                'message' => 'Solo los pacientes pueden actualizar esta información.',
            ], 403);
        }

        $patientProfile = PatientProfiles::where('user_id', $user->id)->first();

        if (!$patientProfile) {
            return response()->json([
                'status' => false,
                'message' => 'Perfil de paciente no encontrado.',
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Eliminar las relaciones anteriores
            PatientDisease::where('patient_profile_id', $patientProfile->id)->delete();

            // Crear nuevas relaciones
            foreach ($request->disease_ids as $diseaseId) {
                PatientDisease::create([
                    'patient_profile_id' => $patientProfile->id,
                    'disease_id' => $diseaseId,
                    'reported_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Enfermedades asignadas correctamente.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Error al asignar enfermedades.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener las enfermedades de un paciente específico (para profesionales).
     */
    public function getPatientDiseases($patientId): JsonResponse {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'No autorizado',
            ], 401);
        }

        $user = Auth::user();

        // Verificar que sea un profesional
        if ($user->user_rol !== 'profesional') {
            return response()->json([
                'status' => false,
                'message' => 'Solo los profesionales pueden acceder a esta información.',
            ], 403);
        }

        $patientProfile = PatientProfiles::find($patientId);

        if (!$patientProfile) {
            return response()->json([
                'status' => false,
                'message' => 'Perfil de paciente no encontrado.',
            ], 404);
        }

        // Obtener enfermedades con la fecha de reporte
        $diseases = $patientProfile->diseases()->get();

        return response()->json([
            'status' => true,
            'message' => 'Enfermedades del paciente obtenidas correctamente.',
            'diseases' => $diseases
        ]);
    }
}
