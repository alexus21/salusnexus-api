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
                'description' => 'Especialidad médica que se enfoca en el diagnóstico y tratamiento de trastornos alérgicos e inmunológicos, como el asma, rinitis, urticaria y enfermedades autoinmunes.',
            ],
            [
                'name' => 'Anatomía patológica',
                'description' => 'Disciplina encargada del estudio de tejidos, órganos y células para identificar enfermedades, principalmente mediante el uso de técnicas microscópicas.',
            ],
            [
                'name' => 'Anestesiología',
                'description' => 'Especialidad que se ocupa de la administración de anestesia y el manejo del dolor durante y después de los procedimientos quirúrgicos.',
            ],
            [
                'name' => 'Angiología y cirugía vascular',
                'description' => 'Se encarga del diagnóstico y tratamiento de enfermedades del sistema circulatorio, como venas varicosas, trombosis y arteriosclerosis.',
            ],
            [
                'name' => 'Cardiología',
                'description' => 'Especialidad dedicada al estudio, diagnóstico y tratamiento de las enfermedades del corazón y el sistema circulatorio.',
            ],
            [
                'name' => 'Cirugía cardiovascular',
                'description' => 'Rama quirúrgica que trata patologías del corazón y grandes vasos, como bypass coronario, reemplazo valvular o corrección de aneurismas.',
            ],
            [
                'name' => 'Cirugía general',
                'description' => 'Especialidad que abarca procedimientos quirúrgicos del sistema digestivo, endocrino, hernias, entre otros.',
            ],
            [
                'name' => 'Cirugía maxilofacial',
                'description' => 'Combina medicina y odontología para tratar quirúrgicamente enfermedades, traumatismos y malformaciones de la cara, boca y mandíbula.',
            ],
            [
                'name' => 'Cirugía pediátrica',
                'description' => 'Cirugía especializada en el tratamiento quirúrgico de enfermedades y malformaciones en recién nacidos, niños y adolescentes.',
            ],
            [
                'name' => 'Cirugía plástica, estética y reconstructiva',
                'description' => 'Se enfoca en la corrección de defectos físicos congénitos o adquiridos, así como en procedimientos estéticos para mejorar la apariencia.',
            ],
            [
                'name' => 'Dermatología',
                'description' => 'Especialidad que diagnostica y trata enfermedades de la piel, cabello, uñas y mucosas, como acné, psoriasis o cáncer de piel.',
            ],
            [
                'name' => 'Endocrinología y nutrición',
                'description' => 'Área que se dedica al estudio de trastornos hormonales y metabólicos, como la diabetes, obesidad y disfunción tiroidea.',
            ],
            [
                'name' => 'Enfermedades infecciosas',
                'description' => 'Se enfoca en la prevención, diagnóstico y tratamiento de infecciones causadas por bacterias, virus, hongos y parásitos.',
            ],
            [
                'name' => 'Estomatología',
                'description' => 'Especialidad que trata las enfermedades de la cavidad oral, incluyendo dientes, encías, lengua y mucosas bucales.',
            ],
            [
                'name' => 'Farmacología clínica',
                'description' => 'Disciplina que estudia el uso seguro y eficaz de los medicamentos en humanos, evaluando efectos terapéuticos y adversos.',
            ],
            [
                'name' => 'Gastroenterología',
                'description' => 'Área dedicada al estudio del sistema digestivo y sus enfermedades, como úlceras, colitis o enfermedad celíaca.',
            ],
            [
                'name' => 'Genética médica',
                'description' => 'Se centra en el diagnóstico y manejo de trastornos hereditarios y genéticos, asesorando también a familias sobre riesgos genéticos.',
            ],
            [
                'name' => 'Geriatría',
                'description' => 'Especialidad médica enfocada en la atención integral de los adultos mayores, considerando aspectos físicos, mentales y sociales.',
            ],
            [
                'name' => 'Ginecología y obstetricia',
                'description' => 'Se ocupa de la salud reproductiva de la mujer y el seguimiento del embarazo, parto y puerperio.',
            ],
            [
                'name' => 'Hematología y hemoterapia',
                'description' => 'Rama que estudia las enfermedades de la sangre y órganos hematopoyéticos, así como el manejo de transfusiones.',
            ],
            [
                'name' => 'Hepatología',
                'description' => 'Especialidad centrada en el diagnóstico y tratamiento de enfermedades del hígado, vesícula biliar y vías biliares.',
            ],
            [
                'name' => 'Inmunología',
                'description' => 'Estudia el sistema inmunitario, sus alteraciones y enfermedades asociadas como inmunodeficiencias y autoinmunidad.',
            ],
            [
                'name' => 'Medicina del deporte',
                'description' => 'Trata lesiones deportivas, mejora el rendimiento físico y promueve el ejercicio como herramienta preventiva.',
            ],
            [
                'name' => 'Medicina del trabajo',
                'description' => 'Previene y trata enfermedades relacionadas con el entorno laboral y promueve la salud ocupacional.',
            ],
            [
                'name' => 'Medicina familiar y comunitaria',
                'description' => 'Brinda atención médica integral y continua al individuo y su familia, en el contexto de su comunidad.',
            ],
            [
                'name' => 'Medicina física y rehabilitación',
                'description' => 'Se ocupa de la recuperación funcional de pacientes con discapacidad mediante terapias físicas y tecnológicas.',
            ],
            [
                'name' => 'Medicina intensiva',
                'description' => 'Maneja pacientes en estado crítico que requieren soporte vital en unidades de cuidados intensivos (UCI).',
            ],
            [
                'name' => 'Medicina interna',
                'description' => 'Diagnostica y trata enfermedades en adultos, con enfoque clínico integral y no quirúrgico.',
            ],
            [
                'name' => 'Medicina nuclear',
                'description' => 'Utiliza materiales radiactivos para diagnóstico y tratamiento de enfermedades, especialmente en oncología y cardiología.',
            ],
            [
                'name' => 'Medicina preventiva y salud pública',
                'description' => 'Busca prevenir enfermedades, promover la salud y mejorar la calidad de vida a nivel comunitario y poblacional.',
            ],
            [
                'name' => 'Nefrología',
                'description' => 'Diagnostica y trata enfermedades del riñón, incluyendo insuficiencia renal, litiasis y enfermedades glomerulares.',
            ],
            [
                'name' => 'Neumología',
                'description' => 'Estudia y trata enfermedades del aparato respiratorio como asma, EPOC, neumonía o apnea del sueño.',
            ],
            [
                'name' => 'Neurocirugía',
                'description' => 'Cirugía especializada en el tratamiento de trastornos del sistema nervioso central y periférico, incluyendo tumores y traumatismos.',
            ],
            [
                'name' => 'Neurofisiología clínica',
                'description' => 'Evalúa el funcionamiento del sistema nervioso mediante estudios como electroencefalogramas o electromiografías.',
            ],
            [
                'name' => 'Neurología',
                'description' => 'Diagnostica y trata enfermedades del sistema nervioso como epilepsia, esclerosis múltiple, Parkinson o migrañas.',
            ],
            [
                'name' => 'Oftalmología',
                'description' => 'Se dedica al estudio y tratamiento médico y quirúrgico de enfermedades de los ojos y la visión.',
            ],
            [
                'name' => 'Oncología médica',
                'description' => 'Rama que se enfoca en el diagnóstico y tratamiento no quirúrgico del cáncer, principalmente mediante quimioterapia e inmunoterapia.',
            ],
            [
                'name' => 'Oncología radioterápica',
                'description' => 'Utiliza radiaciones ionizantes con fines terapéuticos para el tratamiento del cáncer.',
            ],
            [
                'name' => 'Otorrinolaringología',
                'description' => 'Estudia y trata afecciones de oído, nariz y garganta, incluyendo cirugías como amigdalectomías y septoplastias.',
            ],
            [
                'name' => 'Patología clínica',
                'description' => 'Se ocupa del análisis de muestras biológicas en laboratorio clínico para apoyar el diagnóstico médico.',
            ],
            [
                'name' => 'Pediatría',
                'description' => 'Atiende el desarrollo, prevención y tratamiento de enfermedades en niños y adolescentes.',
            ],
            [
                'name' => 'Psiquiatría',
                'description' => 'Diagnostica y trata trastornos mentales como la depresión, ansiedad, esquizofrenia o trastorno bipolar.',
            ],
            [
                'name' => 'Radiodiagnóstico',
                'description' => 'Utiliza técnicas de imagen como rayos X, tomografías y resonancias para el diagnóstico de enfermedades.',
            ],
            [
                'name' => 'Reumatología',
                'description' => 'Especialidad médica que trata enfermedades inflamatorias, autoinmunes y degenerativas del sistema musculoesquelético.',
            ],
            [
                'name' => 'Traumatología y ortopedia',
                'description' => 'Diagnostica y trata lesiones del aparato locomotor, como fracturas, luxaciones y deformidades óseas.',
            ],
            [
                'name' => 'Urología',
                'description' => 'Se ocupa de las enfermedades del aparato urinario y del sistema reproductor masculino, tanto médicas como quirúrgicas.',
            ],
        ]);
    }
}
