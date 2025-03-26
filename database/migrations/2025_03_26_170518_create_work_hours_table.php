<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     *
     * CREATE TABLE horas_trabajo
     * (
     * horas_trabajo_id      SERIAL PRIMARY KEY,
     * perfil_profesional_id INT                    NOT NULL REFERENCES perfiles_profesionales (perfil_id) ON DELETE CASCADE,
     * dia_semana            SMALLINT               NOT NULL CHECK (dia_semana >= 0 AND dia_semana <= 6),
     * hora_inicio           TIME WITHOUT TIME ZONE NOT NULL,
     * hora_fin              TIME WITHOUT TIME ZONE NOT NULL,
     * esta_disponible       BOOLEAN DEFAULT TRUE,
     * CONSTRAINT unica_hora_trabajo UNIQUE (perfil_profesional_id, dia_semana, hora_inicio)
     * );
     */
    public function up(): void {
        Schema::create('work_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')
                ->constrained('professional_profiles')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->tinyInteger('day_of_week')
                ->unsigned()
                ->default(0)
                ->check('day_of_week >= 0 AND day_of_week <= 6');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')
                ->default(true);
            $table->unique(['professional_profile_id', 'day_of_week', 'start_time']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('work_hours');
    }
};
