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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            if ($request->has('first_name')) {
                $user->first_name = $request->first_name;
            }

            if ($request->has('last_name')) {
                $user->last_name = $request->last_name;
            }

            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }

            if ($request->has('dui')) {
                $user->dui = $request->dui;
            }

            if ($request->has('date_of_birth')) {
                $user->date_of_birth = $request->date_of_birth;
            }

            if ($request->has('home_address')) {
                $user->address = $request->home_address;
            }

            $user->save();

            $patient_id = DB::table('patient_profiles')->where('user_id', $id)->value('id');

            $patient = PatientProfiles::find($patient_id);

            if ($request->has('emergency_contact_name')) {
                $patient->emergency_contact_name = $request->emergency_contact_name;
            }

            if ($request->has('emergency_contact_phone')) {
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

    public function updatePassword(Request $request, int $id): JsonResponse {
        log::info($request);

        if (!Auth::check() || !Auth::user()->verified || Auth::user()->id != $id) {
            return response()->json(['message' => 'Acceso no autorizado'], 401);
        }

        $rules = [
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8'
        ];

        $messages = [
            'current_password.required' => 'La contraseña actual es obligatoria',
            'current_password.string' => 'La contraseña actual debe ser una cadena de texto',
            'current_password.min' => 'La contraseña actual debe tener al menos 8 caracteres',
            'new_password.required' => 'La nueva contraseña es obligatoria',
            'new_password.string' => 'La nueva contraseña debe ser una cadena de texto',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres',
            'new_password.confirmed' => 'Las contraseñas no coinciden',
            'new_password_confirmation.required' => 'La confirmación de la contraseña es obligatoria',
            'new_password_confirmation.string' => 'La confirmación de la contraseña debe ser una cadena de texto',
            'new_password_confirmation.min' => 'La confirmación de la contraseña debe tener al menos 8 caracteres'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            if ($request->has('current_password')) {
                if (!password_verify($request->current_password, $user->password)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'La contraseña actual no es correcta'
                    ], 400);
                }
            }

            if ($request->has('new_password')) {
                $user->password = Hash::make($request->new_password);
            }

            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Contraseña actualizada correctamente'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar la contraseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request) {
        if (!Auth::check() || !Auth::user()->verified || Auth::user()->id != $request->id || $request->keyword !=  'ELIMINAR') {
            return response()->json(['message' => 'Acceso no autorizado'], 401);
        }

        try {
            $user = User::find($request->id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'Usuario eliminado correctamente'
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
