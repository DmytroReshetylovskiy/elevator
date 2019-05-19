<?php

namespace Elevator\Command;

use Elevator\Model\PassengerManager;
use Elevator\Model\Elevator;
use Elevator\Model\Passenger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RunElevatorCommand
 * @package Elevator\Command
 */
class RunElevatorCommand extends Command
{
    /** @var Elevator */
    private $elevator;

    /** @var Passenger[] */
    private $passengers = [];

    /**
     * RunElevatorCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('run');
        $this->passengers = PassengerManager::generatePassengers(20);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);
        $this->elevator = new Elevator($output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        while (true) {
            $this->elevator->checkLastFloorInDirection();
            $passengersOnFloor = $this->checkPassengersOnTheFloor();
            $passengersInElevator = $this->checkPassengersInTheElevator();
            if ($passengersOnFloor || $passengersInElevator) {
                $this->elevator->open();
                if ($passengersInElevator) {
                    $this->elevator->setDownPassenger($passengersInElevator);
                }
                if ($passengersOnFloor) {
                    $this->elevator->takePassenger($passengersOnFloor);
                    $this->passengers->deletePassengers($passengersOnFloor);
                }
                $this->elevator->close();
            }
            if (!$this->checkForUndeliveredPassengers()) {
                break;
            }
            $this->elevator->goToDirection();
        }
    }

    /**
     * @return bool
     */
    private function checkForUndeliveredPassengers(): bool
    {
        return count($this->passengers->getPassengers()) || count($this->elevator->getPassengers());
    }

    /**
     * @return array|null
     */
    private function checkPassengersOnTheFloor(): ?array
    {
        if (($passengers = $this->getPassengersOnFloor($this->elevator->getCurrentFloor()))) {
            return $passengers;
        }
        return null;
    }

    /**
     * @return array|null
     */
    private function checkPassengersInTheElevator(): ?array
    {
        return $this->elevator->setDownPassengersOnFloor();
    }

    /**
     * @param int $floor
     * @return array|null
     */
    private function getPassengersOnFloor(int $floor): ?array
    {
        $passengers = [];
        foreach ($this->passengers->getPassengers() as $key => $passenger) {
            if (($passenger->getStartFloor() == $floor) && ($passenger->getDirection() == $this->elevator->getDirection())) {
                $passengers[$key] = $passenger;
            }
        }
        return count($passengers) ? $passengers : null;
    }
}