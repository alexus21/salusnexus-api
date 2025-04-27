<?php

namespace App\Models;

use Database\Factories\MedicalClinicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
        $query = DB::table('medical_clinics')
            ->join('cities', 'medical_clinics.city_id', '=', 'cities.id')
            ->join('departments', 'cities.department_id', '=', 'departments.id')
            ->join('professional_profiles', 'medical_clinics.professional_id', '=', 'professional_profiles.id')
            ->join('users', 'professional_profiles.user_id', '=', 'users.id')
            ->join('professional_specialities', 'professional_profiles.id', '=', 'professional_specialities.professional_id')
            ->join('specialities', 'professional_specialities.speciality_id', '=', 'specialities.id')
            ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
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
                'specialities.name AS speciality_name',
                'subscriptions.subscription_type',
            )
            ->when($clinic_id, function ($query, $clinic_id) {
                return $query->where('medical_clinics.id', '=', $clinic_id);
            })
            ->orderBy('subscription_type', 'ASC');

        return $clinic_id ? $query->first() : $query->get();
    }

    public static function verifyClinicOwnership($clinicId, $professionalId) {
        return DB::table('medical_clinics')
            ->join('professional_profiles', 'medical_clinics.professional_id', '=', 'professional_profiles.id')
            ->where('medical_clinics.professional_id', $professionalId)
            ->where('medical_clinics.id', $clinicId)
            ->first();
    }

    public static function getMyClinic($patient_id){
        return DB::table('medical_clinics')
            ->join('professional_profiles', 'medical_clinics.professional_id', '=', 'professional_profiles.id')
            ->join('users', 'professional_profiles.user_id', '=', 'users.id')
            ->join('cities', 'medical_clinics.city_id', '=', 'cities.id')
            ->join('departments', 'cities.department_id', '=', 'departments.id')
            ->join('professional_specialities', 'professional_profiles.id', '=', 'professional_specialities.professional_id')
            ->join('specialities', 'professional_specialities.speciality_id', '=', 'specialities.id')
            ->select(
                'users.first_name',
                'users.last_name',
                'users.phone',
                'users.email',
                'users.profile_photo_path',
                'users.verified',
                'professional_profiles.id as professional_id',
                'professional_profiles.biography',
                'professional_profiles.home_visits',
                'professional_profiles.years_of_experience',
                'medical_clinics.id as clinic_id',
                'medical_clinics.clinic_name',
                'medical_clinics.address',
                'medical_clinics.clinic_address_reference',
                'medical_clinics.clinic_latitude',
                'medical_clinics.clinic_longitude',
                'medical_clinics.description',
                'medical_clinics.facade_photo',
                'medical_clinics.waiting_room_photo',
                'medical_clinics.office_photo',
                'cities.id as city_id',
                'cities.name as city_name',
                'departments.name as department_name',
                'specialities.name as speciality'
            )
            ->where('professional_profiles.id', $patient_id)
            ->first();
    }
}
