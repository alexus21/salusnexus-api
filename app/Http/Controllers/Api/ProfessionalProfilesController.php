<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfessionalProfilesRequest;
use App\Http\Requests\UpdateProfessionalProfilesRequest;
use App\Models\ProfessionalProfiles;
use App\Models\Subscriptions;
use App\Models\User;
use App\Rules\DUIRule;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

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
    public function create(Request $request) {
        //
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

    public function verifyProfessionalAccount(Request $request): JsonResponse {
        $rules = [
            'license_number' => 'required|string',
            'biography' => 'required|string',
            'clinic_address' => 'required|string',
            'clinic_address_reference' => 'nullable|string',
            'clinic_city_id' => 'required|string',
            'clinic_latitude' => 'required|string',
            'clinic_longitude' => 'required|string',
            'home_visits' => 'required|boolean',
            'years_of_experience' => 'required|integer',
            'website_url' => 'nullable|string',
            'profile_photo_path' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'dui' => ['required', 'string', 'unique:users,dui', new DUIRule()],
        ];

        $messages = [
            'license_number.required' => 'El número de licencia es requerido.',
            'license_number.string' => 'El número de licencia debe ser una cadena de texto.',
            'biography.required' => 'La biografía es requerida.',
            'biography.string' => 'La biografía debe ser una cadena de texto.',
            'clinic_address_1.required' => 'La dirección de la clínica 1 es requerida.',
            'clinic_address_1.string' => 'La dirección de la clínica 1 debe ser una cadena de texto.',
            'clinic_latitude.required' => 'La latitud de la clínica es requerida.',
            'clinic_latitude.string' => 'La latitud de la clínica debe ser una cadena de texto.',
            'clinic_longitude.required' => 'La longitud de la clínica es requerida.',
            'clinic_longitude.string' => 'La longitud de la clínica debe ser una cadena de texto.',
            'home_visits.required' => 'Las visitas a domicilio son requeridas.',
            'home_visits.boolean' => 'Las visitas a domicilio deben ser un valor booleano.',
            'years_of_experience.required' => 'Los años de experiencia son requeridos.',
            'years_of_experience.integer' => 'Los años de experiencia deben ser un número entero.',
            'website_url.string' => 'La URL del sitio web debe ser una cadena de texto.',
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
            if(!$request->hasFile('profile_photo_path')){
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ninguna imagen'
                ], 400);
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
            DB::table('professional_profiles')->where('user_id', Auth::user()->id)->update([
                'license_number' => $request->license_number,
                'biography' => $request->biography,
                'home_visits' => true,
                'years_of_experience' => $request->years_of_experience,
                'website_url' => $request->website_url,
                'user_id' => Auth::user()->id,
            ]);

            DB::table('users')->where('id', Auth::user()->id)->update([
                'address' => $request->clinic_address,
                'address_reference' => $request->clinic_address_reference,
                'latitude' => $request->clinic_latitude,
                'longitude' => $request->clinic_longitude,
                'profile_photo_path' => $path,
                'dui' => $request->dui,
                'verified' => true,
                'email_verified_at' => Carbon::now(),
            ]);

            // Check if the user already has a subscription
            $existingSubscription = Subscriptions::where('user_id', Auth::user()->id)
                ->where('subscription_status', 'activa')
                ->first();

            if (!$existingSubscription) {
                // Crear suscripción gratuita
                (new SubscriptionsController())->store(Auth::user()->id, 'profesional', $request->subscription_period);
            }

            return response()->json([
                'success' => true,
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
