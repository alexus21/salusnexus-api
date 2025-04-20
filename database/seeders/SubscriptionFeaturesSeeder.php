<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            // Paciente Gratis
            ['feature' => 'Creación de perfil básico', 'subscription_type' => 'paciente_gratis'],
            ['feature' => 'Acceso completo al directorio de profesionales', 'subscription_type' => 'paciente_gratis'],
            ['feature' => 'Publicar reseñas y calificar establecimientos', 'subscription_type' => 'paciente_gratis'],
            ['feature' => 'Agendar citas', 'subscription_type' => 'paciente_gratis'],
            ['feature' => 'Solicitar servicios a domicilio', 'subscription_type' => 'paciente_gratis'],

            // Paciente Avanzado
            ['feature' => 'Todas las ventajas del plan gratuito', 'subscription_type' => 'paciente_avanzado'],
            ['feature' => 'Acceso a reseñas públicas de otros pacientes', 'subscription_type' => 'paciente_avanzado'],
            ['feature' => 'Historial de citas', 'subscription_type' => 'paciente_avanzado'],
            ['feature' => 'Soporte prioritario para la gestión de citas', 'subscription_type' => 'paciente_avanzado'],
            ['feature' => 'Consejos de salud personalizados según perfil', 'subscription_type' => 'paciente_avanzado'],
            ['feature' => 'Detalles de medicamentos recetados', 'subscription_type' => 'paciente_avanzado'],
            ['feature' => 'Notificaciones de citas futuras', 'subscription_type' => 'paciente_avanzado'],
            ['feature' => 'Recordatorios personalizados', 'subscription_type' => 'paciente_avanzado'],

            // Profesional Gratis
            ['feature' => 'Creación y personalización de perfil profesional', 'subscription_type' => 'profesional_gratis'],
            ['feature' => 'Publicación y visibilidad de tu negocio', 'subscription_type' => 'profesional_gratis'],
            ['feature' => 'Recepción de calificaciones de pacientes', 'subscription_type' => 'profesional_gratis'],
            ['feature' => 'Monitoreo de visualizaciones de pacientes', 'subscription_type' => 'profesional_gratis'],
            ['feature' => 'Notificaciones de nuevas reseñas', 'subscription_type' => 'profesional_gratis'],
            ['feature' => 'Búsqueda en el directorio de pacientes', 'subscription_type' => 'profesional_gratis'],

            // Profesional Avanzado
            ['feature' => 'Todas las ventajas del plan gratuito', 'subscription_type' => 'profesional_avanzado'],
            ['feature' => 'Habilitación para agendar citas', 'subscription_type' => 'profesional_avanzado'],
            ['feature' => 'Historial de citas', 'subscription_type' => 'profesional_avanzado'],
            ['feature' => 'Soporte técnico prioritario', 'subscription_type' => 'profesional_avanzado'],
            ['feature' => 'Habilitación de servicio a domicilio', 'subscription_type' => 'profesional_avanzado'],
            ['feature' => 'Exportación de historiales de pacientes', 'subscription_type' => 'profesional_avanzado'],
            ['feature' => 'Recordatorios personalizados', 'subscription_type' => 'profesional_avanzado'],
            ['feature' => 'Chat integrado para comunicación con pacientes', 'subscription_type' => 'profesional_avanzado'],
        ];

        DB::table('subscription_features')->insert($features);
    }
}
