<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Image;
use App\Models\Product;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Faker\Factory as Faker;

class ImageSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 🔹 Créer 20 images standalone
        for ($i = 1; $i <= 20; $i++) {
            $image = Image::create([
                'name' => $faker->words(2, true),
                'src' => "https://picsum.photos/seed/product{$i}/600/600",
                'alt' => $faker->sentence(3),
            ]);

            // Ajouter le fichier via MediaLibrary  https://picsum.photos/id/237/200/300
            $image->addMediaFromUrl("https://picsum.photos/seed/product{$i}/600/600")
                ->toMediaCollection('default');
        }

        $this->command->info('✅ 20 images created with MediaLibrary!');

        // 🔹 Attacher aléatoirement une image à chaque produit
        $products = Product::all();
        $imageIds = Image::pluck('id');
        foreach ($products as $product) {

            // image principale
            $product->update([
                'image_id' => $imageIds->random(),
            ]);

            // galerie (1 à 4 images)
            $product->images()->syncWithoutDetaching(
                $imageIds->random(rand(1, 4))->toArray()
            );
        }

        $this->command->info('✅ Images attached to products!');
    }
}
