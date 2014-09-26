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
use Mapstorming\Config;
use Mapstorming\Project;
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

    protected function configure()
    {
        $this->config = new Config();
        $this->city = new City();
        $this->project = new Project();
        $this->configTilemill = [
            'tileMillDocumentPath' => '/Users/pablochiappetti/Documents/MapBox/project/',
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->projectPath = $this->configTilemill['tileMillDocumentPath'] . $this->configTilemill['projectName'] . '/';
        if (!$this->projectPath) throw new InvalidArgumentException;
        // Style the output
        $output = $this->setOutputFormat($output);
        // Helper to ask questions through Console
        $helper = $this->getHelper('question');

        // Remove everything inside projectPath
        $this->emptyProjectPath();

        // Copy datasets inside projectPath.'datasets/'
        $this->copyDatasets();

        // Copy projectTemplate/* a projectPath
        $this->copyProjectData();

        // Remove everything inside outputMBTiles
        $this->cleanMbtiles();

        $project = $this->project->getJSON();
        $upload = $input->getOption('upload');

        $output->writeln("<say>We're processing <high>" . count($project->Layer) . " layers</high> from geojsons to mbtiles!</say>");

        foreach ($project->Layer as $layer) {
            $project = $this->turnAllLayersExcept($project, $layer);
            $project->name = $layer->name;
            $this->project->save($project);
            $this->copyProjectData();

            $output->writeln("\n<say>Exporting <high>{$layer->name}</high></say>");
            $output->writeln("<ask>Please wait, this may take a while... ☕️   </ask>");
            $this->exportLayer($layer);

            if ($upload) {
                $output->writeln("<say>Uploading <high>{$layer->name}...</high></say>");
                $this->uploadLayer($layer);
                $output->writeln("<say>Uploaded <high>{$layer->name}</high> successfully</say>");
            }
        }

        $output->writeln("\n<say>VAMO ARRIBA! All layers have been <ask>successfully uploaded</ask> to <high>{$this->configTilemill['syncAccount']}'s</high> Mapbox account</say>");
    }

    private function turnAllLayersExcept($project, $layer)
    {
        foreach ($project->Layer as $key => $l) {
            if ($l->name != $layer->name) {
                $l->status = 'Off';
            } else {
                $l->status = 'On';
            }
        }

        return $project;
    }

    private function exportLayer($layer)
    {
        $config = $this->configTilemill;
        $commands = [
            "cd " . $config['tileMillPath']
        ];
        $commands[] = './index.js export ' . $config['projectName'] . ' ' . $config['outputMBTiles'] . '/' . $layer->name . '.mbtiles --format=mbtiles';

        system(implode('&&', $commands));
    }

    private function uploadLayer($layer)
    {
        $config = $this->configTilemill;
        $commands = [
            "cd " . $config['tileMillPath'],
            './index.js export ' . $layer->name . ' ' . $config['outputMBTiles'] . '/' . $layer->name . '.mbtiles --format=upload --syncAccount=' . $config["syncAccount"]
        ];

        system(implode('&&', $commands));
        //  . ' 2>/dev/null 1>&2'
    }

    protected function copyProjectData()
    {
        exec("cp -r '{$this->configTilemill['projectTemplate']}'/* {$this->projectPath}");
    }

    protected function copyDatasets()
    {
        exec("cp -r '{$this->configTilemill['datasetPath']}' {$this->projectPath}");
    }

    protected function emptyProjectPath()
    {
        exec('rm -rf ' . $this->projectPath . '*');
    }

    protected function cleanMbtiles()
    {
        exec('rm -rf ' . $this->configTilemill['outputMBTiles'] . '/*');
    }

}