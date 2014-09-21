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

    public function getAllNames(){
        $arr = [];
        foreach ($this->all as $city) {
            $arr[] = $city->name;
        }
        return $arr;
    }

    public function getByName($cityName)
    {
        foreach ($this->all as $city){
            if ($city->name == $cityName){
                return $city;
            }
        }
    }
}