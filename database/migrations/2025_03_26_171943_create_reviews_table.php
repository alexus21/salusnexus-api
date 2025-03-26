<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')
                ->unique()
                ->constrained('appointments')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('patient_user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('professional_user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->smallInteger('rating')
                ->check('rating >= 1 AND rating <= 5');
            $table->text('comment');
            $table->timestamp('review_datetime')
                ->default(now());
            $table->boolean('is_published')
                ->default(true);
            $table->text('professional_response');
            $table->timestamp('response_datetime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('reviews');
    }
};
