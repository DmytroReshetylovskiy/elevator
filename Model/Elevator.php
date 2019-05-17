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

    /** @var int */
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

    /**
     * @param int $direction
     * @return self
     */
    private function setDirection(int $direction): self
    {
        $this->direction = $direction;
        return $this;
    }

    public function open()
    {
        $this->sleep(2);
        $this->output->writeln('Открылись двери');
    }

    public function close()
    {
        $this->sleep(2);
        $this->output->writeln('Закрылись двери');
    }

    /**
     * @param array $passenger
     */
    public function takePassenger(array $passenger)
    {
        $this->sleep(2);
        $this->passengers[] = $passenger;
        $this->output->writeln('Подобрал человека на ' . $this->currentFloor . 'м этаже');
    }

    /**
     * @param array $passenger
     */
    public function setDownPassenger(array $passenger)
    {
        $this->sleep(2);
        $passengerStartFloor = reset($passenger);
        $this->output->writeln('Вышел пассажир зашедший на ' . $passengerStartFloor['startFloor'] . 'м этаже');
        unset($this->passengers[key($passenger)]);
    }

    /**
     * @param array $passenger
     */
    public function moveTo(array $passenger)
    {
        $this->sleep(2);
        $this->output->writeln('Принял команду перемещения на ' . $passenger['destFloor'] . ' этаж');
    }

    /**
     * @return array|null
     */
    public function setDownPassengersOnFloor(): ?array
    {
        foreach ($this->passengers as $key => $passenger) {
            if ($passenger['destFloor'] == $this->getCurrentFloor()) {
                return [$key => $passenger];
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function getPassengers(): array
    {
        return $this->passengers;
    }

    public function goToDirection()
    {
        $this->sleep(4);
        $this->direction == ElevatorEnum::DIRECTION_UP ? $this->currentFloor++ : $this->currentFloor--;
        $this->output->writeln('Переместились на ' . $this->currentFloor . ' этаж');
    }

    public function checkOnTheTopFloor()
    {
        if (self::LAST_FLOOR == $this->getCurrentFloor()) {
            $this->setDirection(ElevatorEnum::DIRECTION_DOWN);
        }
    }

    /**
     * @param int $value
     */
    private function sleep(int $value)
    {
        sleep($value);
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

    /**
     * @param array $passenger
     */
    public function exitPassenger(array $passenger)
    {
        $key = array_keys($passenger)[0];
        unset($this->passengers[$key]);
        $this->output->writeln('');
    }
}