<?php

namespace App\Models;

use Database\Factories\ProfessionalProfilesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class ProfessionalProfiles extends Model {
    /** @use HasFactory<ProfessionalProfilesFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'biography',
        'clinic_name',
        'home_visits',
        'years_of_experience',
        'website_url',
        'user_id',
    ];
}
