<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientProfilesRequest;
use App\Http\Requests\UpdatePatientProfilesRequest;
use App\Models\PatientProfiles;
use App\Models\User;
use App\Rules\DUIRule;
use App\Rules\EmailRule;
use App\Rules\PhoneNumberRule;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

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
    public function create(Request $request) {
        //
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

    public function verifyPatientAccount(Request $request): JsonResponse {
        $rules = [
            'home_address' => 'required|string',
            'home_latitude' => 'required|numeric',
            'home_longitude' => 'required|numeric',
            'home_address_reference' => 'nullable|string',
            'emergency_contact_name' => 'required|string',
            'emergency_contact_phone' => 'required|string',
            'profile_photo_path' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'dui' => ['required', 'string', 'unique:users,dui', new DUIRule()],
        ];

        $messages = [
            'home_address.required' => 'La dirección de casa es requerida',
            'home_address.string' => 'La dirección de casa debe ser texto',
            'home_latitude.required' => 'La latitud de casa es requerida',
            'home_latitude.numeric' => 'La latitud de casa debe ser un número',
            'home_longitude.required' => 'La longitud de casa es requerida',
            'home_longitude.numeric' => 'La longitud de casa debe ser un número',
            'home_address_reference.string' => 'La referencia de la dirección de casa debe ser texto',
            'emergency_contact_name.required' => 'El nombre del contacto de emergencia es requerido',
            'emergency_contact_name.string' => 'El nombre del contacto de emergencia debe ser texto',
            'emergency_contact_phone.required' => 'El teléfono del contacto de emergencia es requerido',
            'emergency_contact_phone.string' => 'El teléfono del contacto de emergencia debe ser texto',
            'profile_photo_path.required' => 'La foto de perfil es requerida',
            'profile_photo_path.image' => 'La foto de perfil debe ser una imagen válida',
            'profile_photo_path.mimes' => 'La foto de perfil debe ser un archivo de tipo jpeg, png o jpg',
            'profile_photo_path.max' => 'La foto de perfil no debe exceder los 2MB',
            'dui.required' => 'El DUI es requerido',
            'dui.string' => 'El DUI debe ser texto',
            'dui.unique' => 'El DUI ya está en uso',
            'dui.regex' => 'El DUI no tiene un formato válido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ], 422);
        }

        try {
            if(!$request->hasFile('profile_photo_path')) {
                return response()->json([
                    'success' => false,
                    'message' => 'La imagen de perfil es requerida'
                ], 422);
            }

            // Generar nombre único para la imagen
            $imageName = Str::uuid() . '.' . $request->profile_photo_path->extension();

            // Obtener ruta anterior de la imagen
            $oldImagePath = DB::table('users')
                ->where('id', Auth::id())
                ->value('profile_photo_path');

            if (!Storage::disk('s3')->exists('images')) {
                Storage::disk('s3')->makeDirectory('images');
            }

            // Verificar y eliminar la imagen anterior si existe
            if ($oldImagePath && Storage::disk('s3')->exists($oldImagePath)) {
                Storage::disk('s3')->delete($oldImagePath);
            }

            // Guardar la imagen en el disco (puedes usar public, s3, etc.)
            $path = $request->file('profile_photo_path')->storeAs('images', $imageName, 's3');

            // URL pública de la imagen (si está en storage/public)
            $url = Storage::disk('s3')->url($path);

            // Guardar la información del usuario en la base de datos
            DB::table('patient_profiles')->where('user_id', Auth::user()->id)->update([
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
            ]);

            DB::table('users')->where('id', Auth::user()->id)->update([
                'address' => $request->home_address,
                'latitude' => $request->home_latitude,
                'longitude' => $request->home_longitude,
                'address_reference' => $request->home_address_reference,
                'profile_photo_path' => $path,
                'dui' => $request->dui,
                'verified' => true,
                'email_verified_at' => Carbon::now(),
            ]);

            // Crear suscripción gratuita
            (new SubscriptionsController())->store(Auth::user()->id, 'paciente', null);

            $user = ((new User())->getUserInfoByItsId(Auth::user()->id));

            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'Perfil verificado correctamente',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la imagen: ' . $e->getMessage()
            ], 500);
        }
    }
}
