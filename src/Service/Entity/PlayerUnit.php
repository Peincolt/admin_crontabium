<?php

namespace App\Service\Entity;

use App\Entity\Player;
use App\Service\Data\Helper;
use Doctrine\ORM\EntityManagerInterface;

class PlayerUnit 
{

    private $entityManagerInterface;
    private $dataHelper;

    public function __construct(EntityManagerInterface $entityManagerInterface, Helper $dataHelper)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->dataHelper = $dataHelper;
    }

    public function createPlayerUnit(array $data, Player $player, string $type)
    {
        $baseUnitEntityName = "\App\Entity\\".ucfirst($type);
        $entityName = $baseUnitEntityName."Player";
        $fonctionName = 'set'.ucfirst($type);
        if ($baseUnit = $this->dataHelper->getDatabaseData($baseUnitEntityName, array('base_id' => $data['base_id']))) {
            if (!$playerUnit = $this->dataHelper->getDatabaseData($entityName, array('player' => $player, $type => $baseUnit))) {
                $playerUnit = new $entityName;
            }
            $entityField = $this->dataHelper->matchEntityField('player_'.$type,$data);
            foreach($entityField as $key => $value) {
                $function = 'set'.$key;
                $playerUnit->$function($value);
            }
            $playerUnit->setPlayer($player);
            $playerUnit->$fonctionName($baseUnit);
            $this->entityManagerInterface->persist($playerUnit);
            $this->entityManagerInterface->flush();
            return $playerUnit;
        }
        return null;
    }

    public function createPlayerShip(array $data, Player $player)
    {
        return $this->createPlayerUnit($data,$player,'ship');
    }

    public function createPlayerHero(array $data, Player $player)
    {
        return $this->createPlayerUnit($data,$player,'hero');
    }
}