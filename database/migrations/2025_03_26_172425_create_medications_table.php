<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->text('functions')->nullable();
            $table->text('benefits')->nullable();
            $table->text('precautions')->nullable();
            $table->text('contraindications')->nullable();
            $table->text('dose_info')->nullable();
            $table->string('image_url', 512)->nullable();
            $table->foreignId('category_id')
                ->constrained('medications_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('medications');
    }
};
