<?php
namespace Mapstorming;


class Project {
    public function create($city, $layers){

        $output = new \StdClass();

        $output->bounds = [
            $city->SWLng,
            $city->SWLat,
            $city->NELng,
            $city->NELat
        ];
        $output->center = [
            $city->centerLng,
            $city->centerLat,
            $city->centerZoom
        ];
        $output->format = "png";
        $output->interactivity = false;
        $output->minzoom = $city->minZoom;
        $output->maxzoom = $city->maxZoom;
        $output->srs = "+proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0.0 +k=1.0 +units=m +nadgrids=@null +wktext +no_defs +over";
        $output->Stylesheet = ['style.mss'];
        $output->Layer = [];

        foreach ($layers as $layer) {
            $l = new \StdClass();
            $l->geometry = preg_match('|cycle|', $layer) ? 'linestring' : 'point';
            $l->extent = $output->bounds;
            $l->status = 'Off';
            // Must be the same as name, will be the mapbox id
            $l->id = 'bk'.$city->bikestormingId.'_'.$layer;
            $l->class = $layer;
            $l->Datasource = new \StdClass();
            $l->Datasource->file = 'datasets/'.$city->bikestormingId.'/bk'.$city->bikestormingId.'_'.$layer.'.geojson';
            $l->Datasource->id = $layer;
            $l->Datasource->project = '';
            $l->Datasource->srs = '';
            $l->{'srs-name'} = 'autodetect';
            $l->srs = '';
            $l->advanced = new \StdClass();
            // Must be the same as id, will be the mapbox id
            $l->name = 'bk'.$city->bikestormingId.'_'.$layer;
            array_push($output->Layer, $l);
        }

        $output->scale = 2;
        $output->metatile = 2;
        $output->_basemap = "";
        $output->name = "";
        $output->description = "";
        $output->attribution = "";

        $this->save($output);

    }

    public function getJSON(){
        return json_decode(file_get_contents(__DIR__.'/../tilemill_project/template/project.mml'));
    }

    public function save($project){
        file_put_contents(__DIR__.'/../tilemill_project/template/project.mml', json_encode($project));
    }
}