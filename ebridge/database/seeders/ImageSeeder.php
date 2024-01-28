<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('images')->insert([
            [
                'owner_id' => 1,
                'filename' => '1497719850_65b230bae574b.jpg',
                'title' => null
            ],
            [
                'owner_id' => 1,
                'filename' => '1497719850_65b230bae574b.jpg',
                'title' => null
            ],
            [
                'owner_id' => 1,
                'filename' => '2088970275_65b5c0b162244.jpg',
                'title' => null
            ],
            [
                'owner_id' => 1,
                'filename' => '1133765716_65b2300c93926.jpg',
                'title' => null
            ],
            [
                'owner_id' => 1,
                'filename' => '569780899_65b230bb7c938.jpg',
                'title' => null
            ],
            [
                'owner_id' => 1,
                'filename' => '1133765716_65b2300c93926.jpg',
                'title' => null
            ]]);
    }
}