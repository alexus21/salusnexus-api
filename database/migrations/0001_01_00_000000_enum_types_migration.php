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
        DB::statement("CREATE TYPE rol_usuario AS ENUM ('paciente', 'profesional')");
        DB::statement("CREATE TYPE tipo_plan_suscripcion AS ENUM ('paciente_gratis', 'paciente_avanzado', 'profesional_gratis', 'profesional_avanzado')");
        DB::statement("CREATE TYPE estado_suscripcion AS ENUM ('activa', 'cancelada', 'expirada', 'prueba', 'pago_pendiente')");
        DB::statement("CREATE TYPE estado_cita AS ENUM ('programada', 'completada', 'cancelada_paciente', 'cancelada_profesional', 'no_asistio', 'pendiente_confirmacion')");
        DB::statement("CREATE TYPE tipo_servicio AS ENUM ('consultorio', 'domicilio')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        DB::statement("DROP TYPE rol_usuario");
        DB::statement("DROP TYPE tipo_plan_suscripcion");
        DB::statement("DROP TYPE estado_suscripcion");
        DB::statement("DROP TYPE estado_cita");
        DB::statement("DROP TYPE tipo_servicio");
    }
};
