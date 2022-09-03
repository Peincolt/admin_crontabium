<?php

namespace App\Service\Entity;

use App\Entity\Ability as AbilityEntity;
use App\Service\Data\Helper as DataHelper;
use App\Service\Api\SwgohGg;
use App\Repository\AbilityRepository;
use App\Repository\HeroRepository;
use Doctrine\ORM\EntityManagerInterface;

class Ability
{
    private $_swgohgg;
    private $_abilityRepository;
    private $_heroRepository;
    private $_dataHelper;
    private $_entityManagerInterface;

    public function __construct(
        SwgohGg $swgohgg,
        AbilityRepository $abilityRepository,
        HeroRepository $heroRepository,
        DataHelper $dataHelper,
        EntityManagerInterface $entityManagerInterface
    )
    {
        $this->_swgohgg = $swgohgg;
        $this->_abilityRepository = $abilityRepository;
        $this->_heroRepository = $heroRepository;
        $this->_dataHelper = $dataHelper;
        $this->_entityManagerInterface = $entityManagerInterface;
    }

    public function updateAbilities() 
    {
        $data = $this->_swgohgg->fetchAbilities();
        $count = 0;
        if (is_array($data)) {
            foreach ($data as $key => $arrayData) {
                $ability = $this->_abilityRepository->findOneBy(
                    [
                        'baseId' => $arrayData['base_id']
                    ]
                );
                if (empty($ability)) {
                    $ability = new AbilityEntity();
                    $hero = $this->_heroRepository->findOneBy(
                        [
                            'base_id' => $arrayData['character_base_id']
                        ]
                    );
                    if (!empty($hero)) {
                        $ability->setHero($hero);
                        $this->_entityManagerInterface->persist($ability);
                    }
                }
                $this->_dataHelper->fillObject($arrayData, 'ability', $ability);
                if ($count >= 1000) {
                    $this->_entityManagerInterface->flush();
                    $count = 0;
                }
                $count++;
            }
            $this->_entityManagerInterface->flush();
            return true;
        }
        return false;
    }
}