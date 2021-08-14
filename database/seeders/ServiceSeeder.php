<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Service::upsert([
            ['name' => 'Шиномонтаж'],
            ['name' => 'Техосмотр'],
            ['name' => 'Балансировка'],
        ], ['name']);
    }
}
