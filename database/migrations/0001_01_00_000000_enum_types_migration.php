<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        DB::statement("DROP TYPE IF EXISTS user_rol CASCADE");
        DB::statement("DROP TYPE IF EXISTS type_subscription_plan CASCADE");
        DB::statement("DROP TYPE IF EXISTS subscription_period CASCADE");
        DB::statement("DROP TYPE IF EXISTS suscription_status CASCADE");
        DB::statement("DROP TYPE IF EXISTS appointment_status CASCADE");
        DB::statement("DROP TYPE IF EXISTS service_type CASCADE");
        DB::statement("DROP TYPE IF EXISTS gender CASCADE");
        DB::statement("DROP TYPE IF EXISTS payment_provider CASCADE");
        DB::statement("DROP TYPE IF EXISTS speciality_type CASCADE");
        DB::statement('DROP TYPE IF EXISTS licensing_authority CASCADE');

        DB::statement("CREATE TYPE user_rol AS ENUM ('administrador', 'paciente', 'profesional')");
        DB::statement("CREATE TYPE type_subscription_plan AS
            ENUM ('paciente_gratis', 'paciente_avanzado', 'profesional_gratis', 'profesional_avanzado')");
        DB::statement("CREATE TYPE subscription_period AS
            ENUM('mensual', 'anual')");
        DB::statement("CREATE TYPE suscription_status AS
            ENUM ('activa', 'cancelada', 'expirada', 'prueba', 'pago_pendiente')");
        DB::statement("CREATE TYPE appointment_status AS
            ENUM ('programada', 'completada', 'cancelada_paciente', 'cancelada_profesional', 'no_asistio', 'pendiente_confirmacion')");
        DB::statement("CREATE TYPE service_type AS
            ENUM ('consultorio', 'domicilio')");
        DB::statement("CREATE TYPE gender AS
            ENUM ('masculino', 'femenino')");
        DB::statement("CREATE TYPE payment_provider AS
            ENUM ('visa', 'mastercard', 'maestro', 'paypal', 'diners', 'amex')");
        DB::statement("CREATE TYPE speciality_type AS
            ENUM ('primaria', 'secundaria')");
        DB::statement("CREATE TYPE licensing_authority AS
            ENUM ('Ministerio de Salud', 'Consejo Superior de Salud Pública', 'Departamento de Comercio EE. UU.', 'OMS/OPS')");
    }
};
