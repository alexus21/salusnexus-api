<?php

namespace App\Models;

use Database\Factories\PatientProfilesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class PatientProfiles extends Model {
    /** @use HasFactory<PatientProfilesFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'emergency_contact_name',
        'emergency_contact_phone',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtiene las enfermedades asociadas a este perfil de paciente.
     */
    public function diseases() {
        return $this->belongsToMany(Disease::class, 'patient_diseases', 'patient_profile_id', 'disease_id')
            ->withPivot('reported_at')
            ->withTimestamps();
    }
}
