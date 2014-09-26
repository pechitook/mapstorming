<?php namespace Mapstorming;

class Config {

    public function __construct(){
        $this->fullpath = '/Users/pablochiappetti/Documents/Bikestorming/mapstorming/';
        $this->layers = [
            'bike_shop',
            'safe_parking',
            'air_pump',
            'free_wifi',
            'wifi_cafe',
            'cyclestreet',
            'cycleway',
            'weekend_cycleway',
            'cycleroute',
            'cobblestone',
            'heavy_transit',
            'cyclefriendly_street',
            'heights',
            'racks',
            'ferry',
            'subway',
            'train',
            'rewards',
            'public_bike',
        ];
        $this->scrapSources = [
            'free_wifi' => [
                'foursquare'
            ],
            'bike_shop' => [
                'google'
            ],
        ];
    }

    public function scrapSourcesFor($dataset)
    {
        return $this->scrapSources[$dataset];
    }
}