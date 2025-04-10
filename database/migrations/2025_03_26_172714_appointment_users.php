<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('appointment_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')
                ->constrained('appointments')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('professional_user_id')
                ->constrained('professional_profiles')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('patient_user_id')
                ->constrained('patient_profiles')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('appointment_users');
    }
};
