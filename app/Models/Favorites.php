<?php

namespace App\Models;

use Database\Factories\FavoritesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Favorites extends Model {
    /** @use HasFactory<FavoritesFactory> */
    use HasFactory, HasApiTokens;

    protected $table = 'favorites';

    protected $fillable = [
        'clinic_id',
        'patient_id',
    ];

}
