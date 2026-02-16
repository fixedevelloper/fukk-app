<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Zone;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | DOUALA
        |--------------------------------------------------------------------------
        */

        $douala = City::firstOrCreate(
            ['name' => 'Douala'],
            ['latitude' => 4.0511, 'longitude' => 9.7679]
        );

        $doualaZones = [
            ['name' => 'Akwa', 'latitude' => 4.0516, 'longitude' => 9.7679],
            ['name' => 'Bonapriso', 'latitude' => 4.0483, 'longitude' => 9.7066],
            ['name' => 'Bonamoussadi', 'latitude' => 4.0935, 'longitude' => 9.7482],
            ['name' => 'Makepe', 'latitude' => 4.0845, 'longitude' => 9.7413],
            ['name' => 'Logpom', 'latitude' => 4.0975, 'longitude' => 9.7574],
        ];

        foreach ($doualaZones as $zone) {
            Zone::updateOrCreate(
                ['name' => $zone['name'], 'city_id' => $douala->id],
                $zone
            );
        }

        /*
        |--------------------------------------------------------------------------
        | YAOUNDE
        |--------------------------------------------------------------------------
        */

        $yaounde = City::firstOrCreate(
            ['name' => 'Yaoundé'],
            ['latitude' => 3.8480, 'longitude' => 11.5021]
        );

        $yaoundeZones = [
            ['name' => 'Bastos', 'latitude' => 3.8667, 'longitude' => 11.5167],
            ['name' => 'Mvan', 'latitude' => 3.8186, 'longitude' => 11.5084],
            ['name' => 'Emana', 'latitude' => 3.8900, 'longitude' => 11.4900],
            ['name' => 'Mokolo', 'latitude' => 3.8660, 'longitude' => 11.5160],
            ['name' => 'Odza', 'latitude' => 3.7915, 'longitude' => 11.5510],
        ];

        foreach ($yaoundeZones as $zone) {
            Zone::updateOrCreate(
                ['name' => $zone['name'], 'city_id' => $yaounde->id],
                $zone
            );
        }

        /*
        |--------------------------------------------------------------------------
        | BAFOUSSAM
        |--------------------------------------------------------------------------
        */

        $bafoussam = City::firstOrCreate(
            ['name' => 'Bafoussam'],
            ['latitude' => 5.4778, 'longitude' => 10.4170]
        );

        Zone::updateOrCreate(
            ['name' => 'Centre Ville', 'city_id' => $bafoussam->id],
            ['latitude' => 5.4781, 'longitude' => 10.4169]
        );

        /*
        |--------------------------------------------------------------------------
        | GAROUA
        |--------------------------------------------------------------------------
        */

        $garoua = City::firstOrCreate(
            ['name' => 'Garoua'],
            ['latitude' => 9.3014, 'longitude' => 13.3977]
        );

        Zone::updateOrCreate(
            ['name' => 'Poumpoumré', 'city_id' => $garoua->id],
            ['latitude' => 9.3030, 'longitude' => 13.4000]
        );

        /*
        |--------------------------------------------------------------------------
        | BAMENDA
        |--------------------------------------------------------------------------
        */

        $bamenda = City::firstOrCreate(
            ['name' => 'Bamenda'],
            ['latitude' => 5.9597, 'longitude' => 10.1459]
        );

        Zone::updateOrCreate(
            ['name' => 'Commercial Avenue', 'city_id' => $bamenda->id],
            ['latitude' => 5.9605, 'longitude' => 10.1450]
        );
    }
}
