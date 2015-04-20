<?php
/**
 * Created by PhpStorm.
 * User: pablochiappetti
 * Date: 11/24/14
 * Time: 10:51
 */

namespace Mapstorming;


class GeojsonHandler {

    public function saveDataset($dataset, $city, $data) {
        $fullpath = $this->getGeojsonFullpath($dataset, $city);
        file_put_contents($fullpath, $data);

        return $fullpath;
    }

    /**
     * @param $dataset
     * @param $city
     * @return array
     */
    public function getGeojsonFullpath($dataset, $city) {
        $filename = $this->getGeojsonFilename($dataset, $city);
        $fullpath = __DIR__ . '/../../tilemill_project/datasets/' . $city->bkID . '/' . $filename;

        return $fullpath;
    }

    /**
     * @param $dataset
     * @param $city
     * @return string
     */
    public function getGeojsonFilename($dataset, $city) {
        $filename = 'bk_' . $city->bkID . '_' . $dataset . '.geojson';

        return $filename;
    }

    public function getProperties($dataset, $city) {
        $geojson = json_decode(file_get_contents($this->getGeojsonFullpath($dataset, $city)));
        return $this->getPropertiesFromGeojson($geojson);
    }

    /**
     * @param $geojson
     * @return array
     */
    public function getPropertiesFromGeojson($geojson)
    {
        if (!$features = $geojson->features){
            $features = $geojson['features'];
        }
        $properties = [];
        foreach ($features as $item) {
            if (!$props = $item->properties){
                $props = $item['properties'];
            }
            foreach ($props as $name => $value) {
                $properties[] = $name;
            }
        }

        return array_unique($properties);
    }
} 