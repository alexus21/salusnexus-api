<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
        if ($request->has('user_rol') && $request->user_rol == 'paciente') {
            return (new PatientProfilesController())->create($request);
        }

        if ($request->has('user_rol') && $request->user_rol == 'profesional') {
            return (new ProfessionalProfilesController())->create($request);
        }

        return response()->json([
            'message' => 'Tipo de usuario no válido',
            'status' => false
        ], 400);
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
