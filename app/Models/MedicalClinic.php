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
        // Requeridos
        'professional_name',
        'speciality_type',
        'years_of_experience',
        'clinic_name',
        'address', // <- Ubicacion física escrita a mano
        'city_id',
        'professional_id',
        'home_visits',
        'rating',

        // Adicionales
        'phone_number',
        'email',
        'website',
        'description',
        'latitude',
        'longitude',

        // Photos
        'facade_photo',
        'waiting_room_photo',
        'office_photo',
    ];

    public function getClinicInfo(int $clinic_id){
        /*if ($clinic_id){

        }*/
    }
}
