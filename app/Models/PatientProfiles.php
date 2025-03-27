<?php

namespace App\Models;

use Database\Factories\PatientProfilesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class PatientProfiles extends Model {
    /** @use HasFactory<PatientProfilesFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'date_of_birth',
        'gender',
        'home_address_1',
        'home_address_2',
        'city_id',
        'home_latitude',
        'home_longitude',
        'emergency_contact_name',
        'emergency_contact_phone',
        'user_id',
    ];
}
