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
    const DISTANCE = 4;

    /** @var int */
    const OPENING_AND_CLOSING = 2;

    /** @var int */
    const TAKE_AND_SET_DOWN = 2;

    /** @var int */
    const FIRST_FLOOR = 1;

    /** @var int */
    const LAST_FLOOR = 10;

    /** @var int */
    const MAX_WEIGHT = 700;

    /** @var OutputInterface */
    private $output;

    /** @var Passenger[] */
    private $passengers = [];

    /** @var int */
    private $currentFloor = 1;

    /** @var int */
    private $direction = ElevatorEnum::DIRECTION_UP;

    /** @var int */
    private $currentWeight = 0;

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
     * @return Elevator
     */
    private function setDirection(int $direction): Elevator
    {
        $this->direction = $direction;
        return $this;
    }

    /**
     * @return array
     */
    public function getPassengers(): array
    {
        return $this->passengers;
    }

    /**
     * @return int
     */
    public function getCurrentWeight(): int
    {
        return $this->currentWeight;
    }

    /**
     * @param int $weight
     * @return Elevator
     */
    public function addWeight(int $weight): Elevator
    {
        $this->currentWeight += $weight;
        return $this;
    }

    /**
     * @param int $weight
     * @return Elevator
     */
    public function removeWeight(int $weight): Elevator
    {
        $this->currentWeight -= $weight;
        return $this;
    }

    public function checkWeight()
    {
        return $this->getCurrentWeight() < self::MAX_WEIGHT ? true : false;
    }

    public function open(): void
    {
        $this->sleep(self::OPENING_AND_CLOSING);
        $this->output->writeln('Открылись двери');
    }

    public function close(): void
    {
        $this->sleep(self::OPENING_AND_CLOSING);
        $this->output->writeln('Закрылись двери');
    }

    /**
     * @param array $passengers
     */
    public function takePassengers(array $passengers): void
    {
        $this->sleep(self::TAKE_AND_SET_DOWN);
        $this->getPassengerInElevator(count($passengers), ['-го пассажира', '-х пассажиров', ' пассажиров']);
        /** @var  Passenger $passenger */
        foreach ($passengers as $key => $passenger) {
            if ($this->checkWeight()) {
                $this->passengers[$key] = $passenger;
                $this->output->writeln('Принял команду перемещения на ' . $passenger->getDestFloor() . ' этаж от пассажира №' . $key);
                $this->addWeight($passenger->getWeight());
            }
        }
    }

    /**
     * @param $num
     * @param $titles
     */
    private function getPassengerInElevator($num, $titles): void
    {
        $cases = [2, 0, 1, 1, 1, 2];
        $this->output->writeln('Подобрал ' .  $num . $titles[($num % 100 > 4 && $num % 100 < 20) ? 2 : $cases[min($num % 10, 5)]] . ' на ' . $this->getCurrentFloor() . 'м этаже');
        $this->sleep(self::TAKE_AND_SET_DOWN);
    }

    /**
     * @param array $passengers
     */
    public function setDownPassengers(array $passengers): void
    {
        $this->sleep(self::TAKE_AND_SET_DOWN);
        /** @var  Passenger $passenger */
        foreach ($passengers as $key => $passenger) {
            $this->output->writeln('Вышел пассажир №' . $key .' зашедший на ' . $passenger->getStartFloor() . 'м этаже');
            $this->removeWeight($passenger->getWeight());
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

    public function goToDirection(): void
    {
        $this->sleep(self::DISTANCE / self::SPEED);
        $this->direction == ElevatorEnum::DIRECTION_UP ? $this->currentFloor++ : $this->currentFloor--;
        $this->output->writeln('Переместились на ' . $this->getCurrentFloor() . ' этаж');
    }

    public function checkLastFloorInDirection(): void
    {
        switch ($this->getCurrentFloor()) {
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
