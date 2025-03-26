<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     */
    public function up(): void {
        Schema::create('professional_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('license_number', 50)->nullable();
            $table->text('biography')->nullable();
            $table->string('clinic_name', 200)->nullable();
            $table->string('clinic_address_1', 255)->nullable();
            $table->string('clinic_address_2', 255)->nullable();
            $table->foreignId('clinic_city_id')->constrained('cities')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('clinic_department_id')->constrained('departments')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('clinic_latitude', 10, 8)->nullable();
            $table->decimal('clinic_longitude', 11, 8)->nullable();
            $table->boolean('home_visits')->default(false);
            $table->integer('years_experience')->nullable();
            $table->string('website_url', 512)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        DB::statement('DROP TABLE IF EXISTS professional_profiles CASCADE');
    }
};
