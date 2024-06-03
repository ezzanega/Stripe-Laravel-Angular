<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Plan::create(['name' => 'First Plan', 'price' => 20]);
        Plan::create(['name' => 'Second Plan', 'price' => 300]);
        Plan::create(['name' => 'Premium Plan', 'price' => 400]);
    }
}
