<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * CREATE TABLE perfiles_pacientes
     * (
     * perfil_id                    SERIAL PRIMARY KEY,
     * usuario_id                   INT UNIQUE NOT NULL REFERENCES usuarios (usuario_id) ON DELETE CASCADE,
     * fecha_nacimiento             DATE,
     * genero                       VARCHAR(50),
     * direccion_domicilio_1        VARCHAR(255),
     * direccion_domicilio_2        VARCHAR(255),
     * ciudad_domicilio             VARCHAR(100),
     * departamento_domicilio       VARCHAR(100),
     * latitud_domicilio            DECIMAL(10, 8),
     * longitud_domicilio           DECIMAL(11, 8),
     * nombre_contacto_emergencia   VARCHAR(200),
     * telefono_contacto_emergencia VARCHAR(20)
     * );
     */
    public function up(): void {
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->date('date_of_birth')->check('date_of_birth <= NOW()');
            $table->enum('gender', ['masculino', 'femenino']);
            $table->string('home_address_1', 255);
            $table->string('home_address_2', 255);
            $table->foreignId('city_id')
                ->constrained('cities')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('department_id')
                ->constrained('departments')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->decimal('home_latitude', 10, 8);
            $table->decimal('home_longitude', 11, 8);
            $table->string('emergency_contact_name', 200);
            $table->string('emergency_contact_phone', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('patient_profiles');
    }
};
