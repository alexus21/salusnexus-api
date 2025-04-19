<?php

namespace App\Models;

use Database\Factories\ClinicViewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class ClinicView extends Model {
    /** @use HasFactory<ClinicViewFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'clinic_views';

    protected $fillable = [
        'patient_id',
        'clinic_id',
        'view_count'
    ];
}
