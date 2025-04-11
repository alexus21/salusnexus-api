<?php

namespace App\Models;

use Database\Factories\MedicalClinicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class MedicalClinic extends Model {
    /** @use HasFactory<MedicalClinicFactory> */
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'main_specialty',
        'address',
        'phone_number',
        'email',
        'website',
        'description',
        'latitude',
        'longitude',
    ];
}
