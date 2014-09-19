<?php namespace Mapstorming\Commands;

use Mapstorming\City;
use Mapstorming\Config;
use Mapstorming\Project;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;


/**
 * @property Project project
 * @property Config config
 */
class ProcessDatasets extends Command {

    protected $data = [];

	protected function configure()
    {
    	$this->config = new Config();
        $this->project = new Project();
        $this->city = new City();

        $this->setName('process')
             ->setDescription('Process GeoJSON datasets with Tilemill')
//             ->addArgument(
//                 'directory',
//                 InputArgument::OPTIONAL,
//                 'Full path to the geojsons you want to process'
//             )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Style the output
        $output = $this->setOutputFormat($output);
        // Helper to ask questions through Console
        $helper = $this->getHelper('question');

        $this->displayWelcomeMessage($output);
        $this->displayLoadedCities($output);

        // Does the user want to use an already loaded city?
        if ($this->useLoadedCity($input, $output, $helper)) {
            $city = $this->selectCity($input, $output, $helper);
		}else{
            $this->addNewCity($output);
        }

        $datasetsDir = 'tilemill_project/datasets/'.$city->bikestormingId.'/';

        if ($geojsons = $this->getGeojsonsInDirectory($datasetsDir)) {
            $layers = $this->getLayersFromFileName($geojsons);
            if (! $this->askToProcessAllLayers($layers, $input, $output, $helper, $datasetsDir)){
                $layers = $this->addDatasets($input, $output, $helper);
            }
        }else{
            $output->writeln("<error>There are no geojson files to process in $datasetsDir - Please add them and try again</error>");
            return false;
        }

        $this->project->create($city, $layers);

        $output->writeln("\n<say>tilemill_project/template/project.mml properly configured.</say>");
        $output->writeln("<say>Now, simply run <high>grunt exportMbtiles</high> to generate them,\n or <high>grunt uploadMbtiles</high> to directly upload them to Mapbox</say>");

    }

    /**
     * @param OutputInterface $output
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    protected function setOutputFormat(OutputInterface $output)
    {
        $style = new OutputFormatterStyle('cyan', 'black', array('bold'));
        $output->getFormatter()->setStyle('high', $style);
        $style = new OutputFormatterStyle('blue', 'black', array('bold'));
        $output->getFormatter()->setStyle('say', $style);
        $style = new OutputFormatterStyle('green', 'black', array('bold'));
        $output->getFormatter()->setStyle('ask', $style);
        return $output;
    }

    /**
     * @param OutputInterface $output
     */
    protected function displayWelcomeMessage(OutputInterface $output)
    {
        $output->writeln("<high>                                _                 _                      </high>");
        $output->writeln("<high>   ___ ___    _____ ___ ___ ___| |_ ___ ___ _____|_|___ ___    ___ ___   </high>");
        $output->writeln("<high>  |___|___|  |     | .'| . |_ -|  _| . |  _|     | |   | . |  |___|___|  </high>");
        $output->writeln("<high>             |_|_|_|__,|  _|___|_| |___|_| |_|_|_|_|_|_|_  |             </high>");
        $output->writeln("<high>                       |_|                             |___|             </high>");
        $output->writeln("");
        $output->writeln("<say>\nWelcome to <high>Mapstorming!</high> Let's open a city together, shall we?</say>");
    }

    /**
     * @param OutputInterface $output
     */
    protected function displayLoadedCities(OutputInterface $output)
    {
        $cities = $this->city->getAll();

        $output->writeln("\n<say>We have " . count($cities) . " cities in our database:</say>");

        foreach ($cities as $city) {
            $output->writeln("- " . $city->name);
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param $helper
     * @return boolean
     */
    protected function useLoadedCity(InputInterface $input, OutputInterface $output, $helper)
    {
        $question = new ConfirmationQuestion("\n<ask>Do you want to use one of the above? (yes/no):</ask> ", false);
        return $helper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $helper
     */
    protected function selectCity(InputInterface $input, OutputInterface $output, $helper)
    {
        $question = new ChoiceQuestion(
            "\n<say>Please select which city you want to work with</say>",
            $this->city->getAllNames()
        );

        $question->setErrorMessage('Please use the number in [brackets] to refer to the city.');

        $cityName = $helper->ask($input, $output, $question);

        $output->writeln("<say><high>$cityName</high> it is!</say>");

        return $this->getCityByName($cityName);

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $helper
     * @return array
     */
    protected function addDatasets(InputInterface $input, OutputInterface $output, $helper)
    {
        $layers = [];
        while ($res != 'done') {
            $question = new Question('<info>Which datasets do you want to process? </info>', null);
            $res = $helper->ask($input, $output, $question);

            // show list
            if ($res == 'done') {
                return $layers;
            }

            // show list
            if ($res == 'list') {
                foreach ($this->config->layers() as $layer) {
                    $output->writeln("- $layer");
                }
                continue;
            }

            // if it is not a layer, show help
            if (!in_array($res, $this->config->layers())) {
                $output->writeln('<error>'.$res.' is not a valid option.</error>');
                $output->writeln('Type <info>list</info> to see which datasets are available.');
                $output->writeln('Alternatively, type <info>done</info> when you are ready to move on.');
                continue;
            }

            $layers[] = $res;
            $output->writeln('<say>Added '.$res.' to the processing list.</say>');
        }
    }

    /**
     * @param $directory
     * @return array
     */
    protected function getGeojsonsInDirectory($directory)
    {
        $files = array_diff(scandir($directory), ['..', '.', '.DS_Store']);
        $geojsons = [];
        foreach($files as $file){
            if (preg_match('|.geojson|', $file)) $geojsons[] = $file;
        }
        return $geojsons;
    }

    private function getLayersFromFileName($geojsons)
    {
        $layers = [];
        foreach ($geojsons as $file){
            preg_match('|[a-z]*_([a-zA-Z_]*).geojson|', $file, $res);
            $layers[] = $res[1];
        }
        return $layers;
    }

    private function askToProcessAllLayers($layers, Input $input,Output $output, Helper $helper, $directory)
    {
        $output->writeln("\n<say>This are all the datasets ready to be processed on <high>".$directory."</high>:</say>");
        foreach($layers as $layer){
            $output->writeln("- $layer");
        }
        $question = new ConfirmationQuestion("\n<ask>Do you want to process all of them? (yes/no): </ask>", false);
        return $helper->ask($input, $output, $question);
    }

    private function addNewCity(Output $output)
    {
        $output->writeln("<say>Ok, let's add a new city then!</say>");
    }

    private function getCityByName($cityName)
    {
        $cities = $this->city->getAll();
        foreach ($cities as $city){
            if ($city->name == $cityName){
                return $city;
            }
        }
    }
}