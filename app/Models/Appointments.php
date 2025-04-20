<?php

namespace App\Models;

use Database\Factories\AppointmentsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
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
}
