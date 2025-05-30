<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('clinic_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')
                ->constrained('medical_clinics')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->boolean('open')->default(true);
            $table->text('day_of_the_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('clinic_schedules');
    }
};
