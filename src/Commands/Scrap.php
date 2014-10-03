<?php
/**
 * Created by PhpStorm.
 * User: pablochiappetti
 * Date: 9/25/14
 * Time: 10:26
 */

namespace Mapstorming\Commands;

use Mapstorming\City;
use Mapstorming\Config;
use Mapstorming\DB;
use Mapstorming\Scrappers\ScrapperFactory;
use Mapstorming\ValidableQuestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * @property ScrapperFactory scrapper
 * @property City city
 * @property Config config
 */
class Scrap extends MapstormingCommand {
    protected $allCities;

    public function configure()
    {
        $this->config = new Config();
        $this->city = new City();

        $this->setName('get')
            ->setDescription("Let's go get some datasets!")
            ->addArgument('dataset', InputArgument::OPTIONAL, 'Which dataset do we want?')
            ->addArgument('cityId', InputArgument::OPTIONAL, 'The city we want the data from')
            ->addOption('source', 's', InputOption::VALUE_OPTIONAL, 'Source of the data (foursquare, google, OSM)');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output = $this->setOutputFormat($output);
        $helper = $this->getHelper('question');

        // Get cities info from DB
        $output->writeln("<say>Loading cities from DB...</say>");
        $this->allCities = $this->city->getAll();

        $dataset = $input->getArgument('dataset');
        $cityId = $input->getArgument('cityId');
        $source = $input->getOption('source');

        if (! $dataset || ! $cityId || ! $source) {
            // Get the City ID
            $question = new ValidableQuestion("<ask>Which city do you want to get datasets to?: </ask>", ['required']);
            // set autocompleter values to city names, including all lowercase
            $question->setAutocompleterValues(array_merge($this->city->getNames($this->allCities), $this->city->getNames($this->allCities, true)));
            $cityName = $helper->ask($input, $output, $question);
            $cityId = $this->city->getByName($this->allCities, $cityName)->bikestormingId;

            // Get the Dataset
            $question = new ValidableQuestion("<ask>Which dataset do you want to get?: </ask>", ['required']);
            $question->setAutocompleterValues($this->config->layers);
            $dataset = $helper->ask($input, $output, $question);

            // Get the source
            $sources = $this->config->scrapSourcesFor($dataset);
            if (!$sources){
                $output->writeln("<error>We don't have scrappers for that dataset, YET</error>");
                die();
            }
            $question = new ChoiceQuestion("<ask>These are the sources we can get $dataset from. Please select one: </ask>", $sources);
            $source = $helper->ask($input, $output, $question);

        }

        $output->writeln("\n<say>Getting <high>$dataset</high> from <high>$source</high> for <high>".strtoupper($cityId)."</high>... Please stand by.</say>");
        $scrapper = ScrapperFactory::getInstance($source);
        $data = $scrapper->scrap($cityId, $dataset, $input, $output);

        $countItems = count(json_decode($data)->features);
        $savedFile = $this->saveDataset($dataset, $this->city->getById($cityId), $data);

        $output->writeln("<ask>$countItems items saved to $savedFile");
    }

    private function saveDataset($dataset, $city, $data)
    {
        $filename = 'bk'.$city->bikestormingId.'_'.$dataset.'.geojson';
        file_put_contents(__DIR__.'/../../tilemill_project/datasets/'.$city->bikestormingId.'/'.$filename, $data);
        return $filename;
    }

}