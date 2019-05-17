<?php

namespace Elevator\Command;

use Elevator\Enum\ElevatorEnum;
use Elevator\Model\Elevator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            $currentFloor = $this->elevator->getCurrentFloor();
            if ($currentFloor == $this->getMinPassengerStartFloor()) {
                $passenger = $this->getPassengerOnFloor($this->elevator->getCurrentFloor());
                if ($this->elevator->getDirection() === $this->getPassengerDirection($passenger)) {
                    $this->elevator->open();
                    $this->elevator->takePassenger($passenger);
                    $this->elevator->moveTo(reset($passenger));
                    $this->elevator->close();
                    unset($this->passengers[key($passenger)]);
                }
//                if ($currentFloor == $passenger['destFloor']) {
//                    var_dump(111);die;
//                }
            }
            $this->elevator->goToDirection();
        } while (count($this->passengers) && $this->elevator);

    }

    private function getMinPassengerStartFloor(): int
    {
        return min(array_column($this->passengers, 'startFloor'));
    }

    private function getPassengerOnFloor(int $floor): ?array
    {
        foreach ($this->passengers as $key => $passenger) {
            if ($passenger['startFloor'] == $floor) {
                return [$key => $passenger];
            }
        }
        return null;
    }

    private function getPassengerDirection(array $passenger)
    {
        $passenger = reset($passenger);
        return $passenger['startFloor'] < $passenger['destFloor'] ? ElevatorEnum::DIRECTION_UP : ElevatorEnum::DIRECTION_DOWN;
    }
}