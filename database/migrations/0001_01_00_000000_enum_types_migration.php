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
        DB::statement("DROP TYPE IF EXISTS suscription_status CASCADE");
        DB::statement("DROP TYPE IF EXISTS appointment_status CASCADE");
        DB::statement("DROP TYPE IF EXISTS service_type CASCADE");
        DB::statement("DROP TYPE IF EXISTS gender CASCADE");

        DB::statement("CREATE TYPE user_rol AS ENUM ('paciente', 'profesional')");
        DB::statement("CREATE TYPE type_subscription_plan AS
            ENUM ('paciente_gratis', 'paciente_avanzado', 'profesional_gratis', 'profesional_avanzado')");
        DB::statement("CREATE TYPE suscription_status AS
            ENUM ('activa', 'cancelada', 'expirada', 'prueba', 'pago_pendiente')");
        DB::statement("CREATE TYPE appointment_status AS
            ENUM ('programada', 'completada', 'cancelada_paciente', 'cancelada_profesional', 'no_asistio', 'pendiente_confirmacion')");
        DB::statement("CREATE TYPE service_type AS
            ENUM ('consultorio', 'domicilio')");
        DB::statement("CREATE TYPE gender AS
            ENUM ('masculino', 'femenino')");
    }
};
