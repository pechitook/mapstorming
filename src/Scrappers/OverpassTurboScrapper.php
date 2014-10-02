<?php

namespace Mapstorming\Scrappers;


use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use Mapstorming\City;
use Mapstorming\ValidableQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class OverpassturboScrapper implements ScrapperInterface {

    public function __construct()
    {
        $this->city = new City();
        $this->helper = new QuestionHelper();
        $this->client = new \GuzzleHttp\Client();
        CacheSubscriber::attach($this->client);
    }

    public function scrap($cityId, $dataset, $input, $output)
    {
        $city = $this->city->getById($cityId);

        $queries = [
            'cycleway' => '<osm-script output="json"><query type="relation" into="cr"><bbox-query {{bbox}}/><has-kv k="route" v="bicycle"/></query><query type="way" into="cw1"><bbox-query {{bbox}}/><has-kv k="highway" v="cycleway"/></query><query type="way" into="cw2"><bbox-query {{bbox}}/><has-kv k="highway" v="path"/><has-kv k="bicycle" v="designated"/></query><union into="cw"><item set="cw1"/><item set="cw2"/></union><union><item set="cr"/><recurse from="cr" type="down"/><item set="cw"/><recurse from="cw" type="down"/></union><print mode="body" order="quadtile"/></osm-script>',
            'bike_shop' => '<osm-script output="json" timeout="25"><union><query type="node"><has-kv k="shop" v="bicycle"/><bbox-query {{bbox}}/></query><query type="way"><has-kv k="shop" v="bicycle"/><bbox-query {{bbox}}/></query><query type="relation"><has-kv k="shop" v="bicycle"/><bbox-query {{bbox}}/></query></union><print mode="body"/><recurse type="down"/><print mode="skeleton" order="quadtile"/></osm-script>',
        ];

        $q = $queries[$dataset];

        $url = 'http://overpass-turbo.eu/?Q='.urlencode($q).'&C='.$city->mapConfig->centerLat.';'.$city->mapConfig->centerLng.';13&R';

        $output->writeln("\n<say>Ok, I'm gonna need some <high>human help</high> for this one...</say>");
        $output->writeln("<say>Please <high>Cmd+click</high> on this link <ask>➡</ask> <ask>$url</ask></say>");

        $question = new ConfirmationQuestion("\n<say>Press <high>enter</high> when you see the results on the map...</say>");
        $this->helper->ask($input, $output, $question);

        $output->writeln("\n<say>Now click on <high>Export</high> and then select <high>save GeoJSON to gist</high>...</say>");

        $question = new ValidableQuestion("<say>Paste gist's URL <ask>➡</ask>   </say>", ["required"]);
        $gistURL = $this->helper->ask($input, $output, $question);

        $data = $this->processGist($gistURL);
        return $data;
    }

    private function processGist($gistURL)
    {
        $gistId = end(explode('/', parse_url($gistURL)['path']));
        $url = 'https://api.github.com/gists/'.$gistId;
        $pag = $this->client->get($url);
        $obj = $pag->json();
        $geojson = $obj['files']['overpass.geojson']['content'];

        return $geojson;
    }
}