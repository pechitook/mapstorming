#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

use Mapstorming\Commands\AddNewCity;
use Mapstorming\Commands\ProcessDatasets;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new ProcessDatasets());
$application->add(new AddNewCity());
$application->run();