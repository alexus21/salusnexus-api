<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * Ahuachapán
     * Cabañas
     * Chalatenango
     * Cuscatlán
     * La Libertad
     * La Paz
     * La Unión
     * Morazán
     * San Miguel
     * San Salvador
     * San Vicente
     * Santa Ana
     * Sonsonate
     * Usulután
     *
     * name
     */
    public function run(): void {
        DB::table('departments')
            ->insert([
                ['name' => 'Ahuachapán'], // 1
                ['name' => 'Santa Ana'], // 2
                ['name' => 'Sonsonate'], // 3
                ['name' => 'Chalatenango'], // 4
                ['name' => 'La Libertad'], // 5
                ['name' => 'San Salvador'], //6
                ['name' => 'Cuscatlán'], // 7
                ['name' => 'La Paz'], // 8
                ['name' => 'Cabañas'], // 9
                ['name' => 'San Vicente'], // 10
                ['name' => 'Usulután'], // 11
                ['name' => 'San Miguel'], // 12
                ['name' => 'Morazán'], // 13
                ['name' => 'La Unión'], // 14
            ]);
    }
}
