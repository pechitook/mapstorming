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

    public function getByName($cityName, $lowercase = false)
    {
        foreach ($this->all as $city){
            $search = $lowercase ? ucwords($cityName) : $cityName;
            if ($city->name == $search){
                return $city;
            }
        }
    }
    public function getById($cityId, $cities = null)
    {
        if (!$cities){
            $cities = $this->getAll();
        }
        foreach ($cities as $city){
            if ($city->bikestormingId == $cityId){
                return $city;
            }
        }
    }

    public function deleteByBikestormingID($id)
    {
        foreach ($this->all as $key => $city){
            if ($city->bikestormingId == $id){
                unset($this->all[$key]);
            }
        }
        $this->save();
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
}