<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\SubscriptionFeaturesSeeder;
use Database\Seeders\SubscriptionPlansSeeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        // User::factory(10)->create();

        $this->call([
            AdminSeeder::class,
            MedicationsCategoriesSeeder::class,
            HealthCategorySeeder::class,
            DepartmentsSeeder::class,
            CitiesSeeder::class,
            SpecialitiesSeeder::class,
            SubscriptionPlansSeeder::class,
            SubscriptionFeaturesSeeder::class,
            DiseasesSeeder::class,
            PatientDiseaseSeeder::class,
        ]);
    }
}
