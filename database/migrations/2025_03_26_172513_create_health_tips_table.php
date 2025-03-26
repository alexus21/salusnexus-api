<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * CREATE TABLE consejos_salud
     * (
     * consejo_id          SERIAL PRIMARY KEY,
     * titulo              VARCHAR(255) NOT NULL,
     * contenido           TEXT         NOT NULL,
     * categoria           VARCHAR(100),
     * etiquetas_audiencia VARCHAR(255),
     * fecha_publicacion   DATE DEFAULT CURRENT_DATE,
     * referencia_fuente   VARCHAR(512)
     * );
     */
    public function up(): void {
        Schema::create('health_tips', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('content');
            $table->foreignId('category_id')
                ->constrained('health_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('audience_tags', 255)->nullable();
            $table->date('publication_date')->default(now());
            $table->string('source_reference', 512)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('health_tips');
    }
};
