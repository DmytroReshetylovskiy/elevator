<?php

require_once('vendor/autoload.php');

use Symfony\Component\Console\Application;
use Elevator\Command\RunElevatorCommand;

$application = new Application();

$application->add(new RunElevatorCommand([
    ['startFloor' => 1, 'destFloor' => 4],
    ['startFloor' => 3, 'destFloor' => 2],
    ['startFloor' => 4, 'destFloor' => 1],
]));

$application->run();