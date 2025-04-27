<?php

namespace App\Models;

use Database\Factories\AppointmentsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class Appointments extends Model {
    /** @use HasFactory<AppointmentsFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'appointments';

    protected $fillable = [
        'appointment_date',
        'duration_minutes',
        'appointment_status',
        'service_type',
        'visit_reason',
        'patient_notes',
        'professional_notes',
        'cancellation_reason',
        'reschedule_reason',
        'reminder_sent',
        'remind_me_at',
    ];

    public static function getCompletedAppointments($patient_id): Collection {
        return DB::table('appointment_users')
            ->join('appointments', 'appointment_users.appointment_id', '=', 'appointments.id')
            ->join('medical_clinics', 'appointment_users.clinic_id', '=', 'medical_clinics.id')
            ->join('professional_profiles', 'medical_clinics.professional_id', '=', 'professional_profiles.id')
            ->join('users', 'professional_profiles.user_id', '=', 'users.id')
            ->leftJoin('reviews', 'appointments.id', '=', 'reviews.appointment_id')
            ->select(
                'medical_clinics.id as clinic_id',
                'medical_clinics.clinic_name',
                'users.first_name',
                'users.last_name',
                'appointments.id as appointment_id',
                'appointments.appointment_date',
                'appointments.appointment_status',
                'medical_clinics.facade_photo',
                'medical_clinics.waiting_room_photo',
                'medical_clinics.office_photo',
                'reviews.id as review_id',
                'reviews.rating'
            )
            ->where('appointment_users.patient_user_id', $patient_id)
            ->get();
    }

    public static function getAllUserAppointments($user_rol, $id): Collection {
        $query = DB::table('appointment_users')
            ->join('appointments', 'appointment_users.appointment_id', '=', 'appointments.id')
            ->join('patient_profiles', 'appointment_users.patient_user_id', '=', 'patient_profiles.id')
            ->join('users', 'patient_profiles.user_id', '=', 'users.id')
            ->select(
                'appointments.id as appointment_id',
                'appointments.appointment_date',
                'appointments.appointment_status',
                'appointments.service_type',
                'patient_profiles.id as patient_id',
                'users.first_name',
                'users.last_name',
                'users.phone',
                'users.email',
                'users.date_of_birth',
                'users.profile_photo_path',
                'patient_profiles.emergency_contact_name',
                'patient_profiles.emergency_contact_phone'
            );

        if ($user_rol == "paciente") {
            $query->where('appointment_users.patient_user_id', $id);
        } else {
            $query->where('appointment_users.clinic_id', $id);
        }

        return $query->get();
    }
}
