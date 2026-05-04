<?php
// database/seeders/ServiceCategoryUserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ServiceCategoryUserSeeder extends Seeder
{
    /**
     * Run the database seeds for the pivot table `service_category_user`.
     * Creates realistic relationships between providers and service categories.
     *
     * @return void
     */
    public function run()
    {
        $providers = User::where('role', 'PROVIDER')->get();
        $categories = ServiceCategory::all();

        if ($providers->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('⚠️ No providers or categories found. Run UserSeeder and ServiceCategorySeeder first.');
            return;
        }

        foreach ($providers as $provider) {
            $randomCategories = $categories->random(rand(1, 3));
            foreach ($randomCategories as $category) {
                if (!DB::table('service_category_user')->where('user_id', $provider->id)->where('service_category_id', $category->id)->exists()) {
                    DB::table('service_category_user')->insert([
                        'user_id' => $provider->id,
                        'service_category_id' => $category->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('🎉 Relationships created between providers and service categories.');
    }
}
