<?php namespace Mapstorming;


class City {

    protected $all;

    public function __construct(){
        $this->all = json_decode(file_get_contents(__DIR__.'/../data/cities.json'));
    }

    public function add($city){
        array_push($this->all, $city);
        $this->save();
    }

    public function save(){
        file_put_contents(__DIR__.'/../data/cities.json', json_encode($this->all));
    }

    public function getAll(){
        return $this->all;
    }

    public function getAllNames($lowercase = false){
        $arr = [];
        foreach ($this->all as $city) {
            $arr[] = $lowercase ? strtolower($city->name) : $city->name;
        }
        return $arr;
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
    public function getById($cityId)
    {
        foreach ($this->all as $city){
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
}