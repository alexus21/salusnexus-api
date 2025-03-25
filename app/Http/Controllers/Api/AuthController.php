<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
    public function register(Request $request) {
        // Reglas de validación para los campos del formulario
        $rules = [
            'first_name' => 'required|string',              // Nombre obligatorio y debe ser texto
            'last_name' => 'required|string',               // Apellido obligatorio y debe ser texto
            'date_of_birth' => 'required|date|before:today', // Fecha de nacimiento obligatoria, válida y anterior a hoy
            'age' => 'required|integer|min:18',             // Edad obligatoria, entero y mayor o igual a 18
            'phone' => 'required|string',                   // Teléfono obligatorio y debe ser texto
            'email' => 'required|email|unique:users,email', // Correo obligatorio, válido y único en la tabla users
            'address' => 'required|string',                 // Dirección obligatoria y debe ser texto
            'password' => 'required|string',                // Contraseña obligatoria y debe ser texto
            'confirm_password' => 'required|string|same:password', // Confirmación obligatoria y debe coincidir con la contraseña
        ];

        // Mensajes personalizados para errores de validación
        $messages = [
            'first_name.required' => 'El nombre es requerido',
            'first_name.string' => 'El nombre debe ser texto',
            'last_name.required' => 'El apellido es requerido',
            'last_name.string' => 'El apellido debe ser texto',
            'date_of_birth.required' => 'La fecha de nacimiento es requerida',
            'date_of_birth.date' => 'La fecha de nacimiento debe ser una fecha válida',
            'date_of_birth.before' => 'La fecha de nacimiento departamento ser anterior a la fecha actual',
            'age.required' => 'La edad es requerida',
            'age.integer' => 'La edad debe ser un número entero',
            'age.min' => 'La edad debe ser mayor de 18 años',
            'phone.required' => 'El teléfono es requerido',
            'phone.string' => 'El teléfono debe ser texto',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'El correo electrónico ya está en uso',
            'address.required' => 'La dirección es requerida',
            'address.string' => 'La dirección debe ser texto',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser texto',
            'confirm_password.required' => 'La confirmación de contraseña es requerida',
            'confirm_password.string' => 'La confirmación de contraseña debe ser texto',
            'confirm_password.same' => 'Las contraseñas no coinciden'
        ];

        // Validación de los datos recibidos según las reglas y mensajes definidos
        $validator = Validator::make($request->all(), $rules, $messages);

        // Si la validación falla, devolver error con detalles
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'status' => false,
                'errors' => $validator->errors() // Lista de errores específicos
            ], 400); // Código HTTP 400: Solicitud incorrecta
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
                'message' => 'El teléfono ingresado ya está en uso',
                'status' => false,
                'errors' => ['telefono' => ['El teléfono ingresado ya está en uso']]
            ], 400); // Código HTTP 400: Solicitud incorrecta
        }

        // Crear un nuevo usuario en la base de datos con los datos validados
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'age' => $request->age,
            'phone' => $phone,
            'email' => $request->email,
            'address' => $request->address,
            'password' => Hash::make($request->password), // Encriptar la contraseña con Hash
        ]);

        // Generar un token de acceso personal para el usuario
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        // Establecer fecha de expiración del token (1 semana desde ahora)
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save(); // Guardar el token en la base de datos

        // Devolver respuesta de éxito con los datos del usuario y el token
        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'status' => true,
            'data' => [
                'user' => $user, // Información del usuario creado
                'access_token' => $tokenResult->accessToken, // Token de acceso
                'token_type' => 'Bearer', // Tipo de token
                'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString() // Fecha de expiración
            ]
        ], 201); // Código HTTP 201: Creado
    }
}
