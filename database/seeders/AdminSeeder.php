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
            'dui' => '12345678-9',
            'phone' => '+503 7011 6901',
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
            'dui' => '12345678-0',
            'phone' => '+503 6454-9192',
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
