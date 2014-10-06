<?php
/**
 * Created by PhpStorm.
 * User: pablochiappetti
 * Date: 10/6/14
 * Time: 15:51
 */

namespace Mapstorming\Commands;


use Mapstorming\ValidableQuestion;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MainMenu extends MapstormingCommand{

    protected $menuOptions = [
        'open',
        'get',
        'up',
        'activate',
    ];

    public function configure(){
        $this->setName('menu')->setDescription('Main menu of the application. Default command.');
    }

    public function execute(InputInterface $input, OutputInterface $output){
        $output = $this->setOutputFormat($output);
        $helper = $this->getHelper('question');

        // Show welcome message
        $this->displayWelcomeMessage($output);

        // Show menu option
        $this->displayMenuOptions($output);

        $question = new ValidableQuestion("\n<say>Please select one <high>command</high>: </say>", ['required']);
        foreach($this->menuOptions as $option){
            $this->menuOptions[] = strtoupper($option);
        }
        $question->setAutocompleterValues($this->menuOptions);
        $command = strtolower($helper->ask($input, $output, $question));

        return $this->getApplication()->find($command)->run($input, $output);
    }

    /**
     * @param OutputInterface $output
     */
    protected function displayWelcomeMessage(OutputInterface $output)
    {
        $output->writeln("\n<high>        <star>*</star>     <star>*</star>    <star>*</star>     /\__/\  <star>*</star>    <bk>---</bk>    <star>*</star>     </high>");
        $output->writeln("<high>           <star>*</star>            /      \    <bk>/     \ </bk>         </high>");
        $output->writeln("<high>                <star>*</star>   <star>*</star>  |  -  -  |  <bk>|   B   |</bk>   <star>*</star>     </high>");
        $output->writeln("<high>         <star>*</star>   __________| \     /|  <bk>|   K   |</bk>         </high>");
        $output->writeln("<high>           /              \ T / |   <bk>\     /</bk>         </high>");
        $output->writeln("<high>         /                      |  <star>*</star>  <bk>---</bk>     </high>");
        $output->writeln("<high>        |  ||     |    |       /             <star>*</star>     </high>");
        $output->writeln("<high>        |  ||    /______\     / | <star>*</star>     <star>*</star>     </high>");
        $output->writeln("<high>        |  | \  |  /     \   /  |     </high>");
        $output->writeln("<high>         \/   | |\ \      | | \ \     </high>");
        $output->writeln("<high>              | | \ \     | |  \ \     </high>");
        $output->writeln("<high>              | |  \ \    | |   \ \     </high>");
        $output->writeln("<high>              '''   '''   '''    '''        </high>");
        $output->writeln("<say>\nWelcome to <high>Mapstorming!</high></say>");
    }

    private function displayMenuOptions(OutputInterface $output)
    {
        $output->writeln("<say>These commands are available for you</say>\n");
        foreach($this->menuOptions as $option){
            $output->writeln("<ask>- ".strtoupper($option)."</ask>");
        }
    }
}