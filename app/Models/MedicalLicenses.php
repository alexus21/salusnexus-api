<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class MedicalLicenses extends Model {
    /** @use HasFactory<\Database\Factories\MedicalLicensesFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'medical_licenses';

    protected $fillable = [
        'professional_speciality_id',
        'license_number',
        'licensing_authority',
        'issue_date',
        'expiration_date',
        'speciality_id',
        'license_image_path',
    ];
}
