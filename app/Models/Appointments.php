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
        'reminder_sent',
        'remind_me_at',
    ];

    public static function getAllAppointments(): Collection {
        return DB::table('appointment_users')
            ->join('appointments', 'appointment_users.appointment_id', '=', 'appointments.id')
            ->join('patient_profiles', 'appointment_users.patient_user_id', '=', 'patient_profiles.id')
            ->join('users', 'patient_profiles.user_id', '=', 'users.id')
            ->select(
                'appointments.id as appointment_id',
                'appointments.appointment_date',
                'appointments.appointment_status',
                'patient_profiles.id as patient_id',
                'users.first_name',
                'users.last_name',
                'users.phone',
                'users.email',
                'patient_profiles.emergency_contact_name',
                'patient_profiles.emergency_contact_phone'
            )
            ->get();
    }

    public static function getAllUserAppointments($patient_id): Collection {
        return DB::table('appointment_users')
            ->join('appointments', 'appointment_users.appointment_id', '=', 'appointments.id')
            ->join('patient_profiles', 'appointment_users.patient_user_id', '=', 'patient_profiles.id')
            ->join('users', 'patient_profiles.user_id', '=', 'users.id')
            ->select(
                'appointments.id as appointment_id',
                'appointments.appointment_date',
                'appointments.appointment_status',
                'patient_profiles.id as patient_id',
                'users.first_name',
                'users.last_name',
                'users.phone',
                'patient_profiles.emergency_contact_name',
                'patient_profiles.emergency_contact_phone'
            )
            ->where('appointment_users.patient_user_id', $patient_id)
            ->get();
    }
}
