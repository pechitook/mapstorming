<?php namespace Mapstorming\Commands;

use Mapstorming\City;
use Mapstorming\Config;
use Mapstorming\ValidableQuestion;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;


/**
 * @property Project project
 * @property Config config
 */
class AddCity extends MapstormingCommand {

    protected $data = [];

    protected function configure()
    {
        $this->config = new Config();
        $this->city = new City();

        $this->setName('add-city')
            ->setDescription('Add a new City')
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
        $output->writeln("\n<say>Cool! Let's add a new city to Bikestorming :)</say>");
        $city = new \StdClass();

        // City Name
        $question = new ValidableQuestion("<ask><high>Name</high> of the city?: </ask>", ["required"]);
        $city->name = $helper->ask($input, $output, $question);

        // Country
        $question = new ValidableQuestion("<ask>Which <high>Country</high> is it in?: </ask>", ["required"]);
        $city->country = new \StdClass();
        $city->country->name = $helper->ask($input, $output, $question);

        // Bikestorming ID
        $question = new ValidableQuestion("<ask>Bikestorming ID? (ba, bgt, crml, etc...): </ask>", ["required"]);
        $city->bikestormingId = $helper->ask($input, $output, $question);

        // Boundaries
        $output->writeln("\n***");
        $output->writeln("<say>Great. Now, let's set some boundaries:</say>");
        $output->writeln("<ask>Please go to <high>http://boundingbox.klokantech.com/</high> and copy the boundaries in <high>geojson</high> format</ask>");
        $output->writeln("<ask>It should start with <high>[[[</high> followed by many, many numbers.</ask>");
        $helper->ask($input, $output, new ConfirmationQuestion("\n<ask>Press enter when you're ready. I'll be right here <high>:)</high> </ask>"));
        $output->writeln("\n<say>Got it? Amazing! Please paste it down here <high>↓</high></say>");

        $question = new ValidableQuestion("<ask>Weird code: </ask>", ["required"]);
        $boundaries = $helper->ask($input, $output, $question);
        $city = $this->addCityBoundaries($city, $boundaries);

        // Center
        $output->writeln("\n***");
        $output->writeln("<say>What about the center of the city?</say>");
        $output->writeln("<ask>Please go to <high>http://geojson.io/#map=12/{$city->bounds->SWLat}/{$city->bounds->SWLng}</high> and center the city on the map. \n\nWhen you're done, <high>copy the url</high> and paste it here <high>↓</high></ask>");

        $centerUrl = $helper->ask($input, $output, new Question("<ask>URL: </ask>"));
        $city = $this->setCenterFromUrl($city, $centerUrl);


        // Display information, wait for confirmation
        $output->writeln("\n<say>Please review <high>{$city->name}'s</high> information:</say>");
        $output->writeln("<ask>Name:</ask> {$city->name}");
        $output->writeln("<ask>Country:</ask> {$city->country->name}");
        $output->writeln("<ask>Bikestorming ID:</ask> {$city->bikestormingId}");

        $confirm = $helper->ask($input, $output, new ConfirmationQuestion("\n<ask>Is this correct? (yes/no): </ask>", true));

        // Add the city
        if ($confirm){
            $output->writeln("\n<say>Saving to BK's Database...</say>");
            if (!$this->city->add($city)){
                $output->writeln("<error>There was an error trying to save to the DB. Are you online?</error>");
                die();
            }

            $this->createDatasetsDirectory($city->bikestormingId);
            $output->writeln("<high>Whooho, {$city->name} is now part of Bikestorming!</high>");
        }else{
            $output->writeln("\n<error>We need to start over then :(</error>");
        }
    }

    private function addCityBoundaries($city, $boundaries)
    {
        $points = json_decode($boundaries)[0];
        $city->bounds = new \StdClass();
        $city->bounds->SWLng = (double)$points[0][0];
        $city->bounds->SWLat = (double)$points[0][1];
        $city->bounds->NELng = (double)$points[2][0];
        $city->bounds->NELat = (double)$points[2][1];

        return $city;

    }

    private function setCenterFromUrl($city, $url)
    {
        $parsed = explode('/', parse_url($url)["fragment"]);

        $city->mapConfig = new \StdClass();
        $city->mapConfig->centerZoom = (int)str_replace('map=', '', $parsed[0]);
        $city->mapConfig->centerLat = (double) $parsed[1];
        $city->mapConfig->centerLng = (double) $parsed[2];
        $city->minZoom = 11;
        $city->maxZoom = 17;

        return $city;
    }

    private function createDatasetsDirectory($bikestormingId)
    {
        mkdir(__DIR__.'/../../tilemill_project/datasets/'.$bikestormingId);
    }

}