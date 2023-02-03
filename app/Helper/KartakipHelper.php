<?php

namespace App\Helper;

use App\Models\Vehicle;
use App\Service\Kartakip;
use Illuminate\Support\Collection;

class KartakipHelper
{
    public static function updateVehicle(): Collection
    {
        $kartakip = new Kartakip();
        $vehicles = collect($kartakip->getVehicles());
        $vehicles->keyBy('plate')->each(function ($vehicle, $plate) {
            $vehicle['url'] = 'https://mobilkarizleme.ankara.bel.tr/live/'.$plate.'/index.m3u8';
            Vehicle::query()->updateOrCreate([
                'plate' => $plate
            ], $vehicle);
        });
        return $vehicles;
    }
}
