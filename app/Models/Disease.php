<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Disease extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Obtiene los perfiles de pacientes que tienen esta enfermedad.
     */
    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(PatientProfiles::class, 'patient_diseases', 'disease_id', 'patient_profile_id')
            ->withPivot('reported_at')
            ->withTimestamps();
    }
} 