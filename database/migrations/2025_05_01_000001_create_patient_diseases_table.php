<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('patient_diseases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_profile_id')
                ->constrained('patient_profiles')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('disease_id')
                ->constrained('diseases')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamps();
            
            // Asegura que un paciente no pueda estar vinculado a la misma enfermedad múltiples veces
            $table->unique(['patient_profile_id', 'disease_id'], 'patient_diseases_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('patient_diseases');
    }
}; 