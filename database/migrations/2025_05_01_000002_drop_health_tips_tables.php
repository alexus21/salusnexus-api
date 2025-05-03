<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Primero eliminamos la tabla pivote si existe
        if (Schema::hasTable('disease_health_tips')) {
            Schema::dropIfExists('disease_health_tips');
        }
        
        // Luego eliminamos la tabla principal si existe
        if (Schema::hasTable('health_tips')) {
            Schema::dropIfExists('health_tips');
        }
    }

    /**
     * Reverse the migrations.
     * 
     * Nota: No se implementa recreación de tablas en caso de rollback
     * ya que el sistema evolucionó a otro modelo de datos.
     */
    public function down(): void {
        // No vamos a recrear las tablas en caso de rollback
    }
}; 