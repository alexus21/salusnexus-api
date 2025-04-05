<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfessionalProfilesRequest;
use App\Http\Requests\UpdateProfessionalProfilesRequest;
use App\Models\ProfessionalProfiles;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
    public function create(Request $request): JsonResponse {
        $rules = [
            'first_name' => 'required|string',              // Nombre obligatorio y debe ser texto
            'last_name' => 'required|string',               // Apellido obligatorio y debe ser texto
            'phone' => 'required',                   // Teléfono obligatorio y debe ser texto
            'email' => 'required|email|unique:users,email', // Correo obligatorio, válido y único en la tabla users
            'password' => 'required|string',                // Contraseña obligatoria y debe ser texto
            'confirm_password' => 'required|string|same:password', // Confirmación obligatoria y debe coincidir con la contraseña
            'user_rol' => 'required|in:paciente,profesional',                 // Tipo de usuario obligatorio y debe ser texto
            'profile_photo_path' => 'nullable|image',              // Foto de perfil opcional y debe ser una imagen
            // Requerido para los profesionales:
            'license_number' => 'required|string',
            'biography' => 'required|string',
            'clinic_name' => 'required|string',
            'clinic_address_1' => 'required|string',
            'clinic_address_2' => 'nullable|string',
            'clinic_city_id' => 'required|string',
            'clinic_latitude' => 'required|string',
            'clinic_longitude' => 'required|string',
            'home_visits' => 'required|boolean',
            'years_of_experience' => 'required|integer',
            'website_url' => 'nullable|string',
        ];

        $messages = [
            'first_name.required' => 'El nombre es requerido',
            'first_name.string' => 'El nombre debe ser texto',
            'last_name.required' => 'El apellido es requerido',
            'last_name.string' => 'El apellido debe ser texto',
            'phone.required' => 'El teléfono es requerido',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'El correo electrónico ya está en uso',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser texto',
            'confirm_password.required' => 'La confirmación de contraseña es requerida',
            'confirm_password.string' => 'La confirmación de contraseña debe ser texto',
            'confirm_password.same' => 'Las contraseñas no coinciden',
            // Mensajes personalizados para errores de validación
            'license_number.required' => 'El número de licencia es requerido.',
            'license_number.string' => 'El número de licencia debe ser una cadena de texto.',
            'biography.required' => 'La biografía es requerida.',
            'biography.string' => 'La biografía debe ser una cadena de texto.',
            'clinic_name.required' => 'El nombre de la clínica es requerido.',
            'clinic_name.string' => 'El nombre de la clínica debe ser una cadena de texto.',
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
        ];

        // Validación de los datos recibidos según las reglas y mensajes definidos
        $validator = Validator::make($request->all(), $rules, $messages);

        // Si la validación falla, devolver error con detalles
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'status' => false,
                'errors' => $validator->errors() // Lista de errores específicos
            ], 400);
            // Código HTTP 400: Solicitud incorrecta
        }

        // Asegurarse de que el teléfono comience con el código de país "+503" (El Salvador)
        $phone = str_starts_with($request->phone, "+503") ? $request->phone : "+503 " . $request->phone;
        // Formatear el teléfono al estilo "+503 XXXX-XXXX"
        $phone = preg_replace('/(\+503)\s?(\d{4})(\d{4})/', '$1 $2-$3', $phone);

        // Verificar si el teléfono ya está registrado en la base de datos
        $user = DB::table('users')
            ->select('phone')
            ->where('phone', $phone)
            ->first();

        // Si el teléfono ya existe, devolver error
        if ($user) {
            return response()->json([
                'message' => 'El teléfono personal ingresado ya está en uso',
                'status' => false,
                'errors' => ['telefono' => ['El teléfono personal ingresado ya está en uso']]
            ], 400); // Código HTTP 400: Solicitud incorrecta
        }

        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_rol' => strtolower($request->user_rol),
                'profile_photo_path' => $request->profile_photo_path
            ]);

            $user_id = $user->id;

            $professional = ProfessionalProfiles::create([
                'license_number' => $request->license_number,
                'biography' => $request->biography,
                'clinic_name' => $request->clinic_name,
                'clinic_address_1' => $request->clinic_address_1,
                'clinic_latitude' => $request->clinic_latitude,
                'clinic_longitude' => $request->clinic_longitude,
                'clinic_address_reference' => $request->clinic_address_2,
                'home_visits' => $request->home_visits,
                'years_of_experience' => $request->years_of_experience,
                'website_url' => $request->website_url,
                'user_id' => $user_id,
            ]);

            // Generar token
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            $data = [
                'user' => $user,
                'professional_profile' => $professional,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            ];

            return response()->json([
                'status' => true,
                'message' => 'Perfil de profesional creado correctamente',
                'data' => $data
            ], 201); // Código HTTP 201: Recurso creado
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al registrar el usuario: ' . $e->getMessage(),
                'errors' => ['error' => ['Error al registrar el usuario']]
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
