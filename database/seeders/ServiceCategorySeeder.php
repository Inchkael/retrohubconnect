<?php
// database/seeders/ServiceCategorySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;
use Faker\Factory as Faker;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds for the `service_categories` table.
     * Creates 50 service categories with descriptions and statuses.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('fr_BE');
        $categories = [
            'Massage', 'Yoga', 'Méditation', 'Coaching', 'Sophrologie', 'Ostéopathie', 'Naturopathie', 'Réflexologie',
            'Reiki', 'Acupuncture', 'Pilates', 'Nutrition', 'Psychothérapie', 'Thérapie manuelle', 'Aromathérapie',
            'Chiropraxie', 'Kinésithérapie', 'Hypnothérapie', 'Shiatsu', 'Tai Chi', 'Ayurveda', 'Phytothérapie',
            'Homéopathie', 'Magnétothérapie', 'Luminothérapie', 'Musicothérapie', 'Art-thérapie', 'Danse-thérapie',
            'Thérapie par le rire', 'Gestion du stress', 'Coaching sportif', 'Coaching en développement personnel',
            'Coaching professionnel', 'Coaching familial', 'Coaching en nutrition', 'Coaching en bien-être',
            'Coaching en organisation', 'Coaching en communication', 'Coaching en leadership', 'Coaching en gestion du temps',
            'Coaching en confiance en soi', 'Coaching en gestion des émotions', 'Coaching en relation d\'aidant',
            'Coaching en reconversion professionnelle', 'Coaching en gestion de projet', 'Coaching en prise de parole',
            'Coaching en gestion des conflits', 'Coaching en gestion du changement', 'Coaching en équilibre vie pro/vie perso',
            'Coaching en gestion de carrière', 'Coaching en préparation mentale', 'Coaching en gestion de crise',
            'Coaching en gestion de l\'anxiété', 'Coaching en gestion de la colère', 'Coaching en gestion du deuil',
            'Coaching en gestion de la fatigue', 'Coaching en gestion du sommeil', 'Coaching en gestion du poids',
            'Coaching en gestion de la douleur', 'Coaching en gestion de la dépendance', 'Coaching en gestion de la timidité',
            'Coaching en gestion de la procrastination', 'Coaching en gestion de la motivation', 'Coaching en gestion de la créativité',
            'Coaching en gestion de la concentration', 'Coaching en gestion du burn-out', 'Coaching en gestion du stress post-traumatique',
            'Coaching en gestion des phobies', 'Coaching en gestion des TOC', 'Coaching en gestion des troubles alimentaires',
            'Coaching en gestion des addictions', 'Coaching en gestion des troubles du sommeil', 'Coaching en gestion des troubles anxieux',
            'Coaching en gestion des troubles dépressifs', 'Coaching en gestion des troubles bipolaires'
        ];

        foreach ($categories as $name) {
            ServiceCategory::create([
                'name' => $name,
                'description' => $faker->sentence(10),
                'is_highlighted' => $faker->boolean(20),
                'is_validated' => true,
            ]);
        }

        $this->command->info('✅ 50 service categories created successfully.');
    }
}
