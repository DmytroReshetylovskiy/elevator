<?php

namespace Elevator\Command;

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
                $this->elevator->open();
                $this->elevator->takePassenger($passenger);
                $this->elevator->moveTo($passenger[0]['destFloor']);
                $this->elevator->close();
            }
            $this->elevator->goUp();
            sleep(2);
            if ($passenger = $this->elevator->getPassengersFloor()) {
                $this->elevator->open();
                $this->elevator->exitPassenger($passenger);
            }
            var_dump(111);die;

        } while (count($this->passengers));

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
}