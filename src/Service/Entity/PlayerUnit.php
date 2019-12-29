<?php

namespace App\Service\Entity;

use App\Entity\Player;
use App\Service\Data\Helper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

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
        try {
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
                return array('message' => 'Unit is on the database', 'code' => 200);
            }
        } catch (Exception $e) {
            $arrayReturn['error_message'] = $e->getMessage();
            $arrayReturn['error_code'] = $e->getCode();
            return $arrayReturn;
        }
    }

    public function createPlayerShip(array $data, Player $player)
    {
        return $this->createPlayerUnit($data,$player,'ship');
    }

    public function createPlayerHero(array $data, Player $player)
    {
        return $this->createPlayerUnit($data,$player,'hero');
    }

    public function getNumberUnit($type) {
        $numberHeroes = $this->entityManagerInterface
            ->getRepository('App\Entity\\'.$type.'Player')
            ->findAll();
        return count($numberHeroes);
    }
}