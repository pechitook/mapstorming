<?php
// Cool Colors
$colorPregunta = "\033[1;34m";
$colorHighlight = "\033[1;32m";

// Initialize array where data will be stored
$data = [];

// All possible layers. TODO: Replace with API call,
$layers = array(
	'bike_shop',
	'safe_parking',
	'air_pump',
	'free_wifi',
	'wifi_cafe',
	'cyclestreet',
	'cycleway',
	'weekend_cycleway',
	'cycleroute',
	'cobblestone',
	'heavy_transit',
	'cyclefriendly_street',
	'heights',
	'racks',
	'ferry',
	'subway',
	'train',
	'rewards',
	'public_bike',
);
$layersToProcess = [];

// write some text to the screen
function write($str, $color="\033[1;36m"){
	echo "{$color}$str\033[1;30m";
}

// return what the user just typed
function saveInput() {
	$handle = fopen ("php://stdin","r");
	return trim(fgets($handle));
}

// a new line!
function newline()
{
	echo "\n";
}

// list of valid datasets
function listDatasets(){
	global $layers;
	echo "- ".join("\n- ", $layers)."\n";
}

// display an incorrect value
function incorrectValue($input){
	echo "\033[0;31mWrong value: $input $defaultColor \n";
}

// add a dataset to the geojson to mbtiles batch job
function addDataset($dataset)
{
	global $layersToProcess;
	global $colorPregunta;
	global $colorHighlight;
	global $layers;
	$layersToProcess[] = $dataset;
	write("Added {$colorHighlight}$dataset{$colorPregunta} to the processing list. Type {$colorHighlight}done{$colorPregunta} when you're done adding datasets :)", $colorPregunta);
	// remove the added dataset from the $layers array
	if(($key = array_search($dataset, $layers)) !== false) {
	    unset($layers[$key]);
	}
	newline();
}


// Begin collecting data to generate project.mml file
// write("***************\nWELCOME MESSAGE!\n***************", $colorPregunta);
// newline();
// newline();
// write("What city do you want to process?", $colorPregunta);
// newline();
// write("City name: ");
// $data['__City'] = saveInput();
// write("Country?: ");
// $data['__Country'] = saveInput();
// write("Bikestorming id? (ba, bgt, crml, mvd, etc.): ");
// $data['__BikestormingId'] = saveInput();
// newline();

// write("Cool! Let's begin with this city's bounds!", $colorPregunta);
// newline();
// write("South West Bound Longitude: ");
// $data['__SWLng'] = saveInput();

// write("South West Bound Latitude: ");
// $data['__SWLat'] = saveInput();

// write("North East Bound Longitude: ");
// $data['__NELng'] = saveInput();

// write("North East Bound Latitude: ");
// $data['__NELat'] = saveInput();

// newline();
// write("Awesome! Now let's center the city :)", $colorPregunta);
// newline();
// write("Center Latitude: ");
// $data['__CenterLat'] = saveInput();
// write("Center Longitude: ");
// $data['__CenterLng'] = saveInput();
// write("Initial zoom: ");
// $data['__CenterZoom'] = saveInput();

// newline();
// write("Great! Just a little more configuration...", $colorPregunta);
// newline();
// write("Min Zoom: ");
// $data['__MinZoom'] = saveInput();
// write("Max Zoom: ");
// $data['__MaxZoom'] = saveInput();

$data['__City'] = 'Bogotá';
$data['__Country'] = 'Colombia';
$data['__BikestormingId'] = 'bgt';
$data['__SWLng'] = -74.22981262207031;
$data['__SWLat'] = 4.45937341380256;
$data['__NELng'] = -73.98536682128906;
$data['__NELat'] = 4.852890820110573;
$data['__CenterLat'] = 4.6397504;
$data['__CenterLng'] = -74.0619917;
$data['__CenterZoom'] = 13;
$data['__MinZoom'] = 11;
$data['__MaxZoom'] = 17;


newline();
write("Almost done. Now, which datasets do you want to process? Type {$colorHighlight}list{$colorPregunta} to see which you can use!\nWhen you finish, type {$colorHighlight}done{$colorPregunta} to continue", $colorPregunta);
newline();
getDataset();

function getDataset()
{
	write("Dataset: ");
	$input = saveInput();
	if (preg_match('|done|i', $input)) return false;
	if ($input == 'list'){ listDatasets(); return getDataset(); };

	$dataset = processDatasetInput($input);
	if ($dataset) {
		addDataset($dataset);
	}else{
		incorrectValue($input);
	}

	return getDataset();

	// write("Do you want to add another dataset? (y/n): ");
	// $areMoreDatasets = saveInput();
	// if ($areMoreDatasets == 'y' || $areMoreDatasets == 'yes') return getDataset();
	// return false;
}
function processDatasetInput($input)
{
	global $layers;
	if (!in_array($input, $layers)){
		return false;
	}
	return $input;
}

write("Processing...");

$output = new StdClass();
$output->bounds = [
	$data['__SWLng'],
	$data['__SWLat'],
	$data['__NELng'],
	$data['__NELat']
];
$output->center = [
	$data['__CenterLng'],
	$data['__CenterLat'],
	$data['__CenterZoom']
];
$output->format = "png";
$output->interactivity = false;
$output->minzoom = $data['__MinZoom'];
$output->maxzoom = $data['__MaxZoom'];
$output->srs = "+proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0.0 +k=1.0 +units=m +nadgrids=@null +wktext +no_defs +over";
$output->Stylesheet = ['style.mss'];
$output->Layer = [];

foreach ($layersToProcess as $layer) {
	$l = new StdClass();
	$l->geometry = 'point';
	$l->extent = $output->bounds;
	// Must be the same as name, will be the mapbox id
	$l->id = 'bk'.$data['__BikestormingId'].'_'.$layer;
	$l->class = $layer;
	$l->Datasource = new StdClass();
	$l->Datasource->file = 'datasets/bk'.$data['__BikestormingId'].'_'.$layer.'.geojson';
	$l->Datasource->id = $layer;
	$l->Datasource->project = '';
	$l->Datasource->srs = '';
	$l->{'srs-name'} = 'autodetect';
	$l->srs = '';
	$l->advanced = new StdClass();
	// Must be the same as id, will be the mapbox id
	$l->name = 'bk'.$data['__BikestormingId'].'_'.$layer;
	array_push($output->Layer, $l);
}

$output->scale = 2;
$output->metatile = 2;
$output->_basemap = "";
$output->name = "";
$output->description = "";
$output->attribution = "";

newline();
file_put_contents('tilemill_project/template/project.mml', json_encode($output));

// var_dump($data);
newline();
write("Done!");

?>