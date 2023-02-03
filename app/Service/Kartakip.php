<?php

namespace App\Service;

use App\Helper\GuzzleHelper;
use App\Helper\JsonHelper;
use Illuminate\Support\Collection;

class Kartakip
{
    private $service;
    public function __construct()
    {
        $this->service = new GuzzleHelper("https://kartakip.ankara.com.tr/");

        $this->service->setHeaders([
            'Referer' => 'https://www.beratkara.com/',
            'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:82.0) Gecko/20100101 Firefox/82.0',
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Language' => 'tr-TR,tr;q=0.8,en-US;q=0.5,en;q=0.3',
        ]);

        $this->service->init();

        $this->getVehicles();
    }

    public function getVehicles()
    {
        /** @var Collection $response */
        $response = $this->service->get("/ajax/vehicles/camera");

        if ($response->has("exception"))
        {
            die("Dönüş Yok ( HTML ) -> ". $response->get('exception'));
        }
        else
        {
            return JsonHelper::decodeArray($response->get('content'));
        }
    }

}

