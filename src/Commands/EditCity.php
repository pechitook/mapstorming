<?php namespace Mapstorming\Commands;

use Mapstorming\City;
use Mapstorming\Config;
use Mapstorming\ValidableQuestion;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;


/**
 * @property Config config
 * @property City city
 */
class EditCity extends MapstormingCommand {

    protected $allCities;

    protected function configure()
    {
        $this->config = new Config();
        $this->city = new City();

        $this->setName('edit-city')
            ->setDescription('Edit a city')
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
        // Welcome message
        $output->writeln("\n<say>Hello again! Let me grab the cities from the <high>DB</high>...</say>");

        // Get cities from DB
        $this->allCities = $this->city->getAll();
        $output->writeln("<high>Done!</high>");


        // Get the City
        $question = new ChoiceQuestion("<ask>\nWhich city do you want to edit?: </ask>", $this->city->getNames($this->allCities));
        $cityName = $helper->ask($input, $output, $question);
        $city = $this->city->getByName($this->allCities, $cityName);

        var_dump($city);
    }

}