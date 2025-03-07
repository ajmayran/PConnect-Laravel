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
            ['name' => 'Personal Care'],
            ['name' => 'Dairy Products'],
            ['name' => 'Health & Hygiene'],
        ];
        DB::table('categories')->insert($categories);
    }
}
