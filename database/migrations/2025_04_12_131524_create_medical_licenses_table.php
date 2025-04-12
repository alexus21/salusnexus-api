<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('medical_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')
                ->constrained('professional_profiles')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('license_number')->unique();
            $table->enum('licensing_authority', [
                'Ministerio de Salud',
                'Consejo Superior de Salud Pública',
                'Departamento de Comercio EE. UU.',
                'OMS/OPS'
            ]);
            $table->date('issue_date')
                ->check('issue_date <= expiration_date');
            $table->date('expiration_date')
                ->check('expiration_date >= issue_date');
            $table->string('license_image_path', 512)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('medical_licenses');
    }
};
