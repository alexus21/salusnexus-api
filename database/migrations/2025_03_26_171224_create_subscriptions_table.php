<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->enum('subscription_type',
                ['paciente_gratis', 'paciente_avanzado', 'profesional_gratis', 'profesional_avanzado']);
            $table->enum('subscription_status',
                ['activa', 'cancelada', 'expirada', 'prueba', 'pago_pendiente']);
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->foreignId('payment_card_id')
                ->nullable()
                ->constrained('payment_cards')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('subscriptions');
    }
};
