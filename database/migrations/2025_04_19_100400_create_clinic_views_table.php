<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('clinic_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')
                ->constrained('patient_profiles')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('clinic_id')
                ->constrained('medical_clinics')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('view_count')
                ->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('clinic_views');
    }
};
