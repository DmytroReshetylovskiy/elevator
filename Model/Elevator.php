<?php

namespace Elevator\Model;

use Elevator\Enum\ElevatorEnum;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Elevator
 * @package Elevator\Model
 */
class Elevator
{
    /** @var int */
    const SPEED = 1;

    /** @var int */
    const HEIGHT = 4;

    /** @var int */
    const FIRST_FLOOR = 1;

    const LAST_FLOOR = 4;

    /** @var OutputInterface */
    private $output;

    /** @var array */
    private $passengers = [];

    /** @var int */
    private $currentFloor = 1;

    /** @var int */
    private $direction = ElevatorEnum::DIRECTION_UP;

    /**
     * Elevator constructor.
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return int
     */
    public function getCurrentFloor(): int
    {
        return $this->currentFloor;
    }

    /**
     * @return int
     */
    public function getDirection(): int
    {
        return $this->direction;
    }

    public function open()
    {
        $this->output->writeln('Открылись двери');
    }

    public function close()
    {
        $this->output->writeln('Закрылись двери');
    }

    public function takePassenger(array $passenger)
    {
        $this->passengers[] = $passenger;
        $this->output->writeln('Подобрал человека на ' . $this->currentFloor . 'м этаже');
    }

    public function moveTo(array $passenger)
    {
        $this->output->writeln('Принял команду перемещения на ' . $passenger['destFloor'] . ' этаж');
    }

    public function goToDirection()
    {
        sleep(2);
        $this->direction == ElevatorEnum::DIRECTION_UP ? $this->currentFloor++ : $this->currentFloor--;
        $this->output->writeln('Переместились на ' . $this->currentFloor . ' этаж');
    }

    /**
     * @return array|null
     */
    public function getPassengersFloor(): ?array
    {
        foreach ($this->passengers as $key => $passenger) {
            if ($passenger['destFloor'] == $this->currentFloor) {
                return [$key => $passenger];
            }
        }
        return null;
    }

    public function exitPassenger(array $passenger)
    {
        $key = array_keys($passenger)[0];
        unset($this->passengers[$key]);
        $this->output->writeln('');
    }
}