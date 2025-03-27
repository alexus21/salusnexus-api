<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfessionalProfilesRequest;
use App\Http\Requests\UpdateProfessionalProfilesRequest;
use App\Models\ProfessionalProfiles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfessionalProfilesController extends Controller {
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
        $professional = ProfessionalProfiles::create([
            'license_number' => $request->license_number,
            'biography' => $request->biography,
            'clinic_name' => $request->clinic_name,
            'clinic_address_1' => $request->clinic_address_1,
            'clinic_address_2' => $request->clinic_address_2,
            'clinic_city_id' => $request->clinic_city_id,
            'clinic_latitude' => $request->clinic_latitude,
            'clinic_longitude' => $request->clinic_longitude,
            'home_visits' => $request->home_visits,
            'years_of_experience' => $request->years_of_experience,
            'website_url' => $request->website_url,
            'user_id' => $user_id,
        ]);

        if($professional) {
            return response()->json([
                'message' => 'Perfil de profesional creado correctamente',
                'status' => true,
                'data' => $professional
            ], 201); // Código HTTP 201: Recurso creado
        } else {
            return response()->json([
                'message' => 'Error al crear el perfil de profesional',
                'status' => false,
            ], 500); // Código HTTP 500: Error interno del servidor
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProfessionalProfilesRequest $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProfessionalProfiles $professionalProfiles) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProfessionalProfiles $professionalProfiles) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfessionalProfilesRequest $request, ProfessionalProfiles $professionalProfiles) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProfessionalProfiles $professionalProfiles) {
        //
    }
}
