<?php

namespace App\Service\Entity;

use App\Service\Api\SwgohGg;
use App\Service\Data\Helper;
use Doctrine\ORM\EntityManagerInterface;

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
            }
            $this->entityManagerInterface->flush();
        } else {
            return $data;
        }
    }
}