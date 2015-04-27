<?php
/**
 * Created by PhpStorm.
 * User: pablochiappetti
 * Date: 9/25/14
 * Time: 10:26
 */

namespace Mapstorming\Commands;

use Mapstorming\CartoDB;
use Mapstorming\City;
use Mapstorming\Config\Config;
use Mapstorming\DB;
use Mapstorming\GeojsonHandler;
use Mapstorming\Scrappers\ScrapperFactory;
use Mapstorming\ValidableQuestion\ValidableQuestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * @property ScrapperFactory scrapper
 * @property City city
 * @property Config config
 * @property CartoDB cartodb
 * @property GeojsonHandler geojson
 */
class Scrap extends MapstormingCommand {

    protected $allCities;

    public function configure() {
        $this->geojson = new GeojsonHandler();
        $this->config = new Config();
        $this->cartodb = new CartoDB();
        $this->city = new City();

        $this->setName('get')
            ->setDescription("Let's go get some datasets!")
            ->addArgument('dataset', InputArgument::OPTIONAL, 'Which dataset do we want?')
            ->addArgument('cityId', InputArgument::OPTIONAL, 'The city we want the data from')
            ->addOption('source', 's', InputOption::VALUE_OPTIONAL, 'Source of the data (foursquare, google, OSM)');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $output = $this->setOutputFormat($output);
        $helper = $this->getHelper('question');

        // Get cities info from DB
        $output->writeln("<say>Loading cities from DB...</say>");
        $this->allCities = $this->city->getAll();

        $dataset = $input->getArgument('dataset');
        $cityId = $input->getArgument('cityId');
        $source = $input->getOption('source');

        if (!$dataset || !$cityId || !$source) {
            // Get the City ID
            $question = new ValidableQuestion("<ask>Which city do you want to get datasets for?: </ask>", ['required']);
            // set autocompleter values to city names, including all lowercase
            $question->setAutocompleterValues(array_merge($this->city->getNames($this->allCities), $this->city->getNames($this->allCities, true)));
            $cityName = $helper->ask($input, $output, $question);
            $cityId = $this->city->getByName($this->allCities, $cityName)->bkID;

            // Get the Dataset
            $question = new ValidableQuestion("<ask>Which dataset do you want to get?: </ask>", ['required']);
            $question->setAutocompleterValues($this->config->layers);
            $dataset = $helper->ask($input, $output, $question);

            // Get the source
            $sources = $this->config->scrapSourcesFor($dataset);
            if (!$sources) {
                $output->writeln("<error>We don't have scrappers for that dataset, YET</error>");
                die();
            }
            $question = new ChoiceQuestion("<ask>These are the sources we can get $dataset from. Please select one: </ask>", $sources);
            $source = $helper->ask($input, $output, $question);

        }

        $output->writeln("\n<say>Getting <high>$dataset</high> from <high>$source</high> for <high>" . strtoupper($cityId) . "</high>... Please stand by.</say>");
        $scrapper = ScrapperFactory::getInstance($source);
        $data = $scrapper->scrap($cityId, $dataset, $input, $output);

        $countItems = count(json_decode($data)->features);
        $fullpath = $this->geojson->getGeojsonFullpath($dataset, $this->city->getById($cityId));
        $this->geojson->saveDataset($dataset, $this->city->getById($cityId), $data);
        $this->uploadToCartoDB($fullpath);
        $output->writeln("<ask>$countItems items saved to ".$this->getCartoDBUrl($fullpath, $cityId));
    }

    private function uploadToCartoDB($fullpath) {
        $this->cartodb->uploadGeoJSON($fullpath);
    }

	private function getCartoDBUrl($fullpath, $cityId)
	{
		$dataset = $this->getDatasetName($fullpath);
		return 'https://bkx.cartodb.com/tables/'.$this->getFilename($cityId, $dataset);
	}
}
