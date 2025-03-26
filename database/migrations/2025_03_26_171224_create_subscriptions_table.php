<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * CREATE TABLE suscripciones
     * (
     * suscripcion_id                SERIAL PRIMARY KEY,
     * usuario_id                    INT                      NOT NULL REFERENCES usuarios (usuario_id) ON DELETE CASCADE,
     * tipo_plan                     tipo_plan_suscripcion    NOT NULL,
     * estado                        estado_suscripcion       NOT NULL,
     * fecha_inicio                  TIMESTAMP WITH TIME ZONE NOT NULL,
     * fecha_fin                     TIMESTAMP WITH TIME ZONE,
     * prueba_termina_en             TIMESTAMP WITH TIME ZONE,
     * auto_renueva                  BOOLEAN                  DEFAULT FALSE,
     * id_suscripcion_proveedor_pago VARCHAR(255),
     * creado_en                     TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
     * actualizado_en                TIMESTAMP WITH TIME ZONE DEFAULT NOW()
     * );
     */
    public function up(): void {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->enum('subscription_type', ['paciente_gratis', 'paciente_avanzado', 'profesional_gratis', 'profesional_avanzado']);
            $table->enum('subscription_status', ['activa', 'cancelada', 'expirada', 'prueba', 'pago_pendiente']);
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->string('payment_provider_subscription_id', 255)->nullable();
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
