<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AuthControllerTest extends TestCase {

    /**
     * Prueba que un usuario puede registrarse.
     *
     * Este método prueba el registro de un usuario nuevo. Primero, se ejecutan
     * las migraciones frescas y se crea un cliente personal de Passport.
     * Luego, se envía una solicitud POST a la ruta de registro con los datos
     * del usuario. Finalmente, se verifican las respuestas JSON y el estado HTTP.
     */
    public function test_can_register_user(): void {
        Artisan::call('migrate:fresh');
        Artisan::call('passport:client --personal');

        $userData = [
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'gender' => 'required|string|in:M,F',
            'date_of_birth' => '1990-05-15',
            'age' => 33,
            'phone' => '+503 +12234567890',
            'email' => 'juan.perez@example.com',
            'address' => 'Calle Falsa 123, Ciudad',
            'password' => 'secret123',
            'confirm_password' => 'secret123'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Usuario registrado correctamente',
                'status' => true
            ])
            ->assertJsonStructure([
                'message',
                'status',
                'data' => [
                    'user' => [
                        'id',
                        'first_name',
                        'last_name',
                        'gender',
                        'date_of_birth',
                        'age',
                        'phone',
                        'email',
                        'address',
                        'created_at',
                        'updated_at'
                    ],
                    'access_token',
                    'token_type',
                    'expires_at'
                ]
            ]);
    }

    /**
     * Prueba que un usuario puede iniciar sesión.
     *
     * Este método prueba el inicio de sesión de un usuario existente. Se envía
     * una solicitud POST a la ruta de inicio de sesión con los datos del usuario.
     * Finalmente, se verifican las respuestas JSON y el estado HTTP.
     */
    public function test_can_login_user(): void {
        $loginData = [
            'email' => 'juan.perez@example.com',
            'password' => 'secret123'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'status',
                'data' => [
                    'user',
                    'access_token',
                    'token_type',
                    'expires_at'
                ]
            ]);
    }

    /**
     * Prueba que un usuario puede cerrar sesión.
     *
     * Este método prueba el cierre de sesión de un usuario autenticado. Se envía
     * una solicitud POST a la ruta de cierre de sesión con el token de acceso.
     * Finalmente, se verifican las respuestas JSON y el estado HTTP.
     */
    public function test_can_logout_user(): void {
        $user = User::factory()->create([
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'sex' => 'M',
                'date_of_birth' => '1990-05-15',
                'age' => 33,
                'phone' => '+503 +12234567890',
                'email' => 'jperez@mail.com',
                'address' => 'Calle Falsa 123, Ciudad',
                'password' => bcrypt('secret123'),
            ]
        );
        $token = $user->createToken('TestToken')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Sesión cerrada correctamente',
                'status' => true
            ]);
    }

    /**
     * Prueba que la validación falla al registrar un usuario.
     *
     * Este método prueba que la validación falla al registrar un usuario con
     * datos incorrectos. Se envía una solicitud POST a la ruta de registro con
     * datos incorrectos. Finalmente, se verifican las respuestas JSON y el
     * estado HTTP.
     */
    public function test_register_validation_fails(): void {
        $userData = [
            'first_name' => 'Juan',
            'last_name' => '',
            'date_of_birth' => '2027-05-15',
            'age' => 33,
            'phone' => '+503 +12234567890',
            'email' => 'juan.perez@example.com',
            'address' => 'Calle Falsa 123, Ciudad',
            'password' => 'secret123',
            'confirm_password' => 'secret1234'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(400)
            ->assertJsonStructure([
                'message',
                'status',
                'errors'
            ]);
    }

    /**
     * Prueba que la validación falla al iniciar sesión.
     *
     * Este método prueba que la validación falla al iniciar sesión con datos
     * incorrectos. Se envía una solicitud POST a la ruta de inicio de sesión
     * con datos incorrectos. Finalmente, se verifican las respuestas JSON y
     * el estado HTTP.
     */
    public function test_login_with_invalid_credentials(): void {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'No autorizado',
                'status' => false
            ]);
    }
}
