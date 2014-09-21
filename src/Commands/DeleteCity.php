<?php namespace Mapstorming\Commands;

use Mapstorming\City;
use Mapstorming\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;


/**
 * @property Project project
 * @property Config config
 */
class DeleteCity extends MapstormingCommand {
    protected $data = [];

    protected function configure()
    {
        $this->config = new Config();
        $this->city = new City();

        $this->setName('delete-city')
            ->setDescription('Delete a City')
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

        $question = new ChoiceQuestion(
            "\n<say>Please select which city you want to <high>delete</high></say>",
            $this->city->getAllNames()
        );

        $question->setErrorMessage('Please use the number in [brackets] to refer to the city.');

        $cityName = $helper->ask($input, $output, $question);

        $output->writeln("\n<say>*** <high>$cityName</high> it is! ***</say>");

        return $this->city->getByName($cityName);

    }
}