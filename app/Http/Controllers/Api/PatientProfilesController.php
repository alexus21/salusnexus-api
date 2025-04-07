<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientProfilesRequest;
use App\Http\Requests\UpdatePatientProfilesRequest;
use App\Models\PatientProfiles;
use App\Models\User;
use App\Rules\EmailRule;
use App\Rules\PhoneNumberRule;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
    public function create(Request $request): JsonResponse {
        $rules = [
            'first_name' => 'required|string',              // Nombre obligatorio y debe ser texto
            'last_name' => 'required|string',               // Apellido obligatorio y debe ser texto
            'phone' => ['required', new PhoneNumberRule()],                   // Teléfono obligatorio y debe ser texto
            'email' => ['required', 'email', 'unique:users,email', new EmailRule()], // Correo obligatorio, válido y único en la tabla users
            'password' => 'required|string',                // Contraseña obligatoria y debe ser texto
            'confirm_password' => 'required|string|same:password', // Confirmación obligatoria y debe coincidir con la contraseña
            'user_rol' => 'required|in:paciente,profesional',                 // Tipo de usuario obligatorio y debe ser texto
            'profile_photo_path' => 'nullable|image',              // Foto de perfil opcional y debe ser una imagen
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
        ];

        $messages = [
            'first_name.required' => 'El nombre es requerido',
            'first_name.string' => 'El nombre debe ser texto',
            'last_name.required' => 'El apellido es requerido',
            'last_name.string' => 'El apellido debe ser texto',
            'phone.required' => 'El teléfono es requerido',
            'phone.regex' => 'El teléfono no tiene un formato de número de teléfono válido',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'El correo electrónico ya está en uso',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser texto',
            'confirm_password.required' => 'La confirmación de contraseña es requerida',
            'confirm_password.string' => 'La confirmación de contraseña debe ser texto',
            'confirm_password.same' => 'Las contraseñas no coinciden',
            'date_of_birth.required' => 'La fecha de nacimiento es requerida.',
            'date_of_birth.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'gender.required' => 'El género es requerido.',
            'gender.string' => 'El género debe ser una cadena de texto.',
        ];

        // Validación de los datos recibidos según las reglas y mensajes definidos
        $validator = Validator::make($request->all(), $rules, $messages);

        // Si la validación falla, devolver error con detalles
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error: ' . $validator->errors(),
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

            $patient = PatientProfiles::create([
                'date_of_birth' => $request->date_of_birth,
                'gender' => strtolower($request->gender),
                'home_address' => '-',
                'home_latitude' => 13.697497222222,
                'home_longitude' => -89.190313888889,
                'home_address_reference' => '-',
                'emergency_contact_name' => '-',
                'emergency_contact_phone' => '-',
                'user_id' => $user_id,
            ]);

            // Generar token
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            $data = [
                'user' => $user,
                'patient_profile' => $patient,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            ];

            return response()->json([
                'status' => true,
                'message' => 'Usuario registrado correctamente',
                'data' => $data,
            ], 201); // Código HTTP 201: Creado

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
}
