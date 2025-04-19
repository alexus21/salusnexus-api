<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFavoritesRequest;
use App\Http\Requests\UpdateFavoritesRequest;
use App\Models\Favorites;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
