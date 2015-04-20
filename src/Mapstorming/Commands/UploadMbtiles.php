<?php

namespace Mapstorming\Commands;

use Aws\S3\S3Client;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class UploadMbtiles extends MapstormingCommand{

    public function configure()
    {
        $this->setName('upload')
           ->setDescription('Upload mbtiles to production servers')
           ->addArgument(
               'city',
               InputArgument::REQUIRED,
               'City ID'
           )
           ->addArgument(
               'dataset',
               InputArgument::REQUIRED,
               'Data set name (bike_shop, cycleway)'
           )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Style the output
        $output = $this->setOutputFormat($output);

        if (!$city = $input->getArgument('city')){

        }

        if (!$dataset = $input->getArgument('dataset')){

        }

        $filename = $this->getFilename($city, $dataset);
        $output->writeln("<say>Uploading $dataset to S3...</say>");

        $client = S3Client::factory([
            'key' => getenv('AWS_ACCESS_KEY_ID'),
            'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
        ]);

        $result = $client->putObject(array(
            'Bucket'     => 'bk-mbtiles',
            'Key'        => "$filename.mbtiles",
            'SourceFile' => getenv('FULLPATH')."tilemill_project/datasets/$city/mbtiles/$filename.mbtiles"
        ));


    }
}
