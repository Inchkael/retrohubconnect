<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Slider;
use App\Models\Image;

class SliderSeeder extends Seeder
{
    public function run()
    {
        // Supprime les anciennes données si nécessaire
        Slider::query()->delete();

        // Crée 3 slides avec leurs images associées
        for ($i = 1; $i <= 3; $i++) {
            // Crée une entrée dans la table sliders
            $slider = Slider::create([
                'title' => "Slide $i",
                'is_active' => true,
            ]);

            // Crée une image associée dans la table images (relation polymorphique)
            Image::create([
                'path' => "sliders/slide$i.jpg", // Chemin relatif dans le dossier storage/app/public
                'format' => 'jpg',
                'type' => 'original',
                'position' => 0,
                'imageable_id' => $slider->id,
                'imageable_type' => 'App\Models\Slider',
            ]);
        }
    }
}
