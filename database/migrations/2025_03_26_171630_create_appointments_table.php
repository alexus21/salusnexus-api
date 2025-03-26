<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('professional_user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('professional_profile_id')
                ->constrained('professional_profiles')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamp('appointment_datetime');
            $table->integer('duration_minutes')
                ->default(30);
            $table->enum('appointment_status',
                ['programada', 'completada', 'cancelada_paciente', 'cancelada_profesional', 'no_asistio', 'pendiente_confirmacion']);
            $table->enum('service_type', ['consultorio', 'domicilio']);
            $table->text('visit_reason');
            $table->text('patient_notes');
            $table->text('professional_notes');
            $table->text('cancellation_reason');
            $table->boolean('reminder_sent')
                ->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('appointments');
    }
};
