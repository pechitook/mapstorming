<?php namespace Mapstorming\Config;

class Config {

    public function __construct()
    {
        $this->fullpath = getenv('FULLPATH');
        $this->layers = [
            "air_pumps",
            "bike_rental",
            "bike_shelters",
            "bikeshops",
            "cycleway_network",
            "cobblestone",
            "edu",
            "free_wifi",
            "hotels",
            "massive_parkings",
            "planned_cycleway",
            "public_bikes",
            "public_benches",
            "racks",
            "rewards",
            "rides_points",
            "rides_trails",
            "safe_parkings",
            "stolen_bikes",
            "subway_stations",
            "subway_tracks",
            "train_tracks",
            "train_stations",
            "weekend_cycleway",
            "wifi_venue"
        ];

        $this->layerRender = [
            'bikeshops' => 'point',
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
            'public_bikes' => 'point',
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