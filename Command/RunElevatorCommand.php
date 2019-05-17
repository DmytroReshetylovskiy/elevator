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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        do {
            $this->elevator->checkOnTheTopFloor();
            if ($this->checkPassengersOnTheFloorAndInTheElevator()) {
                $this->elevator->open();
                if ($passengerInElevator = $this->elevator->setDownPassengersOnFloor()) {
                    $this->elevator->setDownPassenger($passengerInElevator);
                }
                if ($passengerOnFloor = $this->getPassengerOnFloor($this->elevator->getCurrentFloor())) {
                    if ($this->elevator->getDirection() === $this->getPassengerDirection($passengerOnFloor)) {
                        $this->elevator->takePassenger(reset($passengerOnFloor));
                        $this->elevator->moveTo(reset($passengerOnFloor));
                        unset($this->passengers[key($passengerOnFloor)]);
                    }
                }
                $this->elevator->close();
                if (!count($this->passengers) || !count($this->elevator->getPassengers())) {
                    continue;
                }
            }
            $this->elevator->goToDirection();
        } while (count($this->passengers) || count($this->elevator->getPassengers()));
    }

    /**
     * @return bool
     */
    private function checkPassengersOnTheFloorAndInTheElevator(): bool
    {
        $passenger = $this->getPassengerOnFloor($this->elevator->getCurrentFloor());
        return ($passenger &&
            ($this->elevator->getDirection() === $this->getPassengerDirection($passenger))) ||
            $this->elevator->setDownPassengersOnFloor();
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