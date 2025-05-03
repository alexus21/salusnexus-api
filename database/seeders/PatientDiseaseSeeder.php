<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\PatientProfiles;
use App\Models\PatientDisease;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientDiseaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los perfiles de pacientes
        $patients = PatientProfiles::all();
        
        // Si no hay pacientes, salimos
        if ($patients->isEmpty()) {
            return;
        }
        
        // Obtener todas las enfermedades
        $diseases = Disease::all();
        
        // Si no hay enfermedades, salimos
        if ($diseases->isEmpty()) {
            return;
        }
        
        // Para cada paciente, asignar entre 1 y 3 enfermedades aleatorias
        foreach ($patients as $patient) {
            // Seleccionar aleatoriamente entre 1 y 3 enfermedades para este paciente
            $randomDiseases = $diseases->random(rand(1, 3));
            
            foreach ($randomDiseases as $disease) {
                PatientDisease::create([
                    'patient_profile_id' => $patient->id,
                    'disease_id' => $disease->id,
                    'reported_at' => now()->subDays(rand(1, 30)) // Fecha aleatoria en los últimos 30 días
                ]);
            }
        }
    }
} 