<?php namespace Mapstorming;


use GuzzleHttp\Client;
use Model\ArticleRepository;
use MongoClient;

class City
{

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
		$this->client = new Client();
	}

	public function add($city)
	{
		$response = $this->client->post('http://api.bikestorming.com/cities', ['json' => $city]);
		if ($response->getStatusCode() == 200) return true;

		return false;
	}

	public function getAll()
	{
		$response = $this->client->get('http://api.bikestorming.com/cities');

		return json_decode($response->getBody())->data;
	}

	public function getByName($cities, $name)
	{
		$cities = $this->isCitiesSet($cities);
		foreach ($cities as $city)
		{
			if (strtolower($city->name) == strtolower($name))
			{
				return $city;
			}
		}
	}

	public function getById($cityId, $cities = null)
	{
		$cities = $this->isCitiesSet($cities);
		foreach ($cities as $city)
		{
			if ($city->bk_id == $cityId)
			{
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
		foreach ($this->all as $city)
		{
			$arr[] = $city->bk_id;
		}

		return $arr;
	}

	public function getNames($allCities, $lowercase = false)
	{
		$arr = [];
		foreach ($allCities as $city)
		{
			$arr[] = $lowercase ? strtolower($city->name) : $city->name;
		}

		return $arr;
	}

	public function addLayer($dataset, $city)
	{
		$this->client->post('http://api.bikestorming.com/cities', [
			'json' => [
				'name' => $dataset,
				'download_url' => "https://s3.amazonaws.com/bk-mbtiles/bk_{$city}_{$dataset}.mbtiles",
			]
		]);
//		$db = $this->getDB();
//		$db->addLayer($dataset, ['bkID', $city->bkID], 'cities');
	}

	/**
	 * @param $cities
	 * @return mixed
	 */
	protected function isCitiesSet($cities)
	{
		if (!$cities)
		{
			$cities = $this->getAll();

			return $cities;
		}

		return $cities;
	}

	public function activate($city)
	{
		$db = $this->getDB();
		$db->changeValue('active', true, 'cities', ['bkID' => $city->bkID]);
	}

	public function setOrder($city, $order)
	{
		$db = $this->getDB();
		$db->changeValue('order', $order, 'cities', ['bkID' => $city->bkID]);
	}

	private function getDB()
	{
		if (!$this->db)
		{
			$this->db = new DB();
		}

		return $this->db;
	}
}