<?php

namespace Elevator\Command;

use Elevator\Enum\ElevatorEnum;
use Elevator\Model\Elevator;
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

    /** @var array */
    private $passengers = [];

    /**
     * RunElevatorCommand constructor.
     * @param array $passengers
     */
    public function __construct(array $passengers)
    {
        parent::__construct('run');
        $this->passengers = $passengers;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
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
            $passengerOnFloor = $this->checkPassengersOnTheFloor();
            $passengerInElevator = $this->checkPassengersInTheElevator();
            if ($passengerOnFloor || $passengerInElevator) {
                $this->elevator->open();
                if ($passengerInElevator) {
                    $this->elevator->setDownPassenger($passengerInElevator);
                }
                if ($passengerOnFloor) {
                    $this->elevator->takePassenger(reset($passengerOnFloor));
                    $this->elevator->moveTo(reset($passengerOnFloor));
                    unset($this->passengers[key($passengerOnFloor)]);
                }
                $this->elevator->close();
            }
            if (!$this->checkForUndeliveredPassengers()) {
                break;
            }
            $this->elevator->goToDirection();
        }
    }

    private function checkForUndeliveredPassengers()
    {
        return count($this->passengers) || count($this->elevator->getPassengers());
    }

    /**
     * @return array|null
     */
    private function checkPassengersOnTheFloor(): ?array
    {
        if (($passenger = $this->getPassengerOnFloor($this->elevator->getCurrentFloor())) && ($this->elevator->getDirection() === $this->getPassengerDirection($passenger))) {
            return $passenger;
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
    private function getPassengerOnFloor(int $floor): ?array
    {
        foreach ($this->passengers as $key => $passenger) {
            if ($passenger['startFloor'] == $floor) {
                return [$key => $passenger];
            }
        }
        return null;
    }

    /**
     * @param array $passenger
     * @return int
     */
    private function getPassengerDirection(array $passenger): int
    {
        $passenger = reset($passenger);
        return $passenger['startFloor'] < $passenger['destFloor'] ? ElevatorEnum::DIRECTION_UP : ElevatorEnum::DIRECTION_DOWN;
    }
}