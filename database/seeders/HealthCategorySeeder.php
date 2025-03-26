<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HealthCategorySeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * - **Salud física**
     * - **Salud mental**
     * - **Salud emocional**
     * - **Salud social**
     * - **Salud ambiental**
     * - **Salud ocupacional**
     * - **Salud sexual y reproductiva**
     * - **Salud preventiva**
     * - **Salud pública**
     * - **Salud nutricional**
     * - **Salud infantil**
     * - **Salud geriátrica**
     * - **Salud comunitaria**
     * - **Salud digital**
     * - **Salud global**
     */
    public function run(): void {
        DB::table('health_categories')
            ->insert([
                ['name' => 'Salud física', 'description' => 'Aspectos relacionados con el cuerpo y sus funciones'],
                ['name' => 'Salud mental', 'description' => 'Aspectos relacionados con la mente y sus funciones'],
                ['name' => 'Salud emocional', 'description' => 'Aspectos relacionados con las emociones y su regulación'],
                ['name' => 'Salud social', 'description' => 'Aspectos relacionados con las relaciones interpersonales'],
                ['name' => 'Salud ambiental', 'description' => 'Aspectos relacionados con el entorno y su impacto en la salud'],
                ['name' => 'Salud ocupacional', 'description' => 'Aspectos relacionados con el trabajo y la salud'],
                ['name' => 'Salud sexual y reproductiva', 'description' => 'Aspectos relacionados con la sexualidad y la reproducción'],
                ['name' => 'Salud preventiva', 'description' => 'Aspectos relacionados con la prevención de enfermedades'],
                ['name' => 'Salud pública', 'description' => 'Aspectos relacionados con la salud de la población'],
                ['name' => 'Salud nutricional', 'description' => 'Aspectos relacionados con la alimentación y la nutrición'],
                ['name' => 'Salud infantil', 'description' => 'Aspectos relacionados con la salud de los niños'],
                ['name' => 'Salud geriátrica', 'description' => 'Aspectos relacionados con la salud de las personas mayores'],
                ['name' => 'Salud comunitaria', 'description' => 'Aspectos relacionados con la salud de la comunidad'],
                ['name' => 'Salud digital', 'description' => 'Aspectos relacionados con la salud en línea'],
                ['name' => 'Salud global', 'description' => 'Aspectos relacionados con la salud en todo el mundo']
            ]);
    }
}
