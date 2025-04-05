<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {
    /**
     * Registra un nuevo usuario en el sistema.
     *
     * Este método valida los datos enviados en la solicitud, formatea el número de teléfono,
     * verifica si ya existe, crea el usuario con una contraseña encriptada y genera un token
     * de acceso personal que expira en una semana.
     *
     * @param Request $request Objeto con los datos enviados en la solicitud HTTP.
     * @return JsonResponse Respuesta JSON con el resultado del registro.
     */
    public function register(Request $request): JsonResponse {
        try {
            if ($validation = $this->validateUser($request)) {
                return $validation;
            }

            if ($request->user_rol == 'paciente') {
                if ($validation = $this->validatePatients($request)) {
                    return $validation;
                }
            } else if ($request->user_rol == 'profesional') {
                if ($validation = $this->validateProfesionals($request)) {
                    return $validation;
                }
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

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_rol' => strtolower($request->user_rol),
                'profile_photo_path' => $request->profile_photo_path
            ]);

            // Almacenar la respuesta del controlador de perfiles
            if ($request->user_rol == 'paciente') {
                $profileResponse = (new PatientProfilesController())->create($request, $user->id);
            } else if ($request->user_rol == 'profesional') {
                $profileResponse = (new ProfessionalProfilesController())->create($request, $user->id);
            } else {
                throw new Exception("Tipo de usuario no válido");
            }

            // Obtener el contenido JSON de la respuesta
            $profileData = json_decode($profileResponse->getContent(), true);
            log::info($profileData);

            // Si hubo error en la creación del perfil, lanzar excepción
            if (!$profileData['status']) {
                $patient_id = $profileData['data']['id'];
                User::destroy($patient_id);
                throw new Exception($profileData['error']);
            }

            // Generar token
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'status' => true,
                'data' => [
                    'user' => $user,
                    'profile' => $profileData['data'], // Incluir datos del perfil
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
                ]
            ], 201);

        } catch (QueryException|Exception $e) {
            return response()->json([
                'message' => 'Error al registrar el usuario',
                'status' => false,
                'error' => $e->getMessage(),
            ], 500); // Código HTTP 500: Error interno del servidor
        }
    }

    private function validateUser(Request $request): ?JsonResponse {
        // Reglas de validación para los campos del formulario
        $rules = [
            'first_name' => 'required|string',              // Nombre obligatorio y debe ser texto
            'last_name' => 'required|string',               // Apellido obligatorio y debe ser texto
            'phone' => 'required',                   // Teléfono obligatorio y debe ser texto
            'email' => 'required|email|unique:users,email', // Correo obligatorio, válido y único en la tabla users
            'password' => 'required|string',                // Contraseña obligatoria y debe ser texto
            'confirm_password' => 'required|string|same:password', // Confirmación obligatoria y debe coincidir con la contraseña
            'user_rol' => 'required|in:paciente,profesional',                 // Tipo de usuario obligatorio y debe ser texto
            'profile_photo_path' => 'nullable|image',              // Foto de perfil opcional y debe ser una imagen
        ];

        // Mensajes personalizados para errores de validación
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

        return null;
    }

    private function validatePatients(Request $request): ?JsonResponse {
        $rules = [
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'emergency_contact_name' => 'required|string',
            'emergency_contact_phone' => 'required',
        ];

        $messages = [
            'date_of_birth.required' => 'La fecha de nacimiento es requerida.',
            'date_of_birth.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'gender.required' => 'El género es requerido.',
            'gender.string' => 'El género debe ser una cadena de texto.',
            'emergency_contact_name.required' => 'El nombre del contacto de emergencia es requerido.',
            'emergency_contact_name.string' => 'El nombre del contacto de emergencia debe ser una cadena de texto.',
            'emergency_contact_phone.required' => 'El teléfono del contacto de emergencia es requerido.',
            'emergency_contact_phone.string' => 'El teléfono del contacto de emergencia debe ser una cadena de texto.',
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

        return null;
    }

    private function validateProfesionals(Request $request): ?JsonResponse {
        $rules = [
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

        return null;

    }

    /**
     * Inicia sesión para un usuario existente en el sistema.
     *
     * Este método valida las credenciales (correo y contraseña), intenta autenticar al usuario,
     * y si es exitoso, genera un token de acceso personal que expira en una semana.
     *
     * @param Request $request Objeto con los datos enviados en la solicitud HTTP.
     * @return JsonResponse Respuesta JSON con el resultado del inicio de sesión.
     */
    public function login(Request $request): JsonResponse {
        // Reglas de validación para los campos del formulario
        $rules = [
            'email' => 'required|email',    // Correo obligatorio y debe ser un email válido
            'password' => 'required|string', // Contraseña obligatoria y debe ser texto
        ];

        // Mensajes personalizados para errores de validación
        $messages = [
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser válido',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser texto',
        ];

        // Crea un validador con los datos de la solicitud, las reglas y los mensajes
        $validator = Validator::make($request->all(), $rules, $messages);

        // Si la validación falla, retorna una respuesta JSON con los errores
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'status' => false,
                'errors' => $validator->errors() // Lista de errores específicos
            ], 400); // Código HTTP 400: Solicitud incorrecta
        }

        // Extrae las credenciales (email y contraseña) de la solicitud
        $credentials = request(['email', 'password']);

        // Intenta autenticar al usuario con las credenciales proporcionadas
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'No autorizado',
                'status' => false,
                'errors' => ['email' => 'Correo electrónico o contraseña incorrectos']
            ], 401); // Código HTTP 401: No autorizado
        }

        // Obtiene el usuario autenticado desde la solicitud
        $user = $request->user();
        // Genera un token de acceso personal para el usuario
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        // Establece la fecha de expiración del token a una semana desde ahora
        $token->expires_at = Carbon::now()->addWeeks(1);
        // Guarda el token con la fecha de expiración en la base de datos
        $token->save();

        // Retorna una respuesta JSON con los datos del usuario y el token generado
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'status' => true,
            'data' => [
                'user' => $user, // Información del usuario autenticado
                'access_token' => $tokenResult->accessToken, // Token de acceso
                'token_type' => 'Bearer', // Tipo de token
                'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString() // Fecha de expiración
            ]
        ]); // Código HTTP 200 implícito: Éxito
    }

    /**
     * Cierra la sesión del usuario actual revocando su token de acceso.
     *
     * Este método revoca el token de acceso del usuario autenticado,
     * efectivamente terminando su sesión en el sistema. Requiere que
     * el usuario esté autenticado a través del middleware 'auth:api'.
     *
     * @param Request $request Objeto con los datos de la solicitud HTTP
     * @return JsonResponse Respuesta JSON confirmando el cierre de sesión
     */
    public function logout(Request $request): JsonResponse {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
            'status' => true
        ]);
    }

    public function validateToken(Request $request): JsonResponse {
        $user = Auth::user();

        if ($user) {
            return response()->json([
                'message' => 'Token válido',
                'status' => true,
                'data' => $user
            ]);
        }

        return response()->json([
            'message' => 'Token inválido',
            'status' => false
        ], 401);
    }

    public function profile(Request $request): JsonResponse {
        $user = (new User)->getUserInfoByItsId(Auth::user()->id);

        if ($user) {
            return response()->json([
                'message' => 'Perfil obtenido correctamente',
                'status' => true,
                'data' => $user
            ]);
        }

        return response()->json([
            'message' => 'Token inválido',
            'status' => false
        ], 401);
    }
}
