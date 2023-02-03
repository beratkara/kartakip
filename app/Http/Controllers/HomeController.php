<?php

namespace App\Http\Controllers;

use App\Helper\KartakipHelper;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __invoke()
    {
        if (!Cache::has('updateVehicle')) {
            KartakipHelper::updateVehicle();
            Cache::put('updateVehicle', Carbon::now()->addMinute());
        }

        $vehicles = Vehicle::query()->select(['plate', 'url'])->limit(30)->inRandomOrder()->get();

        return view('index', compact('vehicles'));
    }
}
