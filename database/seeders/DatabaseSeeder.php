<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Status::create([   
            'name' => 'activo',
            'model' => 'All',
            'color_status' => '#fff'
        ]);

        Status::create([
            'name' => 'inactivo',
            'model' => 'All',
            'color_status' => '#fff'
        ]);
    }
}
