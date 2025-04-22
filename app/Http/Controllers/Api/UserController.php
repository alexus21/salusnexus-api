<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientProfiles;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse {
        log::info($id);

        if (!Auth::check()) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $user
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse {
        if (!Auth::check() || !Auth::user()->verified || Auth::user()->id != $id) {
            return response()->json(['message' => 'Acceso no autorizado'], 401);
        }

        log::info($request);

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            if($request->has('first_name')) {
                $user->first_name = $request->first_name;
            }

            if($request->has('last_name')) {
                $user->last_name = $request->last_name;
            }

            if($request->has('phone')) {
                $user->phone = $request->phone;
            }

            if($request->has('dui')) {
                $user->dui = $request->dui;
            }

            if($request->has('date_of_birth')) {
                $user->date_of_birth = $request->date_of_birth;
            }

            if($request->has('home_address')) {
                $user->address = $request->home_address;
            }

            $user->save();

            $patient_id = DB::table('patient_profiles')->where('user_id', $id)->value('id');
            log::info($patient_id);

            $patient = PatientProfiles::find($patient_id);

            if($request->has('emergency_contact_name')) {
                $patient->emergency_contact_name = $request->emergency_contact_name;
            }

            if($request->has('emergency_contact_phone')) {
                $patient->emergency_contact_phone = $request->emergency_contact_phone;
            }

            $patient->save();

            $user = (new User())->getUserProfile($id);

            return response()->json([
                'status' => true,
                'message' => 'Información actualizada correctamente',
                'data' => $user
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }
}
