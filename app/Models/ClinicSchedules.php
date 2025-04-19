<?php

namespace App\Models;

use Database\Factories\ClinicSchedulesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class ClinicSchedules extends Model {
    /** @use HasFactory<ClinicSchedulesFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'clinic_schedules';

    protected $fillable = [
        'clinic_id',
        'day_of_the_week',
        'start_time',
        'end_time',
    ];
}
