<?php

namespace App\Service\Entity;

use App\Service\Api\SwgohGg;
use App\Service\Data\Helper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;

class Unit {

    private $entityManagerInterface;
    private $swgohGg;
    private $dataHelper;

    public function __construct(EntityManagerInterface $entityManagerInterface, SwgohGg $swgohGg, Helper $dataHelper)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->swgohGg = $swgohGg;
        $this->dataHelper = $dataHelper;
    }

    public function updateUnit($type,$listId = false) 
    {
        $entityName = "\App\Entity\\".$this->dataHelper->convertTypeToEntityName($type);
        $data = $this->swgohGg->fetchHeroOrShip($type,$listId);
        if (!isset($data['error_message'])) {
            foreach($data as $key => $value) {
                if (!($hero = $this->dataHelper->getDatabaseData($entityName,array('base_id' => $data[$key]['base_id'])))) {
                    $hero = new $entityName;
                }
                $entityField = $this->dataHelper->matchEntityField($type,$data[$key]);
                foreach($entityField as $key => $value) {
                    $function = 'set'.$key;
                    $hero->$function($value);
                }
                $this->entityManagerInterface->persist($hero);
                $this->entityManagerInterface->flush();
            }
        } else {
            return $data;
        }
    }

    public function getEntityByRoute(string $route)
    {
        $arrayReturn = array();
        $array = explode('_',$route);
        if (count($array) > 2) {
            $arrayReturn['name'] = ucfirst($array[2]);
        } else {
            $arrayReturn['name'] = ucfirst($array[1]);
        }
        $arrayReturn['namespace'] = "App\Entity\\".$arrayReturn['name'];
        $arrayReturn['player_class_name'] = $arrayReturn['name'].'Player';
        $arrayReturn['player_namespace_class'] = "App\Entity\\".$arrayReturn['player_class_name'];
        $arrayReturn['function'] = 'get'.$arrayReturn['player_class_name'].'s';
        return $arrayReturn;
    }

    public function getUnits($type)
    {
        $arrayReturn = array();
        $i=0;
        $units = $this->entityManagerInterface
            ->getRepository("App\Entity\\".ucfirst($type))
            ->findAll();
        foreach ($units as $unit) {
            $arrayReturn[$unit->getName()] = $unit->getId();
        }

        return $arrayReturn;
    }

    public function getAllUnits(ItemInterface $item = null)
    {
        //$item->expiresAfter(3600);
        
        $arrayReturn = array();

        $heroes = $this->entityManagerInterface
            ->getRepository("App\Entity\Hero")
            ->findAll();
        $ships = $this->entityManagerInterface
            ->getRepository("App\Entity\Ship")
            ->findAll();

        foreach($heroes as $hero) {
            array_push($arrayReturn,htmlentities($hero->getName()));
        }

        foreach($ships as $ship) {
            array_push($arrayReturn,htmlentities($ship->getName()));
        }

        return $arrayReturn;
        
    }

    public function findUnitByName($name) {
        $arrayReturn = array();
        $name = html_entity_decode($name);

        $hero = $this->entityManagerInterface
            ->getRepository("App\Entity\Hero")
            ->findOneBy(['name' => $name]);
            $arrayReturn['type'] = 'Hero';
            $arrayReturn['data'] = $hero;

        if (empty($hero)) {
            $ship = $this->entityManagerInterface
            ->getRepository("App\Entity\Ship")
            ->findOneBy(['name' => $name]);
            $arrayReturn['type'] = 'Ship';
            $arrayReturn['data'] = $ship;
        }

        return $arrayReturn;

    }
}