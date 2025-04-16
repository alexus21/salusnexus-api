<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('users')->insert([[
            'first_name' => 'Ángel',
            'last_name' => 'Vásquez',
            'date_of_birth' => '2003-05-03',
            'gender' => 'masculino',
            'dui' => '12345678-9',
            'phone' => '+503 7011-6901',
            'address' => 'Calle 1, San Salvador',
            'latitude' => 13.7034,
            'longitude' => -89.2034,
            'address_reference' => 'Cerca de la Plaza Barrios',
            'email' => 'angelvasquez@salusnexus.admin.com',
            'password' => Hash::make(env('ADMIN_PASSWORD')),
            'user_rol' => 'administrador',
            'profile_photo_path' => null,
            'active' => true,
            'verified' => true,
            'email_verified_at' => Carbon::now(),
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ],[
            'first_name' => 'Alex',
            'last_name' => 'Ulloa',
            'date_of_birth' => '2002-12-02',
            'gender' => 'masculino',
            'dui' => '12345678-0',
            'phone' => '+503 6454-9192',
            'address' => 'Calle 2, San Salvador',
            'latitude' => 13.7034,
            'longitude' => -89.2034,
            'address_reference' => 'Cerca de la Plaza Barrios',
            'email' => 'alexulloa@salusnexus.admin.com',
            'password' => Hash::make(env('ADMIN_PASSWORD')),
            'user_rol' => 'administrador',
            'profile_photo_path' => null,
            'active' => true,
            'verified' => true,
            'email_verified_at' => Carbon::now(),
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]
        ]);
    }
}
