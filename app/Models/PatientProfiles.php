<?php

namespace App\Models;

use Database\Factories\PatientProfilesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class PatientProfiles extends Model {
    /** @use HasFactory<PatientProfilesFactory> */
    use HasFactory, HasApiTokens;
}
