<?php

namespace App\Models;

use Database\Factories\ReviewsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Reviews extends Model {
    /** @use HasFactory<ReviewsFactory> */
    use HasFactory, HasApiTokens;

    protected $table = 'reviews';

    protected $fillable = [
        'appointment_id',
        'rating',
        'comment',
        'review_datetime',
        'is_published',
        'professional_response',
        'response_datetime',
    ];
}
