<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicationsCategoriesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * - **Analgésicos**
     * - **Antiinflamatorios**
     * - **Antibióticos**
     * - **Antivirales**
     * - **Antifúngicos**
     * - **Antiparasitarios**
     * - **Antihistamínicos**
     * - **Antidepresivos**
     * - **Ansiolíticos**
     * - **Antipsicóticos**
     * - **Anticonvulsivos**
     * - **Relajantes musculares**
     * - **Broncodilatadores**
     * - **Antihipertensivos**
     * - **Diuréticos**
     * - **Hipoglucemiantes**
     * - **Anticoagulantes**
     * - **Antiácidos**
     * - **Laxantes**
     * - **Antiespasmódicos**
     * - **Inmunosupresores**
     * - **Corticoides**
     * - **Hormonas y análogos**
     * - **Quimioterapéuticos**
     * - **Vacunas**
     *
     * name
     * description
     */
    public function run(): void {
        DB::table('medications_categories')
            ->insert([
                ['name' => 'Analgésicos', 'description' => 'Medicamentos para aliviar el dolor'],
                ['name' => 'Antiinflamatorios', 'description' => 'Medicamentos para reducir la inflamación'],
                ['name' => 'Antibióticos', 'description' => 'Medicamentos para combatir infecciones bacterianas'],
                ['name' => 'Antivirales', 'description' => 'Medicamentos para combatir infecciones virales'],
                ['name' => 'Antifúngicos', 'description' => 'Medicamentos para combatir infecciones fúngicas'],
                ['name' => 'Antiparasitarios', 'description' => 'Medicamentos para combatir infecciones parasitarias'],
                ['name' => 'Antihistamínicos', 'description' => 'Medicamentos para combatir alergias'],
                ['name' => 'Antidepresivos', 'description' => 'Medicamentos para tratar la depresión'],
                ['name' => 'Ansiolíticos', 'description' => 'Medicamentos para tratar la ansiedad'],
                ['name' => 'Antipsicóticos', 'description' => 'Medicamentos para tratar trastornos psicóticos'],
                ['name' => 'Anticonvulsivos', 'description' => 'Medicamentos para tratar convulsiones'],
                ['name' => 'Relajantes musculares', 'description' => 'Medicamentos para relajar los músculos'],
                ['name' => 'Broncodilatadores', 'description' => 'Medicamentos para dilatar los bronquios'],
                ['name' => 'Antihipertensivos', 'description' => 'Medicamentos para tratar la hipertensión'],
                ['name' => 'Diuréticos', 'description' => 'Medicamentos para aumentar la eliminación de orina'],
                ['name' => 'Hipoglucemiantes', 'description' => 'Medicamentos para reducir la glucosa en sangre'],
                ['name' => 'Anticoagulantes', 'description' => 'Medicamentos para prevenir la coagulación sanguínea'],
                ['name' => 'Antiácidos', 'description' => 'Medicamentos para neutralizar el ácido estomacal'],
                ['name' => 'Laxantes', 'description' => 'Medicamentos para tratar el estreñimiento'],
                ['name' => 'Antiespasmódicos', 'description' => 'Medicamentos para tratar espasmos musculares'],
                ['name' => 'Inmunosupresores', 'description' => 'Medicamentos para suprimir el sistema inmunológico'],
                ['name' => 'Corticoides', 'description' => 'Medicamentos con efectos similares a las hormonas esteroides'],
                ['name' => 'Hormonas y análogos', 'description' => 'Medicamentos para regular hormonas'],
                ['name' => 'Quimioterapéuticos', 'description' => 'Medicamentos para tratar el cáncer'],
                ['name' => 'Vacunas', 'description' => 'Medicamentos para prevenir enfermedades'],
            ]);
    }
}
