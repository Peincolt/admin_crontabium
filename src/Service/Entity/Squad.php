<?php

namespace App\Service\Entity;

use Exception;
use App\Entity\Guild;
use App\Entity\HeroPlayer;
use App\Entity\Player;
use App\Entity\ShipPlayer;
use App\Repository\HeroRepository;
use App\Repository\ShipRepository;
use App\Entity\Squad as SquadEntity;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Entity\Unit as UnitHelper;
use App\Service\Entity\PlayerUnit as PlayerUnitService;

class Squad
{
    private $entityManagerInterface;
    private $unitHelper;
    private $playerUnitService;

    public function __construct(EntityManagerInterface $entityManagerInterface,
        UnitHelper $unitHelper,
        PlayerUnitService $playerUnitService
    ) {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->unitHelper = $unitHelper;
        $this->playerUnitService = $playerUnitService;
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

    // RAJOUTER ICI L'ID DE LA GUILDE POUR MODIFIER LA FCT QUI VA CHERCHER LES INFOS DES JOUEURS EN BDD
    // MODIFIER LA FONCTION QUI PERMET DE RECUPERER LES PERSOS D'UNE SQUAD AFIN D'UTILISER LES NEWS SQUAD
    public function getPlayerSquadInformation(SquadEntity $squad, Guild $guild)
    {
        $arrayReturn = array();
        $arrayPlayersName = array();

        $players = $guild->getPlayers();

        foreach ($players as $player) {
            array_push($arrayPlayersName, $player->getName());
        }

        // Ici prendre hero play repo ou ship player. Via le repo, faire une requête qui ressort les hero player ou ship player de la guilde
        // Regarde si pour le HP player, des omicrons TW sont de sortis (requête via repo). Si oui, ajout au tableau avec traduction
        // Fill le tableau avec les infos demandées
        if ($squad->getType() == "hero") {
            $repo = $this->entityManagerInterface->getRepository(HeroPlayer::class);
        } else {
            $repo = $this->entityManagerInterface->getRepository(ShipPlayer::class);
        }

        foreach ($squad->getSquadUnits() as $squadUnit) {
            $unit = $squadUnit->getUnitByType($squad->getType());
            foreach ($guild->getPlayers() as $player) {
                $arrayReturn[$player->getName()][$unit->getName()] = $this->playerUnitService
                    ->getPlayerUnitInformation($player, $squad->getType(), $unit);
            }
        /*foreach ($squad->getSquadUnits() as $squadUnit) {
            $unit = $squadUnit->getUnitByType($squad->getType());
            //$player
            $playerUnit = $repo
                ->getPlayerInformations($unit->getId());
            foreach ($arrayPlayersName as $playerName) {
                $playerUnit = $repo->getPlayerInformations($unit, $player);
                if (array_key_exists($playerName, $playerUnit)) {
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

        }*/

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
        }
        return $arrayReturn;
    }
}