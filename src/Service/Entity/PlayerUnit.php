<?php

namespace App\Service\Entity;

use App\Entity\Player;
use App\Service\Data\Helper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PlayerUnit 
{

    private $_entityManagerInterface;
    private $_dataHelper;

    public function __construct(EntityManagerInterface $entityManagerInterface, Helper $dataHelper)
    {
        $this->_entityManagerInterface = $entityManagerInterface;
        $this->_dataHelper = $dataHelper;
    }

    public function createPlayerUnit(array $data, Player $player, string $type)
    {
        try {
            $baseUnitEntityName = "\App\Entity\\".ucfirst($type);
            $entityName = $baseUnitEntityName."Player";
            $fonctionName = 'set'.ucfirst($type);
            if ($baseUnit = $this->_dataHelper->getDatabaseData($baseUnitEntityName, array('base_id' => $data['base_id']))) {
                if (!$playerUnit = $this->_dataHelper->getDatabaseData($entityName, array('player' => $player, $type => $baseUnit))) {
                    $playerUnit = new $entityName;
                }
                $this->_dataHelper->fillObject($data, 'player_'.$type, $playerUnit);
                if ($type == 'hero') {
                    //$this->_dataHelper->fillHeroAbility
                }
                $playerUnit->setPlayer($player);
                $playerUnit->$fonctionName($baseUnit);
                $this->_entityManagerInterface->persist($playerUnit);
                $this->_entityManagerInterface->flush();
            }
        } catch (Exception $e) {
            $arrayReturn['error_message'] = $e->getMessage();
            $arrayReturn['error_code'] = $e->getCode();
            return $arrayReturn;
        }
    }

    public function createPlayerShip(array $data, Player $player)
    {
        return $this->createPlayerUnit($data, $player, 'ship');
    }

    public function createPlayerHero(array $data, Player $player)
    {
        return $this->createPlayerUnit($data, $player, 'hero');
    }

    public function getNumberUnit($type)
    {
        $numberHeroes = $this->entityManagerInterface
            ->getRepository($type)
            ->findAll();
        return count($numberHeroes);
    }
}