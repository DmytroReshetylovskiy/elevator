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
    const LAST_FLOOR = 10;

    /** @var OutputInterface */
    private $output;

    /** @var Passenger[] */
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

    public function open(): void
    {
        $this->sleep(2);
        $this->output->writeln('Открылись двери');
    }

    public function close(): void
    {
        $this->sleep(2);
        $this->output->writeln('Закрылись двери');
    }

    /**
     * @param array $passengers
     */
    public function takePassenger(array $passengers): void
    {
        $this->sleep(2);
        $this->getPassengerInElevator(count($passengers), ['-го пассажира', '-х пассажиров', ' пассажиров']);
        /** @var  Passenger $passenger */
        foreach ($passengers as $key => $passenger) {
            $this->passengers[$key] = $passenger;
            $this->output->writeln('Принял команду перемещения на ' . $passenger->getDestFloor() . ' этаж от пассажира №' . $key);
        }
    }

    /**
     * @param $num
     * @param $titles
     */
    private function getPassengerInElevator($num, $titles): void
    {
        $cases = [2, 0, 1, 1, 1, 2];
        $this->output->writeln('Подобрал ' .  $num . $titles[($num % 100 > 4 && $num % 100 < 20) ? 2 : $cases[min($num % 10, 5)]] . ' на ' . $this->currentFloor . 'м этаже');
        $this->sleep(2);
    }

    /**
     * @param array $passengers
     */
    public function setDownPassenger(array $passengers): void
    {
        $this->sleep(2);
        /** @var  Passenger $passenger */
        foreach ($passengers as $key => $passenger) {
            $this->output->writeln('Вышел пассажир №' . $key .' зашедший на ' . $passenger->getStartFloor() . 'м этаже');
            unset($this->passengers[$key]);
        }
    }

    /**
     * @return array|null
     */
    public function setDownPassengersOnFloor(): ?array
    {
        $passengers = [];
        foreach ($this->passengers as $key => $passenger) {
            if ($passenger->getDestFloor() == $this->getCurrentFloor()) {
                $passengers[$key] = $passenger;
            }
        }
        return count($passengers) ? $passengers : null;
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

    public function checkLastFloorInDirection(): void
    {
        switch ($this->currentFloor) {
            case self::FIRST_FLOOR:
                $this->setDirection(ElevatorEnum::DIRECTION_UP);
                break;
            case self::LAST_FLOOR:
                $this->setDirection(ElevatorEnum::DIRECTION_DOWN);
                break;
        }
    }

    /**
     * @param int $value
     */
    private function sleep(int $value): void
    {
        sleep($value);
    }
}
