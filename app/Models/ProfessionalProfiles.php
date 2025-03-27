<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class ProfessionalProfiles extends Model {
    /** @use HasFactory<\Database\Factories\ProfessionalProfilesFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'license_number',
        'biography',
        'clinic_name',
        'clinic_address_1',
        'clinic_address_2',
        'clinic_city_id',
        'clinic_latitude',
        'clinic_longitude',
        'home_visits',
        'years_of_experience',
        'website_url',
        'user_id',
    ];
}
