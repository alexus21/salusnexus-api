<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('professional_specialities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')
                ->constrained('professional_profiles')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('speciality_id')
                ->constrained('specialities')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        DB::statement('DROP TABLE IF EXISTS professional_specialities CASCADE');
    }
};
