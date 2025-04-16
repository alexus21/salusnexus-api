<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class PaymentCard extends Model {
    /** @use HasFactory<\Database\Factories\PaymentCardFactory> */
    use HasFactory, HasApiTokens;

    protected $table = 'payment_cards';

    protected $fillable = [
        'card_number',
        'cardholder_name',
        'expiration_date',
        'payment_provider',
        'user_id',
    ];

}
