<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        DB::statement("CREATE TYPE user_rol AS ENUM ('paciente', 'profesional')");
        DB::statement("CREATE TYPE type_subscription_plan AS
                ENUM ('paciente_gratis', 'paciente_avanzado', 'profesional_gratis', 'profesional_avanzado')");
        DB::statement("CREATE TYPE suscription_status AS
                ENUM ('activa', 'cancelada', 'expirada', 'prueba', 'pago_pendiente')");
        DB::statement("CREATE TYPE appointment_status AS
                ENUM ('programada', 'completada', 'cancelada_paciente', 'cancelada_profesional', 'no_asistio', 'pendiente_confirmacion')");
        DB::statement("CREATE TYPE service_type AS ENUM ('consultorio', 'domicilio')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        DB::statement("DROP TYPE user_rol");
        DB::statement("DROP TYPE type_subscription_plan");
        DB::statement("DROP TYPE suscription_status");
        DB::statement("DROP TYPE appointment_status");
        DB::statement("DROP TYPE service_type");
    }
};
