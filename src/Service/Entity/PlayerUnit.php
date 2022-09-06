<?php

namespace App\Service\Entity;

use App\Entity\Player;
use App\Repository\HeroPlayerAbilityRepository;
use App\Repository\HeroPlayerRepository;
use App\Repository\ShipPlayerRepository;
use App\Service\Data\Helper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PlayerUnit 
{

    private $_entityManagerInterface;
    private $_dataHelper;
    private $heroPlayerRepository;
    private $shipPlayerRepository;
    private $heroPlayerAbilityRepository;

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        Helper $dataHelper,
        HeroPlayerRepository $heroPlayerRepository,
        ShipPlayerRepository $shipPlayerRepository,
        HeroPlayerAbilityRepository $heroPlayerAbilityRepository
    ) {
        $this->_entityManagerInterface = $entityManagerInterface;
        $this->_dataHelper = $dataHelper;
        $this->heroPlayerRepository = $heroPlayerRepository;
        $this->shipPlayerRepository = $shipPlayerRepository;
        $this->heroPlayerAbilityRepository = $heroPlayerAbilityRepository;
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
                    $playerUnit->setPlayer($player);
                    $this->_entityManagerInterface->persist($playerUnit);
                }
                $playerUnit->$fonctionName($baseUnit);
                $this->_dataHelper->fillObject($data, 'player_'.$type, $playerUnit);
                if ($type == 'hero' && count($data['omicron_abilities']) > 0) {
                    $this->_dataHelper->fillHeroOmicronAbility(
                        $playerUnit,
                        $data['omicron_abilities'],
                        $data['ability_data']
                    );
                }
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

    public function getPlayerUnitInformation(Player $player,string $type, $unit)
    {
        $arrayReturn = array();
        switch ($type) {
            case 'hero':
                $repo = $this->heroPlayerRepository;
            break;
            default:
                $repo = $this->shipPlayerRepository;
            break;
        }

        $unitInformations = $repo->getPlayerInformations($unit, $player);
        if (!empty($unitInformations)) {
            $unitInformation = $unitInformations[0];
            $arrayReturn['name'] = $unit->getName();
            $arrayReturn['rarity'] = $unitInformation->getNumberStars();
            $arrayReturn['level'] = $unitInformation->getLevel();
            $arrayReturn['gear_level'] = $unitInformation->getGearLevel();
            $arrayReturn['relic_level'] = $unitInformation->getRelicLevel();
            $arrayReturn['speed'] = $unitInformation->getSpeed();
            $arrayReturn['life'] = $unitInformation->getLife();
            $arrayReturn['protection'] = $unitInformation->getProtection();
            if ($type == 'hero') {
                $twOmicrons = $this->heroPlayerAbilityRepository->getTwOmicron($unitInformation);
            }
            if (!empty($twOmicrons)) {
                $arrayReturn['omicrons'] = array();
                foreach($twOmicrons as $omicron) {
                    array_push($arrayReturn['omicrons'],$omicron->getAbility()->getName());
                }
            }
        } else {
            $arrayReturn['name'] = 0;
            $arrayReturn['rarity'] = 0;
            $arrayReturn['level'] = 0;
            $arrayReturn['gear_level'] = 0;
            $arrayReturn['relic_level'] = 0;
            $arrayReturn['speed'] = 0;
            $arrayReturn['life'] = 0;
            $arrayReturn['protection'] = 0;
        }
        return $arrayReturn;
    }
}