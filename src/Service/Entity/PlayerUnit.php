<?php

namespace App\Service\Entity;

use App\Entity\Player;
use App\Service\Data\Edit;
use Doctrine\ORM\EntityManagerInterface;

class PlayerUnit 
{

    private $entityManagerInterface;
    private $editData;

    public function __construct(EntityManagerInterface $entityManagerInterface, Edit $editData)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->editData = $editData;
    }

    public function createPlayerUnit(array $data, Player $player, string $type)
    {
        $baseUnitEntityName = "\App\Entity\\".ucfirst($type);
        $entityName = $baseUnitEntityName."Player";
        $fonctionName = 'set'.ucfirst($type);
        if ($baseUnit = $this->getUnit($baseUnitEntityName, array('base_id' => $data['base_id']))) {
            if (!$playerUnit = $this->getUnit($entityName, array('player' => $player, $type => $baseUnit))) {
                $playerUnit = new $entityName;
            }
            $entityField = $this->editData->matchEntityField('player_'.$type,$data);
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

    public function getUnit(string $entityName, array $data)
    {
        return $this->entityManagerInterface
            ->getRepository($entityName)
            ->findOneBy($data);
    }
}