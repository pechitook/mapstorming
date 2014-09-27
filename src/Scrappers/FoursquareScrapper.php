<?php

namespace Mapstorming\Scrappers;


use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use Mapstorming\City;

class FoursquareScrapper implements ScrapperInterface {

    public function __construct()
    {
        $this->city = new City();
        $this->client = new \GuzzleHttp\Client();
        CacheSubscriber::attach($this->client);
    }

    public function scrap($cityId, $dataset)
    {
        $city = $this->city->getById($cityId);

        $queries = [
            'free_wifi' => 'wifi'
        ];

        $q = $queries[$dataset];

        //$ll = number_format($city->mapConfig->centerLat, 1) . ',' . number_format($city->mapConfig->centerLng, 1);
        $near = $city->name.', '.$city->country->name;

        for ($i = 0; $i < 400; $i += 50) {
            $offset = $i;
            $url = 'https://api.foursquare.com/v2/venues/explore?client_id=NPW3MDLV4KNVNXZ1BSGL20K01RWOFOIMA3MF2BNJLOBCJQIW&client_secret=PTZEYUDBJ45JN422UAHI0PHAHONPWBLPNF115ER4DWTSNPPI&v=' . date('Ymd') . '&near=' . $near . '&q=' . $q . '&limit=50&offset=' . $offset;
            $pag = $this->client->get($url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; rv:11.0) Gecko/20100101 Firefox/11.0',
                ]
            ]);
            $obj = $pag->json();
            if (!$spots) $spots = [];
            $spots['type'] = 'FeatureCollection';
            foreach ($obj['response']['groups'][0]['items'] as $item) {
                $spot = new \StdClass();
                $spot->type = 'Feature';
                $spot->geometry = (object) [
                    'type'        => 'Point',
                    'coordinates' => [$item['venue']['location']['lng'], $item['venue']['location']['lat']]
                ];
                $spot->properties = (object) [
                    'name'              => $item['venue']['name'],
                    'phone'             => $item['venue']['contact']['phone'],
                    'address'           => $item['venue']['location']['address'],
                    'crossStreet'       => $item['venue']['crossStreet'],
                    'category'          => $item['venue']['categories']['name'],
                    'url'               => $item['venue']['url'],
                    'price'             => $item['venue']['price']['message'],
                    'foursquare_rating' => $item['venue']['rating'],
                    'foursquare_id'     => $item['venue']['id'],
                ];
                $spots['features'][] = $spot;
            }
        }

        return json_encode($spots);
    }
}