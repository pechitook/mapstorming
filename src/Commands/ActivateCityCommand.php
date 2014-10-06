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
class ActivateCityCommand extends MapstormingCommand {

    protected $allCities;

    protected function configure()
    {
        $this->config = new Config();
        $this->city = new City();

        $this->setName('activate')
            ->setDescription('Activate a city in Bikestorming app')
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
        $output->writeln("\n<say>Let me grab all the inactive cities from the <high>DB</high>...</say>");


        // Get cities from DB
        $this->allCities = $this->city->getAll();
        $output->writeln("\n<say>Ok, here you have them <high>↓ </high></say>");

        // Create array with only inactive cities and display them
        $unactiveCities = [];
        foreach($this->allCities as $city){
            if (!$city->active) {
                $output->writeln("- {$city->name}");
                $unactiveCities[] = $city;
            }
        }


        // Get the City
        $question = new ValidableQuestion("<ask>\nWhich city do you want to activate?: </ask>", ['required']);
        $question->setAutocompleterValues(array_merge($this->city->getNames($unactiveCities), $this->city->getNames($unactiveCities, true)));
        $cityName = $helper->ask($input, $output, $question);

        $city = $this->city->getByName($this->allCities, $cityName);

        $this->city->activate($city);
        $this->city->setOrder($city, 100);
    }

}