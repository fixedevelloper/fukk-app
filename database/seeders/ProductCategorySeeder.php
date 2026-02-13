<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert(
            array(
                0 =>
                    array(
                        'name' => 'Electronique',
                        'slug' => 'electronique',
                        'status' => 'published',
                        'parent_id' => 0,
                    ),
                1 =>
                    array(
                        'name' => 'Decor & Meubles',
                        'slug' => 'decor_meubles',
                        'status' => 'published',
                        'parent_id' => 0,
                    ),
                2 =>
                    array(
                        'name' => 'Sante et Beaute',
                        'slug' => 'sante_et_beaute',
                        'status' => 'published',
                        'parent_id' => 0,
                    ),
                3 =>
                    array(
                        'name' => 'Mode',
                        'slug' => 'mode',
                        'status' => 'published',
                        'parent_id' => 0,
                    ),
                4 =>
                    array(
                        'name' => 'Ordinateurs et Accessoires',
                        'slug' => 'ordinateurs_accessoires',
                        'status' => 'published',
                        'parent_id' => 0,
                    ),
                5 =>
                    array(
                        'name' => 'Cave',
                        'slug' => 'cave',
                        'status' => 'published',
                        'parent_id' => 0,
                    ),
            )
        );
    }
}
