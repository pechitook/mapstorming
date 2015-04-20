<?php
/**
 * Created by PhpStorm.
 * User: pablochiappetti
 * Date: 9/23/14
 * Time: 16:50
 */

namespace Mapstorming\Commands;


use Geocoder\Exception\InvalidArgumentException;
use Mapstorming\City;
use Mapstorming\Config\Config;
use Mapstorming\Project;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property City city
 * @property Config config
 * @property mixed configTilemill
 * @property Project project
 */
class ExportMbtiles extends MapstormingCommand {

	protected $input;
	protected $output;
	protected $projectPath;

    protected function configure() {
        $this->config = new Config();
        $this->accessToken = getenv('TILEMILL_ACCESS_TOKEN');
        $this->city = new City();
        $this->project = new Project();
        $this->configTilemill = [
            'tileMillDocumentPath' => getenv('TILEMILL_PATH'),
            'syncAccount'          => 'bkx',
            'tileMillPath'         => '/Applications/TileMill.app/Contents/Resources/',
            'outputMBTiles'        => $this->config->fullpath . 'tilemill_project/mbtiles',
            'datasetPath'          => $this->config->fullpath . 'tilemill_project/datasets',
            'projectTemplate'      => $this->config->fullpath . 'tilemill_project/template',
            'layersTogethers'      => false,
            'prependName'          => '',
            'projectName'          => 'bk_export_bot',
            'upload'               => false,
        ];

        $this->setName('export')
            ->setDescription('Export Mbtiles')
            ->addOption(
                'upload',
                null,
                InputArgument::OPTIONAL,
                'Should the exported mbtiles be uploadede to Mapbox?',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
	    $this->input = $input;
        $this->projectPath = $this->configTilemill['tileMillDocumentPath'] . $this->configTilemill['projectName'] . '/';
        if (!$this->projectPath) throw new InvalidArgumentException;
        // Style the output
        $output = $this->setOutputFormat($output);
	    $this->output = $output;
        // Helper to ask questions through Console
        $helper = $this->getHelper('question');

        // Remove everything inside projectPath
        $this->emptyProjectPath();

        // Copy datasets inside projectPath.'datasets/'
        $this->copyDatasets();

        // Copy projectTemplate/* a projectPath
        $this->copyProjectData();

        // Remove everything inside outputMBTiles
        // $this->cleanMbtiles();

        $project = $this->project->getJSON();
        $upload = $input->getOption('upload');

        foreach ($project->Layer as $layer) {

            $project = $this->turnAllLayersExcept($project, $layer);
            $project->name = $layer->name;
            $this->project->save($project);
            $this->copyProjectData();

            $output->writeln("\n<say>Exporting <high>{$layer->name}</high></say>");
            $output->writeln("<ask>Please wait, this may take a while... ☕️   </ask>");
            $this->exportLayer($layer);

            if ($upload) {
                $this->uploadLayer($layer);
                $output->writeln("<say>Uploaded <high>{$layer->name}</high> successfully</say>");

                // Add layer to city's document on DB
                $output->writeln("<say>Updating <high>{$layer->name}</high> in API...</say>");
                $this->city->addLayer($this->getDatasetName($layer->name), $this->city->getById($this->getCityId($layer->name)));
                $output->writeln("<ask>Done!</ask>");
            }
        }

        $output->writeln("\n<say>VAMO ARRIBA! All layers have been <ask>successfully exported</ask></say>");
    }

    private function turnAllLayersExcept($project, $layer) {
        foreach ($project->Layer as $key => $l) {
            if ($l->name != $layer->name) {
                $l->status = 'Off';
            } else {
                $l->status = 'On';
            }
        }

        return $project;
    }

    private function exportLayer($layer) {

        $city = $this->getCityId($layer->name);
        $outputPath = $this->config->fullpath . "tilemill_project/datasets/$city/mbtiles";

        $config = $this->configTilemill;
        $commands[] = "cd " . $config['tileMillPath'];
        $commands[] = './index.js export --verbose=off ' . $config['projectName'] . ' ' . $outputPath . '/' . $layer->name . '.mbtiles --format=mbtiles';
        system(implode('&&', $commands));
    }

    private function uploadLayer($layer) {
	    $command = $this->getApplication()->find('upload');
	    $input = new ArrayInput(array(
		    'command' => 'upload',
		    'city'  => $this->getCityId($layer->name),
		    'dataset' => $this->getDatasetName($layer->name),
	    ));

	    return $command->run($input, $this->output);
    }

    protected function copyProjectData() {
        exec("cp -r '{$this->configTilemill['projectTemplate']}'/* {$this->projectPath}");
    }

    protected function copyDatasets() {
        exec("cp -r '{$this->configTilemill['datasetPath']}' {$this->projectPath}");
    }

    protected function emptyProjectPath() {
        exec('rm -rf ' . $this->projectPath . '*');
    }

    protected function cleanMbtiles() {
        exec('rm -rf ' . $this->configTilemill['outputMBTiles'] . '/*');
    }

}