<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shops')->insert([
            [
                'owner_id' => 1,
                'name' => '店名',
                'information' => 'お店の情報',
                'filename' => '1497719850_65b230bae574b.jpg',
                'is_selling' => true
            ],
            [
                'owner_id' => 2,
                'name' => '店名',
                'information' => 'お店の情報',
                'filename' => '1507850927_65b5c0b0c81bf.jpg',
                'is_selling' => true
            ],
        ]);
    }
}
