<?php

namespace App\Service\Entity;

use App\Repository\ShipRepository;

class Ship
{
    private $shipRepository;

    public function __construct(ShipRepository $shipRepository)
    {
        $this->shipRepository = $shipRepository;
    }

    public function getShipsSquadCommand()
    {
        $arrayReturn = array();
        $shipList = $this->getShipsList(null,array('id','name'));
        foreach ($shipList as $ship) {
            $arrayReturn[$ship['name']] = $ship['id'];
        }
        return $arrayReturn;
    }

    public function getShipsList(?array $filters, ?array $select)
    {
        return $this->shipRepository->getShipsListByFilter($filters,$select);
    }
}