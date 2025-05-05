<?php

namespace Database\Seeders;

use App\Models\Disease;
use Illuminate\Database\Seeder;

class DiseasesSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $diseases = [
            [
                'name' => 'Diabetes Tipo 1',
                'description' => 'Condición crónica en la que el páncreas produce muy poca o ninguna insulina por sí mismo.'
            ],
            [
                'name' => 'Diabetes Tipo 2',
                'description' => 'Enfermedad metabólica que causa un alto nivel de azúcar en sangre por resistencia a la insulina.'
            ],
            [
                'name' => 'Hipertensión Arterial',
                'description' => 'Condición en la que la fuerza de la sangre contra las paredes de las arterias es consistentemente demasiado alta.'
            ],
            [
                'name' => 'Asma',
                'description' => 'Enfermedad crónica que afecta las vías respiratorias que llevan aire hacia y desde los pulmones.'
            ],
            [
                'name' => 'Artritis Reumatoide',
                'description' => 'Enfermedad inflamatoria crónica que afecta principalmente las articulaciones.'
            ],
            [
                'name' => 'Enfermedad Cardíaca Coronaria',
                'description' => 'Estrechamiento de los vasos sanguíneos pequeños que suministran sangre y oxígeno al corazón.'
            ],
            [
                'name' => 'Depresión',
                'description' => 'Trastorno del estado de ánimo que causa un sentimiento persistente de tristeza y pérdida de interés.'
            ],
            [
                'name' => 'Enfermedad Pulmonar Obstructiva Crónica (EPOC)',
                'description' => 'Enfermedad pulmonar inflamatoria crónica que causa obstrucción del flujo de aire desde los pulmones.'
            ],
            [
                'name' => 'Cáncer de Mama',
                'description' => 'Cáncer que se forma en las células de los senos (mamas).'
            ],
            [
                'name' => 'Alzheimer',
                'description' => 'Trastorno neurológico progresivo que causa que el cerebro se encoja y las células cerebrales mueran.'
            ],
            [
                'name' => 'Obesidad',
                'description' => 'Condición médica compleja en la que hay una acumulación excesiva de grasa corporal.'
            ],
            [
                'name' => 'Osteoporosis',
                'description' => 'Enfermedad que debilita los huesos, haciéndolos frágiles y más propensos a romperse.'
            ],
            [
                'name' => 'Enfermedad Renal Crónica',
                'description' => 'Pérdida gradual de la función renal con el tiempo.'
            ],
            [
                'name' => 'Fibromialgia',
                'description' => 'Trastorno caracterizado por dolor musculoesquelético generalizado acompañado de fatiga, sueño, memoria y problemas del estado de ánimo.'
            ],
            [
                'name' => 'Hipotiroidismo',
                'description' => 'Condición en la que la glándula tiroides no produce suficiente hormona tiroidea.'
            ],
            [
                'name' => 'Cirrosis Hepática',
                'description' => 'Enfermedad crónica del hígado que causa daño hepático progresivo y cicatrización.'
            ],
            [
                'name' => 'VIH/SIDA',
                'description' => 'Virus de la inmunodeficiencia humana (VIH) que ataca el sistema inmunológico y puede llevar al SIDA.'
            ],
            [
                'name' => 'Ébola',
                'description' => 'Enfermedad viral grave y a menudo mortal causada por el virus del Ébola.'
            ],
            [
                'name' => 'Esclerosis Múltiple',
                'description' => 'Enfermedad autoinmune que afecta el sistema nervioso central, dañando la mielina que recubre las neuronas.'
            ],
            [
                'name' => 'Parkinson',
                'description' => 'Trastorno neurodegenerativo que afecta el movimiento y causa temblores, rigidez y problemas de equilibrio.'
            ],
            [
                'name' => 'Esquizofrenia',
                'description' => 'Trastorno mental grave que afecta la forma en que una persona piensa, siente y se comporta.'
            ],
            [
                'name' => 'Trastorno de Ansiedad Generalizada',
                'description' => 'Trastorno mental caracterizado por preocupación excesiva y persistente sobre diversas actividades.'
            ],
            [
                'name' => 'Trastorno Bipolar',
                'description' => 'Trastorno del estado de ánimo que causa cambios extremos en el estado de ánimo, energía y actividad.'
            ],
            [
                'name' => 'Acné',
                'description' => 'Condición de la piel que ocurre cuando los folículos pilosos se tapan con aceite y células muertas de la piel.'
            ],
            [
                'name' => 'Psoriasis',
                'description' => 'Enfermedad autoinmune que causa la acumulación rápida de células de la piel, formando escamas y manchas rojas.'
            ],
            [
                'name' => 'Eczema',
                'description' => 'Condición que causa piel seca, roja y con picazón.'
            ],
            [
                'name' => 'Migraña',
                'description' => 'Dolor de cabeza intenso y recurrente que a menudo se acompaña de náuseas y sensibilidad a la luz.'
            ],
            [
                'name' => 'Cáncer de Pulmón',
                'description' => 'Cáncer que comienza en los pulmones y puede diseminarse a otras partes del cuerpo.'
            ],
            [
                'name' => 'Cáncer de Piel',
                'description' => 'Cáncer que se forma en la piel, generalmente en áreas expuestas al sol.'
            ],
            [
                'name' => 'Cáncer de Colon',
                'description' => 'Cáncer que comienza en el intestino grueso (colon) o el recto.'
            ],
        ];

        foreach ($diseases as $disease) {
            Disease::create($disease);
        }
    }
}
