#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

use Mapstorming\Commands\AddNewCity;
use Mapstorming\Commands\ProcessDatasets;
use Symfony\Component\Console\Application;

$process = new ProcessDatasets();
$application = new Application();
$application->add($process);
$application->setDefaultCommand($process->getName());
$application->add(new AddNewCity());
$application->run();