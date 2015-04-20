<?php namespace Mapstorming\Commands;


use Mapstorming\CartoDB;
use Mapstorming\City;
use Mapstorming\Config\Config;
use Mapstorming\GeojsonHandler;
use Mapstorming\Project;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mapstorming\ValidableQuestion\ValidableQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * @property City city
 * @property GeojsonHandler geojson
 * @property Config config
 * @property CartoDB cartodb
 * @property Project project
 */
class Transform extends MapstormingCommand {

    protected $data = [];
    protected $allCities;
    protected $emptyFields = [];
    protected $mapped = [];
    protected $finalFeatures = [];
    protected $sourceGeojson;
    protected $finalGeojson;
    protected $tableName;
    protected $input;
    protected $output;
    protected $helper;

    protected function configure()
    {
        $this->geojson = new GeojsonHandler();
        $this->config = new Config();
        $this->cartodb = new CartoDB();
        $this->project = new Project();
        $this->city = new City();
        $this->datasetsDirectory = __DIR__ . '/../../../tilemill_project/datasets/';
        $this->setName('transform')
            ->setDescription('Transform datasets from source to mbtiles')
            ->addArgument(
                'table',
                InputArgument::OPTIONAL,
                'Table name in CartoDB to be transformed'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Style the output
        $this->input = $input;
        $this->output = $this->setOutputFormat($output);
        // Helper to ask questions through Console
        $this->helper = $this->getHelper('question');

//        $this->displayLoadedCities($output);
        // Check if a datasets folder exists for each city. If not, create it
//        $this->checkCitiesFolderExists($this->allCities);
//        $city = $this->selectCity($input, $output, $helper);

        // get all datasets available for the city (source and main) from CartoDB
//        $allTables = $this->cartodb->getAllTableNames();
//        dd($allTables);

        // choose the one to transform
        if (!$this->tableName = strtolower($input->getArgument('table'))) {
            $question = new ValidableQuestion('Which CartoDB table do you want to work with?', ['required']);
            $this->tableName = strtolower($this->helper->ask($input, $output, $question));
        }

        // get the required fields from a template
        $dataset = $this->cartodb->getDatasetName($this->tableName);
        $allDatasets = $this->getAllDatasets();
        $requiredFields = $allDatasets->required_fields->{$dataset};
        $optionalFields = $allDatasets->optional_fields->{$dataset};

        // map those fields to columns in the dataset
        $this->sourceGeojson = $this->cartodb->downloadTable($this->tableName);
        $fields = $this->geojson->getPropertiesFromGeojson($this->sourceGeojson);
        $fields[] = "None of the above... create as empty field";
        // force None to be added as the last index number
        $fields = array_values($fields);

        $this->promptUserToMapFields($requiredFields, $fields);

        // ask whether more fields should be included (as optional)
        if ($this->userWantsToAddOptionalFields()){
            $fields = $this->geojson->getPropertiesFromGeojson($this->sourceGeojson);
            $fields[] = "None of the above... don't add it";
            // force None to be added as the last index number
            $fields = array_values($fields);
            foreach($optionalFields as $optionalField){
                $question = new ChoiceQuestion("Which of the following fields best matches <high>$optionalField</high>?", $fields);
                $mappable = $this->helper->ask($this->input, $this->output, $question);
                if ($mappable != "None of the above... don't add it") {
                    $this->mapped[$mappable] = $optionalField;
                }
            }
        }

        // save field names that where mapped for machine learning
        $this->mapFields();

        $master = $this->askIfShouldBeMaster();
        // export the dataset as bk_city_dataset_source_odp_transformed
        $this->output->writeln("<ask>Saving geojson file...</ask>");
        $this->createGeojson();

        // Upload to CartoDB
        $this->output->writeln("<ask>Uploading transformed dataset to CartoDB...</ask>");
        $filename = $this->saveGeojson($master);
        $this->printTransformersLogo();
        $this->output->writeln("<high>Done! The TRANSFORMED dataset lives here: https://bkx.cartodb.com/tables/{$filename}</high>");
    }

    protected function displayLoadedCities(OutputInterface $output)
    {
        $output->writeln("\n<say>Loading cities...</say>");
        // Actually call the remote DB to load the cities
        $this->allCities = $this->city->getAll();
        $output->writeln("<say>We have <high>" . count($this->allCities) . " cities</high> in our database:</say>");

        $citiesNames = $this->city->getNames($this->allCities);
        sort($citiesNames);

        foreach ($citiesNames as $name) {
            $output->writeln("- " . $name);
        }
    }

    /**
     * @param $requiredFields
     * @param $fields
     * @param $helper
     */
    protected function promptUserToMapFields($requiredFields, $fields)
    {
        foreach ($requiredFields as $requiredField) {
            $question = new ChoiceQuestion("Which of the following fields best matches <high>$requiredField</high>?", $fields);
            $mappable = $this->helper->ask($this->input, $this->output, $question);
            if ($mappable == 'None of the above... create as empty field') {
                $this->emptyFields[] = $requiredField;
            }
            $this->mapped[$mappable] = $requiredField;
        }
    }

    /**
     * @return void
     */
    protected function mapFields()
    {
        $newProperties = [];
        $sourceProperties = [];
        foreach ($this->sourceGeojson['features'] as &$item) {

            // get the properties that will map to the required ones
            foreach ($item['properties'] as $propertyName => $propertyValue) {
                // if the property was chosen to be mapped
                if (array_key_exists($propertyName, $this->mapped)) {
                    $newProperties[] = [$propertyName => $this->mapped[$propertyName]];
                } else {
                    $sourceProperties[] = [$propertyName => $propertyValue];
                }
            }

            // modify properties array in item
            $modified = [];
            foreach ($newProperties as $newProperty) {
                foreach ($newProperty as $key => $value) {
                    $item['properties'][$value] = $item['properties'][$key];
                    $modified[] = $value;
                    // if required field name is not the same as the mapped field name
                    if ($value != $key) {
                        // add the mapped field name to be renames as source_
                        $sourceProperties[] = [$key => $item['properties'][$key]];
                    }
                }
            }

            if ($this->emptyFields) {
                foreach ($this->emptyFields as $emptyField) {
                    $item['properties'][$emptyField] = "";
                }
            }

            // rename unused fields to source_
            foreach ($sourceProperties as $sourceProperty) {
                foreach ($sourceProperty as $key => $value) {
                    if (!in_array($key, $modified)) {
                        unset($item['properties'][$key]);
                    }
//                    $item['properties']['source_' . $key] = $value;
                }
            }
            $this->finalFeatures[] = $item;

        }
    }

    protected function createGeojson()
    {
        $this->finalGeojson = new \StdClass();
        $this->finalGeojson->type = "FeatureCollection";
        $this->finalGeojson->features = $this->finalFeatures;
        $this->finalGeoJson = json_encode($this->finalGeojson);
    }

    protected function saveGeojson($master)
    {
        if ($master) {
            $filename = $this->transformTableNameToMaster() . '.geojson';
        }else{
            $filename = $this->tableName . '_transformed.geojson';
        }
        $fullpath = __DIR__ . '/../../../tmp/' . $filename;
        file_put_contents($fullpath, $this->finalGeoJson);
        $this->cartodb->uploadGeoJSON($fullpath);
        return str_replace('.geojson', '', $filename);
    }

    protected function printTransformersLogo()
    {
        $this->output->writeln("\n");
        $this->output->writeln("───────────▄▄▄▄▄▄▄▄▄───────────");
        $this->output->writeln("────────▄█████████████▄────────");
        $this->output->writeln("█████──█████████████████──█████");
        $this->output->writeln("▐████▌─▀███▄───────▄███▀─▐████▌");
        $this->output->writeln("─█████▄──▀███▄───▄███▀──▄█████─");
        $this->output->writeln("─▐██▀███▄──▀███▄███▀──▄███▀██▌─");
        $this->output->writeln("──███▄▀███▄──▀███▀──▄███▀▄███──");
        $this->output->writeln("──▐█▄▀█▄▀███─▄─▀─▄─███▀▄█▀▄█▌──");
        $this->output->writeln("───███▄▀█▄██─██▄██─██▄█▀▄███───");
        $this->output->writeln("────▀███▄▀██─█████─██▀▄███▀────");
        $this->output->writeln("───█▄─▀█████─█████─█████▀─▄█───");
        $this->output->writeln("───███────────███────────███───");
        $this->output->writeln("───███▄────▄█─███─█▄────▄███───");
        $this->output->writeln("───█████─▄███─███─███▄─█████───");
        $this->output->writeln("───█████─████─███─████─█████───");
        $this->output->writeln("───█████─████─███─████─█████───");
        $this->output->writeln("───█████─████─███─████─█████───");
        $this->output->writeln("───█████─████▄▄▄▄▄████─█████───");
        $this->output->writeln("────▀███─█████████████─███▀────");
        $this->output->writeln("──────▀█─███─▄▄▄▄▄─███─█▀──────");
        $this->output->writeln("─────────▀█▌▐█████▌▐█▀─────────");
        $this->output->writeln("────────────███████────────────");
        $this->output->writeln("\n");
    }

    /**
     * @param $question
     * @return mixed
     */
    protected function userWantsToAddOptionalFields()
    {
        $question = new ConfirmationQuestion("<ask>Do you want to add any aditional values? (y/n): </ask>", false);
        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function checkCitiesFolderExists($allCities)
    {
        $files = array_diff(scandir($this->datasetsDirectory), ['..', '.', '.DS_Store']);
        foreach ($allCities as $city) {
            if (!in_array($city->bkID, $files)) {
                @mkdir($this->datasetsDirectory . $city->bkID);
                @mkdir($this->datasetsDirectory . $city->bkID . '/mbtiles');
            }
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $helper
     */
    protected function selectCity(InputInterface $input, OutputInterface $output, $helper)
    {
        $question = new ValidableQuestion("\n<say>Which city you want to work with? </say>", ['required']);
        // set autocompleter values to city names, including all lowercase
        $question->setAutocompleterValues(array_merge($this->city->getNames($this->allCities), $this->city->getNames($this->allCities, true)));
        $cityName = $helper->ask($input, $output, $question);

        $output->writeln("\n<say>*** <high>" . ucwords($cityName) . "</high> it is! ***</say>");

        return $this->city->getByName($this->allCities, $cityName);

    }

    private function askIfShouldBeMaster()
    {
        $question = new ConfirmationQuestion("<ask>Do you want this to become the master dataset?</ask>", false);
        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function transformTableNameToMaster()
    {
        $city = $this->cartodb->getCityName($this->tableName);
        $dataset = $this->cartodb->getDatasetName($this->tableName);
        return "bk_{$city}_{$dataset}_master";
    }
} 