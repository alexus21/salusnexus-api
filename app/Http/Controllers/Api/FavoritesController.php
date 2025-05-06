<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFavoritesRequest;
use App\Models\Favorites;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoritesController extends Controller {
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
    public function store(Request $request): JsonResponse {
        if (!Auth::check() || Auth::user()->user_rol != 'paciente' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado'], 401);
        }

        $favorite = Favorites::create([
            'clinic_id' => $request->clinic_id,
            'patient_id' => $request->patient_id,
        ]);

        return response()->json([
            'status' => true,
            'data' => $favorite
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Favorites $favorites) {
        //
    }

    public function getMyFavorites(): JsonResponse {
        if (!Auth::check() || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado'], 401);
        }

        try{
            $patient_id = DB::table('patient_profiles')
                ->where('user_id', Auth::user()->id)
                ->value('id');

            $favorites = Favorites::where('patient_id', $patient_id)
                ->join('medical_clinics', 'favorites.clinic_id', '=', 'medical_clinics.id')
                ->join('cities', 'medical_clinics.city_id', '=', 'cities.id')
                ->select(['clinic_id', 'clinic_name', 'clinic_address_reference', 'cities.id', 'cities.name', 'facade_photo', 'waiting_room_photo', 'office_photo'])
                ->get();

            if ($favorites->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron favoritos'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Favoritos obtenidos correctamente',
                'data' => $favorites
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los favoritos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Favorites $favorites) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFavoritesRequest $request, Favorites $favorites) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $favorites) {
        if (!Auth::check() || Auth::user()->user_rol != 'paciente' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado'], 401);
        }

        $favorite = Favorites::where('clinic_id', $favorites->clinic_id)
            ->where('patient_id', $favorites->patient_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'status' => true,
                'message' => 'Favorito eliminado correctamente'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Favorito no encontrado'
        ], 404);
    }
}
