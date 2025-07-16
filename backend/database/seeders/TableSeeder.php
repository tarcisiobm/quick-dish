<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('tables')->insert([
            ['name' => 'Mesa 1', 'capacity' => 2, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mesa 2', 'capacity' => 4, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mesa 3', 'capacity' => 4, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mesa VIP', 'capacity' => 6, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Barra 1', 'capacity' => 2, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}