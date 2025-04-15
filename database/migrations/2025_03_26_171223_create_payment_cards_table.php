<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('payment_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_number');
            $table->string('cardholder_name');
            $table->string('expiration_date');
            $table->enum('payment_provider', [
                'visa',
                'mastercard',
                'maestro',
                'paypal',
                'diners',
                'amex'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('payment_cards');
    }
};
