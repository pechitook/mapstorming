<?php namespace Mapstorming;

class Config {

    public function __construct()
    {
        $this->fullpath = getenv('FULLPATH');
        $this->layers = [
            'bike_shop',
            'safe_parking',
            'air_pump',
            'free_wifi',
            'wifi_venues',
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

        $this->layerRender = [
            'bike_shop' => 'point',
            'safe_parking' => 'point',
            'air_pump' => 'point',
            'free_wifi' => 'point',
            'wifi_venues' => 'point',
            'cyclestreet' => 'linestring',
            'cycleway' => 'linestring',
            'weekend_cycleway' => 'linestring',
            'cycleroute' => 'linestring',
            'cobblestone' => 'linestring',
            'heavy_transit' => 'linestring',
            'cyclefriendly_street' => 'linestring',
            'heights' => 'point',
            'racks' => 'point',
            'ferry' => 'point',
            'subway' => 'point',
            'train' => 'point',
            'rewards' => 'point',
            'public_bike' => 'point',  
        ];

        $this->scrapSources = [
            'wifi_venues' => [
                'foursquare'
            ],
            'cycleway' => [
                'overpassturbo'
            ],
            'bike_shop' => [
                'overpassturbo'
            ],
            'racks' => [
                'overpassturbo'
            ],
            'cobblestone' => [
                'overpassturbo'
            ],
        ];
    }

    public function scrapSourcesFor($dataset)
    {
        return $this->scrapSources[$dataset];
    }
}