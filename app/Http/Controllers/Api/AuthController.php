<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use App\Models\PatientProfiles;
use App\Models\ProfessionalProfiles;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {
    public function register(Request $request): JsonResponse {
        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'phone' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'confirm_password' => 'required|string|same:password',
            'user_rol' => 'required|in:paciente,profesional',
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
            'date_of_birth.required' => 'La fecha de nacimiento es requerida.',
            'date_of_birth.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'gender.required' => 'El género es requerido.',
            'gender.string' => 'El género debe ser una cadena de texto.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
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
                'date_of_birth' => $request->date_of_birth,
                'gender' => strtolower($request->gender),
                'phone' => $phone,
                'address' => '-',
                'latitude' => 13.697497222222,
                'longitude' => -89.190313888889,
                'address_reference' => '-',
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_rol' => strtolower($request->user_rol),
            ]);

            $user_id = $user->id;

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            if ($request->has('user_rol') && $request->user_rol == 'paciente') {
                $patient = PatientProfiles::create([
                    'emergency_contact_name' => '-',
                    'emergency_contact_phone' => '-',
                    'user_id' => $user_id,
                ]);

                $data = [
                    'user' => $user,
                    'patient_profile' => $patient,
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                ];

                $this->sendWelcomeMail($request);
                return response()->json([
                    'status' => true,
                    'message' => 'Registrado correctamente',
                    'data' => $data
                ], 201);
            }

            if ($request->has('user_rol') && $request->user_rol == 'profesional') {
                $professional = ProfessionalProfiles::create([
                    'license_number' => '-',
                    'biography' => '-',
                    'home_visits' => false,
                    'years_of_experience' => 1,
                    'website_url' => '-',
                    'user_id' => $user_id,
                ]);

                $data = [
                    'user' => $user,
                    'professional_profile' => $professional,
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                ];

                $this->sendWelcomeMail($request);
                return response()->json([
                    'status' => true,
                    'message' => 'Registrado correctamente',
                    'data' => $data
                ], 201);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Rol de usuario no válido',
                    'errors' => ['user_rol' => ['El rol de usuario no es válido']]
                ], 400);
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al registrar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
            'user_rol' => 'required|in:paciente,profesional',
        ];

        $messages = [
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser válido',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser texto',
            'user_rol.required' => 'El rol de usuario es requerido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $credentials = request(['email', 'password', 'user_rol']);

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'No autorizado',
                'status' => false,
                'errors' => ['email' => 'Correo electrónico o contraseña incorrectos']
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        $user = (new User)->getUserProfile($user->id);

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'status' => true,
            'user' => $user,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
        ]);
    }

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

    public function profile(): JsonResponse {
        $user = (new User)->getUserProfile(Auth::user()->id);

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

    public function isUserVerified(): JsonResponse {
        $user = Auth::user()->verified;

        if ($user) {
            return response()->json([
                'message' => 'Ya has verificado tu cuenta',
                'status' => true,
            ]);
        }

        return response()->json([
            'message' => 'No has verificado tu cuenta',
            'status' => false
        ], 401);
    }

    private function sendWelcomeMail(Request $request): void {
        $details = [
            'subject' => 'Bienvenido a SalusNexus, ' . $request->first_name . '.',
            'name' => 'SalusNexus',
            'email' => $request->email,
            'message' => 'Es un gusto tenerte con nosotros.
            Estamos aquí para ayudarte a cuidar de tu salud y bienestar.
            Si tienes alguna pregunta o necesitas asistencia, no dudes en contactarnos.',
        ];

        Mail::to($request->email)->send(new ContactFormMail($details));
    }
}
