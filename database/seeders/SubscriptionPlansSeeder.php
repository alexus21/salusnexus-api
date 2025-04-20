<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Paciente Gratis',
                'subscription_type' => 'paciente_gratis',
                'price_monthly' => null,
                'price_annual' => null,
                'currency' => 'USD',
                'description' => 'Plan básico gratuito para pacientes. Incluye acceso al directorio, agendar citas, reseñas y servicios a domicilio.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paciente Avanzado',
                'subscription_type' => 'paciente_avanzado',
                'price_monthly' => 5.99,
                'price_annual' => 4.99 * 12, // $59.88
                'currency' => 'USD',
                'description' => 'Plan premium para pacientes. Incluye todas las ventajas del plan gratuito más historial de citas, soporte prioritario, consejos personalizados, notificaciones y más.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Profesional Gratis',
                'subscription_type' => 'profesional_gratis',
                'price_monthly' => null,
                'price_annual' => null,
                'currency' => 'USD',
                'description' => 'Plan básico gratuito para profesionales. Incluye creación de perfil, visibilidad, calificaciones, monitoreo y búsqueda de pacientes.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Profesional Avanzado',
                'subscription_type' => 'profesional_avanzado',
                'price_monthly' => 7.99,
                'price_annual' => 6.40 * 12, // $76.80
                'currency' => 'USD',
                'description' => 'Plan premium para profesionales. Incluye todas las ventajas del plan gratuito más agendamiento, historial, soporte prioritario, exportación, recordatorios y chat.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('subscription_plans')->insert($plans);
    }
}
