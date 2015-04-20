<?php
/**
 * Created by PhpStorm.
 * User: pablochiappetti
 * Date: 11/21/14
 * Time: 18:14
 */

namespace Mapstorming;

use Guzzle\Http\Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\DefaultCacheStorage;

class CartoDB {

    public function __construct() {
        $this->guzzle = new Client();
        $this->guzzlehttp = new HttpClient();
    }

    public function uploadGeoJSON($fullpath) {
        $apikey = getenv('CARTODB_APIKEY');
        exec("curl -s -F file=@$fullpath \"https://bkx.cartodb.com/api/v1/imports/?api_key=$apikey\"");
    }

    public function downloadDataset($city, $dataset) {
        try {
            $res = $this->guzzlehttp->get('http://bkx.cartodb.com/api/v2/sql?format=geojson&q=SELECT%20*%20FROM%20bk_' . $city . '_' . $dataset);
        } catch (RequestException $e) {
            if ($e->getCode() == 400) return false;
        }
        return $res->json();
    }

    public function getAllTableNames(){
        $cachePlugin = new CachePlugin([
            'storage' => new DefaultCacheStorage(
                new DoctrineCacheAdapter(
                    new FilesystemCache(__DIR__.'/../../tmp')
                )
            )
        ]);

        $this->guzzle->addSubscriber($cachePlugin);

        $html = $this->guzzle->get('https://bkx.cartodb.com/datasets/page/1')->send();
        preg_match_all('/https:\/\/bkx.cartodb.com\/datasets\/page\/([0-9]+)/', $html, $res);
        foreach($res[1] as $page){
            $pages[] = $page;
        }
        $pages = array_unique($pages);
        $datasets = [];
        foreach($pages as $page){
            $html = $this->guzzle->get('https://bkx.cartodb.com/datasets/page/'.$page)->send();
            $datasets = array_merge($datasets, $this->findDatasetsInHtml($html));
        }

        return $datasets;
    }

    private function findDatasetsInHtml($html)
    {
        preg_match_all('/bk_([a-zA-Z]+)*_([0-9a-zA-Z_]*)_(source|master|wip)_?([0-9a-zA-Z]*)?/', $html, $res);
        if (!$res) return false;
        return array_unique($res[0]);
    }

    public function getDatasetName($tableName)
    {
        preg_match('/bk_([a-zA-Z]+)*_([0-9a-zA-Z_]*)_(source|master|wip)_?([0-9a-zA-Z]*)?/', $tableName, $res);
        return $res[2];
    }

    public function getCityName($tableName)
    {
        preg_match('/bk_([a-zA-Z]+)*_([0-9a-zA-Z_]*)_(source|master|wip)_?([0-9a-zA-Z]*)?/', $tableName, $res);
        return $res[1];
    }

    public function getDatasetType($tableName)
    {
        preg_match('/bk_([a-zA-Z]+)*_([0-9a-zA-Z_]*)_(source|master|wip)_?([0-9a-zA-Z]*)?/', $tableName, $res);
        return $res[3];
    }

    public function getDatasetSource($tableName)
    {
        preg_match('/bk_([a-zA-Z]+)*_([0-9a-zA-Z_]*)_(source|master|wip)_?([0-9a-zA-Z]*)?/', $tableName, $res);
        return $res[4];
    }

    public function downloadTable($tableName)
    {
        try {
            $res = $this->guzzlehttp->get('http://bkx.cartodb.com/api/v2/sql?format=geojson&q=SELECT%20*%20FROM%20'.$tableName);
        } catch (RequestException $e) {
            if ($e->getCode() == 400) return false;
        }
        return $res->json();
    }
} 