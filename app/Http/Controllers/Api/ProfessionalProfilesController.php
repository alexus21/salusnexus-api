<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfessionalProfilesRequest;
use App\Http\Requests\UpdateProfessionalProfilesRequest;
use App\Models\MedicalLicenses;
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
use Illuminate\Support\Facades\Log;
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
            // Datos para la licencia
            'license_number' => 'required|string',
            'license_authority' => 'required|string|in:Ministerio de Salud,Consejo Superior de Salud Pública,Departamento de Comercio EE. UU.,OMS/OPS',
            'issue_date' => 'required|date',
            'expiration_date' => 'required|date|after_or_equal:issue_date',
            'speciality_id' => 'required|integer',
            'license_image_path' => 'required|image|mimes:jpeg,png,jpg|max:2048',

            // Datos del usuario
            'home_address' => 'required|string',
            'home_latitude' => 'required',
            'home_longitude' => 'required',
            'home_address_reference' => 'nullable|string', // <- Por si lo busca la policía (?
            'profile_photo_path' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'dui' => ['required', 'string', 'unique:users,dui', new DUIRule()],

            // Datos del perfil profesional
            'biography' => 'required|string',
            'years_of_experience' => 'required|integer',
            'clinic_address' => 'required|string',
            'clinic_address_reference' => 'nullable|string',
            'city_id' => 'required',
            'clinic_latitude' => 'required',
            'clinic_longitude' => 'required',
            'website_url' => 'nullable|string',
        ];

        $messages = [
            'license_number.required' => 'El número de licencia es requerido.',
            'license_number.string' => 'El número de licencia debe ser una cadena de texto.',
            'license_authority.required' => 'La autoridad de licencia es requerida.',
            'license_authority.string' => 'La autoridad de licencia debe ser una cadena de texto.',
            'license_authority.in' => 'La autoridad de licencia debe ser una de las siguientes: Ministerio de Salud, Consejo Superior de Salud Pública, Departamento de Comercio EE. UU., OMS/OPS.',
            'issue_date.required' => 'La fecha de emisión es requerida.',
            'issue_date.date' => 'La fecha de emisión debe ser una fecha válida.',
            'expiration_date.required' => 'La fecha de expiración es requerida.',
            'expiration_date.date' => 'La fecha de expiración debe ser una fecha válida.',
            'expiration_date.after_or_equal' => 'La fecha de expiración debe ser igual o posterior a la fecha de emisión.',
            'speciality_id.required' => 'La especialidad es requerida.',
            'speciality_id.integer' => 'La especialidad debe ser un número entero.',
            'license_image_path.required' => 'La imagen de la licencia es requerida.',
            'license_image_path.image' => 'La imagen de la licencia debe ser una imagen válida.',
            'license_image_path.mimes' => 'La imagen de la licencia debe ser un archivo de tipo jpeg, png o jpg.',
            'license_image_path.max' => 'La imagen de la licencia no debe exceder los 2MB.',
            'home_address.required' => 'La dirección de casa es requerida.',
            'home_address.string' => 'La dirección de casa debe ser una cadena de texto.',
            'home_latitude.required' => 'La latitud de casa es requerida.',
            'home_latitude.string' => 'La latitud de casa debe ser una cadena de texto.',
            'home_longitude.required' => 'La longitud de casa es requerida.',
            'home_longitude.string' => 'La longitud de casa debe ser una cadena de texto.',
            'home_address_reference.string' => 'La referencia de la dirección de casa debe ser una cadena de texto.',

            'biography.required' => 'La biografía es requerida.',
            'biography.string' => 'La biografía debe ser una cadena de texto.',
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

            'clinic_address.required' => 'La dirección de la clínica es requerida.',
            'clinic_address.string' => 'La dirección de la clínica debe ser una cadena de texto.',
            'clinic_address_reference.string' => 'La referencia de la dirección de la clínica debe ser una cadena de texto.',
            'clinic_latitude.required' => 'La latitud de la clínica es requerida.',
            'clinic_latitude.string' => 'La latitud de la clínica debe ser una cadena de texto.',
            'clinic_longitude.required' => 'La longitud de la clínica es requerida.',
            'clinic_longitude.string' => 'La longitud de la clínica debe ser una cadena de texto.',
            'city_id.required' => 'La ciudad es requerida.',
            'city_id.exists' => 'La ciudad seleccionada no es válida.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ], 422);
        }

        try {
            // URL pública de la imagen (si está en storage/public)
            $pfp_path = $this->saveProfilePhoto($request);
            $license_path = $this->saveLicensePhoto($request);

            // Guardar la información del usuario en la base de datos
            DB::table('professional_profiles')->where('user_id', Auth::user()->id)->update([
                'license_number' => $request->license_number,
                'biography' => $request->biography,
                'home_visits' => true,
                'years_of_experience' => $request->years_of_experience,
                'website_url' => $request->website_url,
                'user_id' => Auth::user()->id,
            ]);

            $professional_id = DB::table('professional_profiles')
                ->where('user_id', Auth::user()->id)
                ->value('id');

            log::info($professional_id);

            $medicalLicense = new MedicalLicenses();
            $medicalLicense = $medicalLicense->create([
                'professional_profile_id' => $professional_id,
                'license_number' => $request->license_number,
                'licensing_authority' => $request->license_authority,
                'issue_date' => $request->issue_date,
                'expiration_date' => $request->expiration_date,
                'license_image_path' => $license_path,
            ]);

            $medicalLicense->save();

            DB::table('users')->where('id', Auth::user()->id)->update([
                'address' => $request->home_address,
                'address_reference' => $request->home_address_reference,
                'latitude' => $request->home_latitude,
                'longitude' => $request->home_longitude,
                'profile_photo_path' => $pfp_path,
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

            (new MedicalClinicController())->store($request);

            $user = ((new User())->getUserInfoByItsId(Auth::user()->id));

            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Perfil verificado correctamente',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveProfilePhoto(Request $request): JsonResponse|string {
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
        Storage::disk('s3')->url($path);

        return $path;
    }

    private function saveLicensePhoto(Request $request): JsonResponse|string {
        if(!$request->hasFile('license_image_path')){
            return response()->json([
                'success' => false,
                'message' => 'No se encontró ninguna imagen'
            ], 400);
        }

        // Generar nombre único para la imagen
        $imageName = Str::uuid() . '.' . $request->license_image_path->extension();

        // Obtener ruta anterior de la imagen
        /*$oldImagePath = DB::table('medical_licenses')
            ->where('id', Auth::id())
            ->value('license_image_path');*/

        if (!Storage::disk('s3')->exists('images')) {
            Storage::disk('s3')->makeDirectory('images');
        }

        // Verificar y eliminar la imagen anterior si existe
        /*if ($oldImagePath && Storage::disk('s3')->exists($oldImagePath)) {
            Storage::disk('s3')->delete($oldImagePath);
        }*/

        // Guardar la imagen en el disco (puedes usar public, s3, etc.)
        $path = $request->file('license_image_path')->storeAs('images', $imageName, 's3');

        // URL pública de la imagen (si está en storage/public)
        Storage::disk('s3')->url($path);

        return $path;
    }
}
