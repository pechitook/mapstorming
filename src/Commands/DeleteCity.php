<?php namespace Mapstorming\Commands;

use Mapstorming\City;
use Mapstorming\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;


/**
 * @property Project project
 * @property Config config
 * @property City city
 */
class DeleteCity extends MapstormingCommand {
    protected $data = [];

    protected function configure()
    {
        $this->config = new Config();
        $this->city = new City();

        $this->setName('delete-city')
            ->setDescription('Delete a City');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Style the output
        $output = $this->setOutputFormat($output);
        // Helper to ask questions through Console
        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(
            "\n<say>Please select which city you want to <high>delete</high></say>",
            $this->city->getAllBikestormingIDs()
        );

        $question->setErrorMessage('Please use the number in [brackets] to refer to the city.');

        $bkid = $helper->ask($input, $output, $question);

        $question = new Question("\n<say>Password, please?: </say>");
        $question->setHidden(true);

        $password = $helper->ask($input, $output, $question);

        if (md5($password) == 'f22ec0566c58b521e7f512d0bb55e67e') return $this->city->deleteByBikestormingID($bkid);

        $output->writeln("<error>Wrong password :/</error>");

    }
}