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
            $table->date('appointment_date');
            $table->integer('duration_minutes')
                ->default(30)
                ->nullable();
            $table->enum('appointment_status',
                ['programada', 'completada', 'cancelada_paciente', 'cancelada_profesional', 'no_asistio', 'pendiente_confirmacion'])
                ->default('programada');
            $table->enum('service_type', ['consultorio', 'domicilio'])
                ->default('consultorio');
            $table->text('visit_reason');
            $table->text('patient_notes')
                ->nullable();
            $table->text('professional_notes')
                ->nullable();
            $table->text('cancellation_reason')
                ->nullable();
            $table->text('reschedule_reason')
                ->nullable();
            $table->boolean('reminder_sent')
                ->default(false);
            $table->integer('remind_me_at')
                ->default(30)
                ->nullable();
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
