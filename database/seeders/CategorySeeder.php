<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Ready To Cook'],
            ['name' => 'Beverages'],
            ['name' => 'Snacks'],
            ['name' => 'Instant Products'],
            ['name' => 'Personal Care'],
            ['name' => 'Dairy Products'],
            ['name' => 'Frozen Products'],
            ['name' => 'Powered Products'],
            ['name' => 'Sauces & Condiments'],
            ['name' => 'Juices & Concentrates'],
        ];
        DB::table('categories')->insert($categories);
    }
}
