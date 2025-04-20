<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('subscription_type', [
                'paciente_gratis',
                'paciente_avanzado',
                'profesional_gratis',
                'profesional_avanzado'
            ])->unique();
            $table->decimal('price_monthly', 8, 2)->nullable();
            $table->decimal('price_annual', 8, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('subscription_plans');
    }
};
