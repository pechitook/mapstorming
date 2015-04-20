<?php namespace Mapstorming\Commands;

use Mapstorming\CartoDB;
use Mapstorming\City;
use Mapstorming\Config\Config;
use Mapstorming\GeojsonHandler;
use Mapstorming\Project;
use Mapstorming\ValidableQuestion\ValidableQuestion;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;


/**
 * @property Project project
 * @property Config config
 * @property mixed datasetsDirectory
 * @property City city
 * @property CartoDB cartodb
 * @property GeojsonHandler geojson
 */
class ProcessDatasets extends MapstormingCommand {

    protected $data = [];
    protected $allCities;

    protected function configure() {
        $this->geojson = new GeojsonHandler();
        $this->config = new Config();
        $this->cartodb = new CartoDB();
        $this->project = new Project();
        $this->city = new City();
        $this->datasetsDirectory = __DIR__ . '/../../../tilemill_project/datasets/';

        $this->setName('up')
            ->setDescription('Export mbtiles from geojson files and upload them to Mapbox');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        // Style the output
        $output = $this->setOutputFormat($output);
        // Style the output
        $output = $this->setOutputFormat($output);
        // Helper to ask questions through Console
        $helper = $this->getHelper('question');

        $this->displayLoadedCities($output);

        // Check if a datasets folder exists for each city. If not, create it
        $this->checkCitiesFolderExists($this->allCities);

        // Does the user want to use an already loaded city?
        //if ($this->useLoadedCity($input, $output, $helper)) {
        //    $city = $this->selectCity($input, $output, $helper);
        //} else {
        //    $this->getApplication()->find('add-city')->run($input, $output);
        //    $city = $this->selectCity($input, $output, $helper);
        //}

        // Make user select one of the cities
        $city = $this->selectCity($input, $output, $helper);


        if ($this->askToDownloadFromCartoDB($input, $output, $helper)) {
            $output->writeln("\n<say>Downloading data from <high>CartoDB</high>...</say>");
            $this->downloadGeojsonsFromCartoDB($city);
        }

        $datasetsDir = $this->datasetsDirectory . $city->bkID . '/';


        if ($geojsons = $this->getGeojsonsInDirectory($datasetsDir)) {
            // Lets grab all datasets from the city's folder
            $layers = $this->getLayersFromFileName($geojsons);
            // Ask to process all layers
            if (!$this->askToProcessAllLayers($layers, $input, $output, $helper, $datasetsDir)) {
                // Don't process them all, add manaully.
                $layers = $this->addDatasets($input, $output, $helper, $layers);
            }
        } else {
            $output->writeln("<error>There are no geojson files to process in 'tilemill_project/datasets/{$city->bkID}' \nPlease add them and try again</error>");

            return false;
        }

        // Upload to S3?
        $question = new ConfirmationQuestion("\n<ask>Do you want to <high>upload</high> the resulting mbtiles to production? (yes/no): </ask>", false);
        $upload = $helper->ask($input, $output, $question);

        foreach ($layers as $layer) {

            $properties = $this->geojson->getProperties($layer, $city);

            $this->project->create($city, $layer, $properties);

            if ($upload) {
                $command = $this->getApplication()->find('export');
                $arguments = array(
                    'command'  => 'export',
                    '--upload' => true,
                );
                $input = new ArrayInput($arguments);

                return $command->run($input, $output);
            }

            $this->getApplication()->find('export')->run($input, $output);
        }

        return;
    }

    /**
     * @param OutputInterface $output
     */
    protected function displayLoadedCities(OutputInterface $output) {
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
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param $helper
     * @return boolean
     */
    protected function useLoadedCity(InputInterface $input, OutputInterface $output, $helper) {
        $question = new ConfirmationQuestion("\n<ask>Do you want to use one of the above? (yes/no):</ask> ", false);

        return $helper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $helper
     */
    protected function selectCity(InputInterface $input, OutputInterface $output, $helper) {
        $question = new ValidableQuestion("\n<say>Which city you want to work with? </say>", ['required']);
        // set autocompleter values to city names, including all lowercase
        $question->setAutocompleterValues(array_merge($this->city->getNames($this->allCities), $this->city->getNames($this->allCities, true)));
        $cityName = $helper->ask($input, $output, $question);

        $output->writeln("\n<say>*** <high>" . ucwords($cityName) . "</high> it is! ***</say>");

        return $this->city->getByName($this->allCities, $cityName);

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $helper
     * @param $allLayers
     * @return array
     */
    protected function addDatasets(InputInterface $input, OutputInterface $output, $helper, $allLayers) {
        $layers = [];
        while ($res != 'done') {
            $question = new Question('<info>Which datasets do you want to process? </info>');
            $question->setAutocompleterValues($allLayers);
            $res = $helper->ask($input, $output, $question);

            // show list
            if ($res == 'done') {
                return $layers;
            }

            $layers[] = $res;
            $output->writeln('<say>Added ' . $res . ' to the processing list.</say>');
        }
    }

    /**
     * @param $directory
     * @return array
     */
    protected function getGeojsonsInDirectory($directory) {
        $files = array_diff(scandir($directory), ['..', '.', '.DS_Store']);
        $geojsons = [];
        foreach ($files as $file) {
            if (preg_match('|.geojson|', $file)) $geojsons[] = $file;
        }

        return $geojsons;
    }

    private function getLayersFromFileName($geojsons) {
        $layers = [];
        foreach ($geojsons as $file) {
            $layers[] = $this->getDatasetName($file);
        }

        return $layers;
    }

    private function askToProcessAllLayers($layers, Input $input, Output $output, Helper $helper) {
        $output->writeln("\n<say>These are all the datasets ready to be processed:</say>");
        foreach ($layers as $layer) {
            $output->writeln("- $layer");
        }
        $question = new ConfirmationQuestion("\n<ask>Do you want to process all of them? (yes/no): </ask>", false);

        return $helper->ask($input, $output, $question);
    }

    private function askToDownloadFromCartoDB(Input $input, Output $output, Helper $helper) {
        $question = new ConfirmationQuestion("\n<ask>Do you want to download the data form <high>CartoDB</high>? (yes/no): </ask>", false);

        return $helper->ask($input, $output, $question);
    }

    private function checkCitiesFolderExists($allCities) {
        $files = array_diff(scandir($this->datasetsDirectory), ['..', '.', '.DS_Store']);
        foreach ($allCities as $city) {
            if (!in_array($city->bkID, $files)) {
                @mkdir($this->datasetsDirectory . $city->bkID);
                @mkdir($this->datasetsDirectory . $city->bkID . '/mbtiles');
            }
        }
    }

    private function downloadGeojsonsFromCartoDB($city) {
        foreach ($this->config->layers as $layer) {
            $geojson = $this->cartodb->downloadDataset($city->bkID, $layer);
            if ($geojson) {
                $this->geojson->saveDataset($layer, $city, json_encode($geojson));
            }
        }
    }

}
