<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {
    public function register(Request $request) {
        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date|before:today',
            'age' => 'required|integer|min:18',
            'phone' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'address' => 'required|string',
            'password' => 'required|string',
            'confirm_password' => 'required|string|same:password',
        ];

        $messages = [
            'first_name.required' => 'El nombre es requerido',
            'first_name.string' => 'El nombre debe ser texto',
            'last_name.required' => 'El apellido es requerido',
            'last_name.string' => 'El apellido debe ser texto',
            'date_of_birth.required' => 'La fecha de nacimiento es requerida',
            'date_of_birth.date' => 'La fecha de nacimiento debe ser una fecha válida',
            'date_of_birth.before' => 'La fecha de nacimiento debe ser anterior a la fecha actual',
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
            'password.confirmed' => 'Las contraseñas no coinciden',
            'confirm_password.required' => 'La confirmación de contraseña es requerida',
            'confirm_password.string' => 'La confirmación de contraseña debe ser texto',
            'confirm_password.same' => 'Las contraseñas no coinciden'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $phone = str_starts_with($request->phone, "+503") ? $request->phone : "+503 " . $request->phone;
        $phone = preg_replace('/(\+503)\s?(\d{4})(\d{4})/', '$1 $2-$3', $phone);

        $user = DB::table('users')
            ->select('phone')
            ->where('phone', $phone)
            ->first();

        if ($user) {
            return response()->json([
                'message' => 'El teléfono ingresado ya está en uso',
                'status' => false,
                'errors' => ['telefono' => ['El teléfono ingresado ya está en uso']]
            ], 400);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'age' => $request->age,
            'phone' => $phone,
            'email' => $request->email,
            'address' => $request->address,
            'password' => Hash::make($request->password),
        ]);

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'status' => true,
            'data' => [
                'user' => $user,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
            ]
        ], 201);
    }
}
