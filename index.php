<?php

require_once('vendor/autoload.php');

use Symfony\Component\Console\Application;
use Elevator\Command\RunElevatorCommand;

$application = new Application();

$application->add(new RunElevatorCommand());

$application->run();