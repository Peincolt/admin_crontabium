<?php

namespace App\Service\Entity;

use App\Entity\Player;

class HeroShipPlayerHelper 
{

    public function __construct()
    {

    }

    public function createPlayerUnit(array $data)
    {

    }

    public function createPlayerShip(array $data, Player $player)
    {
        $this->createPlayerUnit($data,$player,'ship');
    }

    public function createPlayerHero(array $data, Player $player)
    {
        $this->createPlayerUnit($data,$player,'hero');
    }

}