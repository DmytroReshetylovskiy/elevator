<?php

namespace Elevator\Enum;

use Elevator\Model\Passenger;

/**
 * Class PassengerManager
 * @package Elevator\Enum
 */
class PassengerManager
{
    /** @var PassengerManager */
    private static $instance;

    /**
     * @return PassengerManager
     */
    private static function getInstance()
    {
        return self::$instance ?? static::$instance = new self();
    }

    /**
     * PassengerManager constructor.
     */
    private function __construct()
    {
        //
    }

    /** @var Passenger[] */
    private $passengers = [];

    /**
     * @return Passenger[]
     */
    public function getPassengers(): array
    {
        return $this->passengers;
    }

    /**
     * @param Passenger $passenger
     */
    public function addPassenger(Passenger $passenger): void
    {
        array_push($this->passengers, $passenger);
    }

    /**
     * @param int $value
     * @return PassengerManager
     */
    public static function generatePassengers(int $value)
    {
        $passengerManager = self::getInstance();
        for ($i = 0; $i < $value; $i++) {
            $startFloor = rand(1, 10);
            do {
                $destFloor = rand(1, 10);
            } while ($destFloor == $startFloor);
            $status = rand(PassengerStatusEnum::VIP, PassengerStatusEnum::REGULAR);
            $passengerManager->addPassenger(new Passenger($startFloor, $destFloor, $status));
        }
        return $passengerManager;
    }


}
