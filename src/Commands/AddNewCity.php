<?php namespace Mapstorming\Commands;

use Mapstorming\City;
use Mapstorming\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;


/**
 * @property Project project
 * @property Config config
 */
class AddNewCity extends Command {

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

        $output->writeln("<say>Cool! Let's add a new city to Bikestorming :)</say>");
        $output->writeln("<say>Please answer this:</say>");
        $city = new \StdClass();

        // TODO: Make numbers be stored as double;
        $city->name = $helper->ask($input, $output, new Question("<ask>Name of the City?: </ask>"));
        $city->country = $helper->ask($input, $output, new Question("<ask>Country?: </ask>"));
        $city->bikestormingId = $helper->ask($input, $output, new Question("<ask>Bikestorming ID? (ba, bgt, crml, etc...): </ask>"));
        $city->SWLng = (double)$helper->ask($input, $output, new Question("<ask>SWLng: </ask>"));
        $city->SWLat = (double)$helper->ask($input, $output, new Question("<ask>SWLat: </ask>"));
        $city->NELng = (double)$helper->ask($input, $output, new Question("<ask>NELng: </ask>"));
        $city->NELat = (double)$helper->ask($input, $output, new Question("<ask>NELat: </ask>"));
        $city->centerLat = (double)$helper->ask($input, $output, new Question("<ask>centerLat: </ask>"));
        $city->centerLng = (double)$helper->ask($input, $output, new Question("<ask>centerLng: </ask>"));
        $city->centerZoom = (int)$helper->ask($input, $output, new Question("<ask>centerZoom: </ask>"));
        $city->minZoom = (int)$helper->ask($input, $output, new Question("<ask>minZoom: </ask>"));
        $city->maxZoom = (int)$helper->ask($input, $output, new Question("<ask>maxZoom: </ask>"));

        $output->writeln("\n<say>Please review <high>{$city->name}'s</high> information:</say>");
        var_dump($city);

        $confirm = $helper->ask($input, $output, new ConfirmationQuestion("<ask>Is this correct? (yes/no): </ask>", true));

        if ($confirm){
            $this->city->add($city);
            $output->writeln("City added.");
        }else{
            $output->writeln("Ok, let's start over.");
        }
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

}