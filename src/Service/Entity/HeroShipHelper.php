<?php

namespace App\Service\Entity;

use App\Service\Api\SwgohGg;
use App\Service\Data\Edit;
use Doctrine\ORM\EntityManagerInterface;

class HeroShipHelper {

    private $entityManagerInterface;
    private $swgohGg;
    private $editData;

    public function __construct(EntityManagerInterface $entityManagerInterface, SwgohGg $swgohGg, Edit $editData)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->swgohGg = $swgohGg;
        $this->editData = $editData;
    }

    public function updateHeroesOrShips($type,$listId = false) 
    {
        $entityName = "\App\Entity\\".$this->editData->convertTypeToEntityName($type);
        $data = $this->swgohGg->fetchHeroOrShip($type,$listId);
        if (!isset($data['error_message'])) {
            foreach($data as $key => $value) {
                if (!($hero = $this->isHeroOrShipExist($entityName,$data[$key]['base_id']))) {
                    $hero = new $entityName;
                }
                $entityField = $this->editData->matchEntityField($type,$data[$key]);
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

    public function isHeroOrShipExist(string $entityName, string $idBase)
    {
        return $this->entityManagerInterface
            ->getRepository($entityName)
            ->findOneBy(['base_id' => $idBase]);
    }
    
}