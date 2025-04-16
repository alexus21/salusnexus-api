<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('medical_clinics', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_name', 200);
            $table->string('address', 512);
            $table->string('clinic_address_reference', 512)->nullable();
            $table->string('clinic_latitude', 20)->nullable();
            $table->string('clinic_longitude', 20)->nullable();
            $table->string('description', 512);
            $table->foreignId('city_id')
                ->constrained('cities')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('facade_photo', 512);
            $table->string('waiting_room_photo', 512)->nullable();
            $table->string('office_photo', 512)->nullable();
            $table->foreignId('professional_id')
                ->constrained('professional_profiles')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('speciality_type', ['primaria', 'secundaria']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('medical_clinics');
    }
};
