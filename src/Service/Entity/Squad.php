<?php

namespace App\Service\Entity;

use Doctrine\ORM\EntityManagerInterface;
use App\Service\Entity\Unit as UnitHelper;
use Exception;

class Squad
{
    private $entityManagerInterface;
    private $unitHelper;

    public function __construct(EntityManagerInterface $entityManagerInterface, UnitHelper $unitHelper)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->unitHelper = $unitHelper;
    }

    public function createSquad($squad, $data)
    {
        try {
            $this->resetSquad($squad);
            $squad->setName($data['name']);
            for($i=1;$i<=5;$i++) {
                $entity = $this->unitHelper->findUnitByName($data['unit'.$i]);
                $functionName = 'add'.$entity['type'];
                $squad->$functionName($entity['data']);
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

}