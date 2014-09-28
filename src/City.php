<?php namespace Mapstorming;


use Model\ArticleRepository;
use MongoClient;

class City {

    protected $all;

    public function __construct(){
        $this->all = json_decode(file_get_contents(__DIR__.'/../data/cities.json'));
    }

    public function add($city){
        $db = new DB();
        if ($db->insert('cities', json_encode($city))) return true;
        return false;
    }

    public function getAll(){
        $db = new DB();
        return $db->getAll('cities');
    }

    public function getByName($cities, $name)
    {
        $cities = $this->isCitiesSet($cities);
        foreach ($cities as $city){
            if (strtolower($city->name) == strtolower($name)){
                return $city;
            }
        }
    }
    public function getById($cityId, $cities = null)
    {
        $cities = $this->isCitiesSet($cities);
        foreach ($cities as $city){
            if ($city->bikestormingId == $cityId){
                return $city;
            }
        }
    }

    public function deleteByBikestormingID($id, $cities = null)
    {
        var_dump('TODO: Delete city');
    }

    public function getAllBikestormingIDs()
    {
        $arr = [];
        foreach ($this->all as $city) {
            $arr[] = $city->bikestormingId;
        }
        return $arr;
    }

    public function getNames($allCities, $lowercase = false)
    {
        $arr = [];
        foreach ($allCities as $city) {
            $arr[] = $lowercase ? strtolower($city->name) : $city->name;
        }
        return $arr;
    }

    public function addLayer($dataset, $city)
    {
        $db = new DB();
        $data = new \StdClass();
        $data->name = $dataset;
        $db->addItem(json_encode($data), ['bikestormingId', $city->bikestormingId], 'cities', 'layers');
    }

    /**
     * @param $cities
     * @return mixed
     */
    protected function isCitiesSet($cities)
    {
        if (!$cities) {
            $cities = $this->getAll();
            return $cities;
        }
        return $cities;
    }
}