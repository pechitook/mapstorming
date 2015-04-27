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
		$this->client = new Client([
			'base_url' => 'http://api.bikestorming.com',
			'defaults' => [
				'auth' => [getenv('BK_USER'), getenv('BK_PASS')]
			]
		]);
	}

	public function add($city)
	{
		$response = $this->client->post('/cities', ['json' => $city]);
		if ($response->getStatusCode() == 200) return true;

		return false;
	}

	public function getAll()
	{
		try
		{
			$response = $this->client->get('/cities');
		}
		catch(\Exception $e)
		{
			die("\033[1;31mThere was an error trying to connect to Mother Ship.\nDo you have internet?\n");
		}

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
			if ($city->bkID == $cityId)
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
		$this->client->post('/cities/'.$city.'/layers', [
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
		try
		{
			$response = $this->client->post('/cities/'.$city->bkID.'/activate');
		}
		catch(\Exception $e)
		{
			die("\033[1;31mThere was an error trying to connect to Mother Ship.\nDo you have internet?\n");
		}
		// MONGO
		// $db = $this->getDB();
		// $db->changeValue('active', true, 'cities', ['bkID' => $city->bkID]);
	}

	public function setOrder($city, $order)
	{
		// $db = $this->getDB();
		// $db->changeValue('order', $order, 'cities', ['bkID' => $city->bkID]);
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
