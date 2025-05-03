<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientDisease extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_profile_id',
        'disease_id',
        'reported_at',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'reported_at' => 'datetime',
    ];

    /**
     * Obtiene el perfil de paciente asociado.
     */
    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfiles::class, 'patient_profile_id');
    }

    /**
     * Obtiene la enfermedad asociada.
     */
    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class, 'disease_id');
    }
} 