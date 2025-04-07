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
        'home_address',
        'home_latitude',
        'home_longitude',
        'home_address_reference',
        'emergency_contact_name',
        'emergency_contact_phone',
        'user_id',
    ];
}
