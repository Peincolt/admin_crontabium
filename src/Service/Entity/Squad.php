<?php

namespace App\Service\Entity;

use Exception;
use App\Entity\Player;
use App\Entity\Squad as SquadEntity;
use App\Repository\HeroRepository;
use App\Repository\ShipRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Entity\Unit as UnitHelper;

class Squad
{
    private $entityManagerInterface;
    private $unitHelper;
    private $heroRepository;
    private $shipRepository;

    public function __construct(EntityManagerInterface $entityManagerInterface,
        UnitHelper $unitHelper,
        HeroRepository $heroRepository,
        ShipRepository $shipRepository)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->unitHelper = $unitHelper;
        $this->heroRepository = $heroRepository;
        $this->shipRepository = $shipRepository;
    }

    public function createSquad($squad, $data)
    {
        try {
            $this->resetSquad($squad);
            $squad->setName($data['name']);
            for($i=1;$i<=5;$i++) {
                $entity = $this->unitHelper->findUnitByName($data['unit'.$i]);
                if (!empty($entity['data'])) {
                    $functionName = 'add'.$entity['type'];
                    $squad->$functionName($entity['data']);
                } else {
                    $arrayReturn['code'] = 404;
                    $arrayReturn['error_message'] = 'L\'unité '.$data['unit'.$i].' n\'existe pas';
                    return $arrayReturn;
                }
            }
            $this->entityManagerInterface
                ->persist($squad);
            $this->entityManagerInterface
                ->flush();
            return 200;
        } catch (Exception $e) {
            $arrayReturn['code'] = 500;
            $arrayReturn['error_message'] = $e->getMessage();
            return $arrayReturn;
        }
    }

    public function resetSquad($squad)
    {
        $heroes = $squad->getHero();
        $ships = $squad->getShip();

        if (count($heroes) > 0 || count($ships) > 0) {
            if (!empty($heroes)) {
                foreach($heroes as $hero) {
                    $squad->removeHero($hero);
                }
            }
    
            if (!empty($ships)) {
                foreach($ships as $ship) {
                    $squad->removeShip($ship);
                }
            }
    
            $this->entityManagerInterface
                ->persist($squad);
    
            $this->entityManagerInterface
                ->flush();
        }
    }

    public function getSquadUnits($squad)
    {
        $arrayReturn = array();
        $number = 1;
        $heroes = $squad->getHero();
        $ships = $squad->getShip();

        if (!empty($heroes)) {
            foreach($heroes as $hero) {
                $arrayReturn[] = $hero->getName();
            }
        }

        if (!empty($ships)) {
            foreach($ships as $ship) {
                $arrayReturn[] = $ship->getName();
            }
        }

        return $arrayReturn;
    }

    public function squadToForm($squad)
    {
        $arrayReturn = array();
        $number = 1;
        $heroes = $squad->getHero();
        $ships = $squad->getShip();
        $arrayReturn['name'] = $squad->getName();

        if (!empty($heroes)) {
            foreach($heroes as $hero) {
                $arrayReturn['unit'.$number] = $hero->getName();
                $number++;
            }
        }

        if (!empty($ships)) {
            foreach($ships as $ship) {
                $arrayReturn['unit'.$number] = $ship->getName();
                $number++;
            }
        }

        return $arrayReturn;
    }

    public function squadToArray($squad)
    {
        $arrayReturn = array();
        $arrayReturn['units'] = $this->getSquadUnits($squad);
        
    }

    public function getPlayerSquadInformation($id)
    {
        $arrayReturn = array();
        $arrayPlayersName = array();

        $players = $this->entityManagerInterface
            ->getRepository(Player::class)
            ->findAll();

        foreach($players as $player) {
            array_push($arrayPlayersName,$player->getName());
        }

        $squad = $this->entityManagerInterface
            ->getRepository(SquadEntity::class)
            ->find($id);

        if ($squad->getType() == "hero") {
            $list = $squad->getHero();
            $repo = $this->heroRepository;
        } else {
            $list = $squad->getShip();
            $repo = $this->shipRepository;
        }

        foreach ($list as $unit) {
            $playerUnit = $repo
                ->getPlayerInformations($unit->getId());
            foreach($arrayPlayersName as $playerName) {
                if (array_key_exists($playerName,$playerUnit)) {
                    $arrayReturn[$playerName][$unit->getName()] = $playerUnit[$playerName];
                } else {
                    $arrayReturn[$playerName][$unit->getName()]['gear_level'] = 0;
                    $arrayReturn[$playerName][$unit->getName()]['rarity'] = 0;
                    $arrayReturn[$playerName][$unit->getName()]['life'] = 0;
                    $arrayReturn[$playerName][$unit->getName()]['protection'] = 0;
                    $arrayReturn[$playerName][$unit->getName()]['relic_level'] = 0;
                    $arrayReturn[$playerName][$unit->getName()]['speed'] = 0;
                }
            }

        }

        /*foreach ($squad->getShip() as $ship) {
            $playerShip = $this->shipRepository
                ->getPlayerInformations($ship->getId());
            foreach($arrayPlayersName as $playerName) {
                if (array_key_exists($playerName,$playerShip)) {
                    $arrayReturn[$playerName][$ship->getName()] = $playerShip[$playerName];
                } else {
                    $arrayReturn[$playerName][$hero->getName()]['gear_level'] = 0;
                    $arrayReturn[$playerName][$hero->getName()]['rarity'] = 0;
                    $arrayReturn[$playerName][$hero->getName()]['life'] = 0;
                    $arrayReturn[$playerName][$hero->getName()]['protection'] = 0;
                    $arrayReturn[$playerName][$hero->getName()]['relic_level'] = 0;
                    $arrayReturn[$playerName][$hero->getName()]['speed'] = 0;
                }
            }
        }*/

        return $arrayReturn;
    }

}