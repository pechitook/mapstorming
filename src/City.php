<?php namespace Mapstorming;


use Model\ArticleRepository;
use MongoClient;

class City {

    protected $all;
    protected $db;
    protected $editableFields = [
        'localName',
        'localLanguage',
        'atlasImage' => ['bronco', 'chita', 'jorge', 'julia', 'placeholder'],
        'country.code',
        'bounds.SWLng',
        'bounds.SWLat',
        'bounds.NELng',
        'bounds.NELat',
        'mapConfig.minZoom',
        'mapConfig.maxZoom',
        'mapConfig.centerLat',
        'mapConfig.centerLng',
        'mapConfig.centerZoom',
    ];

    public function __construct()
    {
        $this->all = json_decode(file_get_contents(__DIR__ . '/../data/cities.json'));
    }

    public function add($city)
    {
        $db = $this->getDB();
        if ($db->insert('cities', json_encode($city))) return true;

        return false;
    }

    public function getAll()
    {
        $db = $this->getDB();

        return $db->getAll('cities');
    }

    public function getByName($cities, $name)
    {
        $cities = $this->isCitiesSet($cities);
        foreach ($cities as $city) {
            if (strtolower($city->name) == strtolower($name)) {
                return $city;
            }
        }
    }

    public function getById($cityId, $cities = null)
    {
        $cities = $this->isCitiesSet($cities);
        foreach ($cities as $city) {
            if ($city->bikestormingId == $cityId) {
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
        $db = $this->getDB();
        $db->addLayer($dataset, ['bikestormingId', $city->bikestormingId], 'cities');
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

    public function activate($city)
    {
        $db = $this->getDB();
        $db->changeValue('active', true, 'cities', ['bikestormingId' => $city->bikestormingId]);
    }

    public function setOrder($city, $order)
    {
        $db = $this->getDB();
        $db->changeValue('order', $order, 'cities', ['bikestormingId' => $city->bikestormingId]);
    }

    private function getDB()
    {
        if (!$this->db){
            $this->db = new DB();
        }
        return $this->db;
    }
}