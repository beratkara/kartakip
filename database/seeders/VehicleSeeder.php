<?php

namespace Database\Seeders;

use App\Helper\KartakipHelper;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        KartakipHelper::updateVehicle();
    }
}
