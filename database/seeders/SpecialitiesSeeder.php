<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialitiesSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('specialities')->insert([
            [
                'name' => 'Alergología e inmunología clínica',
                'description' => 'Descripción de Alergología e inmunología clínica',
            ],
            [
                'name' => 'Anatomía patológica',
                'description' => 'Descripción de Anatomía patológica',
            ],
            [
                'name' => 'Anestesiología',
                'description' => 'Descripción de Anestesiología',
            ],
            [
                'name' => 'Angiología y cirugía vascular',
                'description' => 'Descripción de Angiología y cirugía vascular',
            ],
            [
                'name' => 'Cardiología',
                'description' => 'Descripción de Cardiología',
            ],
            [
                'name' => 'Cirugía cardiovascular',
                'description' => 'Descripción de Cirugía cardiovascular',
            ],
            [
                'name' => 'Cirugía general',
                'description' => 'Descripción de Cirugía general',
            ],
            [
                'name' => 'Cirugía maxilofacial',
                'description' => 'Descripción de Cirugía maxilofacial',
            ],
            [
                'name' => 'Cirugía pediátrica',
                'description' => 'Descripción de Cirugía pediátrica',
            ],
            [
                'name' => 'Cirugía plástica, estética y reconstructiva',
                'description' => 'Descripción de Cirugía plástica, estética y reconstructiva',
            ],
            [
                'name' => 'Dermatología',
                'description' => 'Descripción de Dermatología',
            ],
            [
                'name' => 'Endocrinología y nutrición',
                'description' => 'Descripción de Endocrinología y nutrición',
            ],
            [
                'name' => 'Enfermedades infecciosas',
                'description' => 'Descripción de Enfermedades infecciosas',
            ],
            [
                'name' => 'Estomatología',
                'description' => 'Descripción de Estomatología',
            ],
            [
                'name' => 'Farmacología clínica',
                'description' => 'Descripción de Farmacología clínica',
            ],
            [
                'name' => 'Gastroenterología',
                'description' => 'Descripción de Gastroenterología',
            ],
            [
                'name' => 'Genética médica',
                'description' => 'Descripción de Genética médica',
            ],
            [
                'name' => 'Geriatría',
                'description' => 'Descripción de Geriatría',
            ],
            [
                'name' => 'Ginecología y obstetricia',
                'description' => 'Descripción de Ginecología y obstetricia',
            ],
            [
                'name' => 'Hematología y hemoterapia',
                'description' => 'Descripción de Hematología y hemoterapia',
            ],
            [
                'name' => 'Hepatología',
                'description' => 'Descripción de Hepatología',
            ],
            [
                'name' => 'Inmunología',
                'description' => 'Descripción de Inmunología',
            ],
            [
                'name' => 'Medicina del deporte',
                'description' => 'Descripción de Medicina del deporte',
            ],
            [
                'name' => 'Medicina del trabajo',
                'description' => 'Descripción de Medicina del trabajo',
            ],
            [
                'name' => 'Medicina familiar y comunitaria',
                'description' => 'Descripción de Medicina familiar y comunitaria',
            ],
            [
                'name' => 'Medicina física y rehabilitación',
                'description' => 'Descripción de Medicina física y rehabilitación',
            ],
            [
                'name' => 'Medicina intensiva',
                'description' => 'Descripción de Medicina intensiva',
            ],
            [
                'name' => 'Medicina interna',
                'description' => 'Descripción de Medicina interna',
            ],
            [
                'name' => 'Medicina nuclear',
                'description' => 'Descripción de Medicina nuclear',
            ],
            [
                'name' => 'Medicina preventiva y salud pública',
                'description' => 'Descripción de Medicina preventiva y salud pública',
            ],
            [
                'name' => 'Nefrología',
                'description' => 'Descripción de Nefrología',
            ],
            [
                'name' => 'Neumología',
                'description' => 'Descripción de Neumología',
            ],
            [
                'name' => 'Neurocirugía',
                'description' => 'Descripción de Neurocirugía',
            ],
            [
                'name' => 'Neurofisiología clínica',
                'description' => 'Descripción de Neurofisiología clínica',
            ],
            [
                'name' => 'Neurología',
                'description' => 'Descripción de Neurología',
            ],
            [
                'name' => 'Oftalmología',
                'description' => 'Descripción de Oftalmología',
            ],
            [
                'name' => 'Oncología médica',
                'description' => 'Descripción de Oncología médica',
            ],
            [
                'name' => 'Oncología radioterápica',
                'description' => 'Descripción de Oncología radioterápica',
            ],
            [
                'name' => 'Otorrinolaringología',
                'description' => 'Descripción de Otorrinolaringología',
            ],
            [
                'name' => 'Patología clínica',
                'description' => 'Descripción de Patología clínica',
            ],
            [
                'name' => 'Pediatría',
                'description' => 'Descripción de Pediatría',
            ],
            [
                'name' => 'Psiquiatría',
                'description' => 'Descripción de Psiquiatría',
            ],
            [
                'name' => 'Radiodiagnóstico',
                'description' => 'Descripción de Radiodiagnóstico',
            ],
            [
                'name' => 'Reumatología',
                'description' => 'Descripción de Reumatología',
            ],
            [
                'name' => 'Traumatología y ortopedia',
                'description' => 'Descripción de Traumatología y ortopedia',
            ],
            [
                'name' => 'Urología',
                'description' => 'Descripción de Urología',
            ],
        ]);
    }
}
