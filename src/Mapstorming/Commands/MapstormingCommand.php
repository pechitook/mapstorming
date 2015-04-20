<?php
/**
 * Created by PhpStorm.
 * User: pablochiappetti
 * Date: 9/20/14
 * Time: 12:39
 */

namespace Mapstorming\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\Output;

class MapstormingCommand extends Command {

    /**
     * @param \Mapstorming\Commands\OutputInterface|\Symfony\Component\Console\Output\Output $output
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function setOutputFormat(Output $output) {
        $style = new OutputFormatterStyle('cyan', 'black', array('bold'));
        $output->getFormatter()->setStyle('high', $style);
        $style = new OutputFormatterStyle('blue', 'black', array('bold'));
        $output->getFormatter()->setStyle('say', $style);
        $style = new OutputFormatterStyle('green', 'black', array('bold'));
        $output->getFormatter()->setStyle('ask', $style);
        $style = new OutputFormatterStyle('white', 'black', array('bold'));
        $output->getFormatter()->setStyle('star', $style);
        $style = new OutputFormatterStyle('red', 'black', array('bold'));
        $output->getFormatter()->setStyle('bk', $style);
        $style = new OutputFormatterStyle('cyan', 'black', array('bold', 'blink'));
        $output->getFormatter()->setStyle('blink', $style);

        return $output;
    }

    protected function getDatasetName($layer) {
        $layer = basename($layer, '.geojson');
        preg_match('|bk_[a-zA-Z]+_([0-9a-zA-Z_]*)|', $layer, $res);
        return $res[1];
    }

    protected function getCityId($layer) {
        $layer = basename($layer, '.geojson');
        preg_match('|bk_([a-zA-Z]*)_|', $layer, $res);
        return $res[1];
    }

    protected function getFilename($city, $dataset){
        return 'bk_'.$city.'_'.$dataset;
    }

    public function getAllDatasets(){
        return json_decode(file_get_contents(__DIR__.'/../../../data/datasets.json'));
    }
} 