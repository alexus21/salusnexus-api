<?php

namespace App\Models;

use Database\Factories\MedicalClinicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;

class MedicalClinic extends Model {
    /** @use HasFactory<MedicalClinicFactory> */
    use HasFactory, HasApiTokens;

    protected $fillable = [
        // Requeridos
        'clinic_name',
        'description',
        'address',
        'clinic_address_reference',
        'clinic_latitude',
        'clinic_longitude',
        'city_id',
        'speciality_type',
        'professional_id',

        // Photos
        'facade_photo',
        'waiting_room_photo',
        'office_photo',
    ];

    public function getClinicInfo($clinic_id) {
//        JOIN professional_specialities ON professional_profiles.id = professional_specialities.professional_id
//          JOIN specialities ON professional_specialities.speciality_id = specialities.id;

        $query = DB::table('medical_clinics')
            ->join('cities', 'medical_clinics.city_id', '=', 'cities.id')
            ->join('departments', 'cities.department_id', '=', 'departments.id')
            ->join('professional_profiles', 'medical_clinics.professional_id', '=', 'professional_profiles.id')
            ->join('users', 'professional_profiles.user_id', '=', 'users.id')
            ->join('professional_specialities', 'professional_profiles.id', '=', 'professional_specialities.professional_id')
            ->join('specialities', 'professional_specialities.speciality_id', '=', 'specialities.id')
            ->select(
                'medical_clinics.*',
                'cities.name AS city_name',
                'departments.name AS department_name',
                'professional_profiles.home_visits',
                'professional_profiles.years_of_experience',
                'professional_profiles.website_url',
                'users.first_name',
                'users.last_name',
                'users.address',
                'users.latitude',
                'users.longitude',
                'users.phone',
                'users.email',
                'specialities.name AS speciality_name'
            )
            ->when($clinic_id, function ($query, $clinic_id) {
                return $query->where('medical_clinics.id', '=', $clinic_id);
            });

        return $clinic_id ? $query->first() : $query->get();
    }
}
