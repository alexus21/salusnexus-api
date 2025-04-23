<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfessionalProfilesRequest;
use App\Http\Requests\UpdateProfessionalProfilesRequest;
use App\Models\MedicalClinic;
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

            /*if ($request->has('dui')) {
                $user->dui = $request->dui;
            }

            if ($request->has('date_of_birth')) {
                $user->date_of_birth = $request->date_of_birth;
            }*/

            if ($request->has('home_address')) {
                $user->address = $request->home_address;
            }

            $user->save();

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
    public function destroy(ProfessionalProfiles $professionalProfiles) {
        //
    }

    public function verifyProfessionalAccount(Request $request): JsonResponse {
        log::info($request);

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
            'clinic_name' => 'required|string|max:200',
            'description' => 'string|max:512',
            'clinic_address' => 'required|string',
            'clinic_address_reference' => 'nullable|string',
            'city_id' => 'required',
            'clinic_latitude' => 'required',
            'clinic_longitude' => 'required',
            'facade_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'waiting_room_photo' => 'image|mimes:jpeg,png,jpg|max:2048',
            'office_photo' => 'image|mimes:jpeg,png,jpg|max:2048',
            'speciality_type' => 'required|in:primaria,secundaria',
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

            'facade_photo.required' => 'La foto de la fachada es requerida.',
            'facade_photo.image' => 'La foto de la fachada debe ser una imagen válida.',
            'facade_photo.mimes' => 'La foto de la fachada debe ser un archivo de tipo jpeg, png o jpg.',
            'facade_photo.max' => 'La foto de la fachada no debe exceder los 2MB.',
            'speciality_type.required' => 'El tipo de especialidad es requerido.',
            'speciality_type.in' => 'El tipo de especialidad debe ser primaria o secundaria.',
            'clinic_name.required' => 'El nombre de la clínica es requerido.',
            'clinic_name.string' => 'El nombre de la clínica debe ser una cadena de texto.',
            'clinic_name.max' => 'El nombre de la clínica no debe exceder los 200 caracteres.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'description.max' => 'La descripción no debe exceder los 512 caracteres.',
        ];

        if ($request->has('waiting_room_photo')) {
            $rules['waiting_room_photo'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
            $messages['waiting_room_photo.image'] = 'La foto de la sala de espera debe ser una imagen.';
            $messages['waiting_room_photo.mimes'] = 'La foto de la sala de espera debe ser un archivo de tipo: jpeg, png, jpg, gif.';
        }

        if ($request->has('office_photo')) {
            $rules['office_photo'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
            $messages['office_photo.image'] = 'La foto de la oficina debe ser una imagen.';
            $messages['office_photo.mimes'] = 'La foto de la oficina debe ser un archivo de tipo: jpeg, png, jpg, gif.';
        }

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

            // Primero verificamos si existe el perfil profesional
            $professional_id = DB::table('professional_profiles')
                ->where('user_id', Auth::user()->id)
                ->value('id');

            if (!$professional_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontró el perfil profesional'
                ], 404);
            }

            // Guardar la información del usuario en la base de datos
            DB::table('professional_profiles')->where('user_id', Auth::user()->id)->update([
                'biography' => $request->biography,
                'home_visits' => true,
                'years_of_experience' => $request->years_of_experience,
                'website_url' => $request->website_url,
            ]);

            DB::table('professional_specialities')
                ->insert([
                    'professional_id' => $professional_id,
                    'speciality_id' => $request->speciality_id
                ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error actualizando perfil profesional: ' . $e->getMessage()
            ], 500);
        }

        try {
            $medicalClinic = MedicalClinic::create([
                'clinic_name' => $request->clinic_name,
                'address' => $request->clinic_address,
                'clinic_address_reference' => $request->clinic_address_reference,
                'clinic_latitude' => $request->clinic_latitude,
                'clinic_longitude' => $request->clinic_longitude,
                'description' => $request->description,
                'city_id' => $request->city_id,
                'professional_id' => $professional_id,
                'speciality_type' => strtolower($request->speciality_type),
                'facade_photo' => $this->saveFacadePhoto($request),
                'waiting_room_photo' => '-',
                'office_photo' => '-',
            ]);

            if ($request->hasFile('waiting_room_photo')) {
                $waitingRoomImageName = Str::uuid() . '.' . $request->waiting_room_photo->extension();
                $waitingRoomPath = $request->file('waiting_room_photo')->storeAs('images/clinics_pics', $waitingRoomImageName, 's3');
                // Almacenar el URL generado correctamente
                $waitingRoomUrl = Storage::disk('s3')->url($waitingRoomPath);
                $medicalClinic->waiting_room_photo = $waitingRoomPath;
                $medicalClinic->save(); // Solo llamar a save() si se modifican propiedades después de create()
            }

            if ($request->hasFile('office_photo')) {
                $officeImageName = Str::uuid() . '.' . $request->office_photo->extension();
                $officePath = $request->file('office_photo')->storeAs('images/clinics_pics', $officeImageName, 's3');
                // Almacenar el URL generado correctamente
                $officeUrl = Storage::disk('s3')->url($officePath);
                $medicalClinic->office_photo = $officePath;
                $medicalClinic->save(); // Solo llamar a save() si se modifican propiedades después de create()
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creando clínica médica: ' . $e->getMessage()
            ], 500);
        }

        try {
            $medicalLicense = MedicalLicenses::create([
                'professional_profile_id' => $professional_id,
                'license_number' => $request->license_number,
                'licensing_authority' => $request->license_authority,
                'issue_date' => $request->issue_date,
                'expiration_date' => $request->expiration_date,
                'license_image_path' => $license_path,
            ]);
            // No es necesario llamar a save() después de create()
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creando licencia médica: ' . $e->getMessage()
            ], 500);
        }

        try {
            // Check if the user already has a subscription
            $existingSubscription = Subscriptions::where('user_id', Auth::user()->id)
                ->where('subscription_status', 'activa')
                ->first();

            if (!$existingSubscription) {
                // Verificar si el parámetro subscription_period existe
                $subscriptionPeriod = $request->has('subscription_period') ? $request->subscription_period : 'mensual';
                // Crear suscripción gratuita
                (new SubscriptionsController())->store(Auth::user()->id, 'profesional', $subscriptionPeriod);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creando suscripción: ' . $e->getMessage()
            ], 500);
        }

        try {
            // Verificar que el usuario existe
            $user = User::find(Auth::user()->id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

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

            $user = ((new User())->getUserProfile(Auth::user()->id));

            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Perfil verificado correctamente',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error actualizando usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveProfilePhoto(Request $request): JsonResponse|string {
        if (!$request->hasFile('profile_photo_path')) {
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
            Storage::disk('s3')->makeDirectory('images/profile_pics');
        }

        // Verificar y eliminar la imagen anterior si existe
        if ($oldImagePath && Storage::disk('s3')->exists($oldImagePath)) {
            Storage::disk('s3')->delete($oldImagePath);
        }

        // Guardar la imagen en el disco (puedes usar public, s3, etc.)
        $path = $request->file('profile_photo_path')->storeAs('images/profile_pics', $imageName, 's3');

        // URL pública de la imagen (si está en storage/public)
        Storage::disk('s3')->url($path);

        return $path;
    }

    private function saveLicensePhoto(Request $request): JsonResponse|string {
        if (!$request->hasFile('license_image_path')) {
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
            Storage::disk('s3')->makeDirectory('images/medical_licenses_pics');
        }

        // Verificar y eliminar la imagen anterior si existe
        /*if ($oldImagePath && Storage::disk('s3')->exists($oldImagePath)) {
            Storage::disk('s3')->delete($oldImagePath);
        }*/

        // Guardar la imagen en el disco (puedes usar public, s3, etc.)
        $path = $request->file('license_image_path')->storeAs('images/medical_licenses_pics', $imageName, 's3');

        // URL pública de la imagen (si está en storage/public)
        Storage::disk('s3')->url($path);

        return $path;
    }

    private function saveFacadePhoto(Request $request) {
        if (!$request->hasFile('facade_photo')) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró ninguna imagen'
            ], 400);
        }

        // Generar nombre único para la imagen
        $imageName = Str::uuid() . '.' . $request->facade_photo->extension();

        // Obtener ruta anterior de la imagen
        $oldImagePath = DB::table('medical_clinics')
            ->where('id', Auth::id())
            ->value('facade_photo');

        if (!Storage::disk('s3')->exists('images')) {
            Storage::disk('s3')->makeDirectory('images/clinics_pics');
        }

        // Verificar y eliminar la imagen anterior si existe
        if ($oldImagePath && Storage::disk('s3')->exists($oldImagePath)) {
            Storage::disk('s3')->delete($oldImagePath);
        }

        // Guardar la imagen en el disco (puedes usar public, s3, etc.)
        $path = $request->file('facade_photo')->storeAs('images/clinics_pics', $imageName, 's3');

        // URL pública de la imagen (si está en storage/public)
        Storage::disk('s3')->url($path);

        return $path;
    }
}
