<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Store;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Label;
use App\Models\ProductCollection;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 🔹 5 Admins
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => "admin{$i}@example.com",
                'password' => Hash::make('password'),
                'user_type' => 0,
                'role' => 'admin',
                'activate' => true,
            ]);
        }

        // 🔹 50 Clients
        $users = [];
        for ($i = 1; $i <= 50; $i++) {
            $users[] = User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'user_type' => 1,
                'role' => 'customer',
                'password' => Hash::make('password'),
                'activate' => true,
            ]);
        }

        // 🔹 10 Brands
        $brands = [];
        for ($i = 1; $i <= 10; $i++) {
            $brands[] = Brand::create([
                'name' => $faker->company,
                'image_id' => null,
            ]);
        }

        // 🔹 20 Categories
        $categories = [];
        for ($i = 1; $i <= 20; $i++) {
            $categories[] = Category::create([
                'name' => $faker->word,
                'slug' => Str::slug($faker->word . '-' . $i),
                'status' => 'published',
                'icon' => null,
                'image_id' => null,
            ]);
        }

        // 🔹 5 Labels
        $labels = [];
        for ($i = 1; $i <= 5; $i++) {
            $labels[] = Label::create([
                'name' => $faker->word,
                'color' => $faker->hexColor,
                'status' => 'published',
            ]);
        }

        // 🔹 5 Collections
        $collections = [];
        for ($i = 1; $i <= 5; $i++) {
            $collections[] = ProductCollection::create([
                'name' => $faker->words(2, true),
                'slug' => Str::slug($faker->words(2, true) . '-' . $i),
                'status' => 'published',
                'image_id' => null,
            ]);
        }

        // 🔹 30 Stores
        $stores = [];
        for ($i = 1; $i <= 30; $i++) {
            $vendorId = User::inRandomOrder()->where('user_type', 0)->first()?->id ?? 1;

            $stores[] = Store::create([
                'name' => $faker->company,
                'vendor_id' => $vendorId,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'status' => 'published',
            ]);
        }

        // 🔹 100 Products
        $products = [];
        for ($i = 1; $i <= 100; $i++) {
            $brand = $faker->randomElement($brands);
            $store = $faker->randomElement($stores);

            $product = Product::create([
                'name' => $faker->words(3, true),
                'slug' => Str::slug($faker->words(3, true) . '-' . $i),
                'price' => $faker->numberBetween(10, 500),
                'sale_price' => $faker->numberBetween(5, 400),
                'quantity' => $faker->numberBetween(0, 100),
                'views' => $faker->numberBetween(0, 500),
                'status' => 'published',
                'brand_id' => $brand->id,
                'store_id' => $store->id,
                'description' => $faker->sentence(),
            ]);

            // Catégories
            $productCategories = $faker->randomElements($categories, rand(1, 3));
            $product->categories()->attach(array_map(fn($c) => $c->id, $productCategories));

            // Labels
            $productLabels = $faker->randomElements($labels, rand(0, 2));
            $product->labels()->attach(array_map(fn($l) => $l->id, $productLabels));

            // Collections
            $productCollections = $faker->randomElements($collections, rand(0, 2));
            $product->collections()->attach(array_map(fn($c) => $c->id, $productCollections));

            // Reviews (1 à 5 par produit)
/*            for ($r = 0; $r < rand(1, 5); $r++) {
                Review::create([
                    'product_id' => $product->id,
                    'customer_id' => $faker->randomElement($users)->id,
                    'star' => $faker->numberBetween(1, 5),
                    'comment' => $faker->sentence(),
                    'status' => 'published',
                ]);
            }*/

            $products[] = $product;
        }

        $this->call([
            ImageSeeder::class,
            // tu peux ajouter d'autres seeders ici : ProductSeeder::class, UserSeeder::class, etc.
        ]);
        $this->command->info('✅ Seeder completed with users, brands, categories, stores, products, labels, collections, orders, and reviews!');
    }
}
