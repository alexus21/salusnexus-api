<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('home_address', 255);
            $table->decimal('home_latitude', 10, 7);
            $table->decimal('home_longitude', 10, 7);
            $table->string('home_address_reference', 255)->nullable();
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
