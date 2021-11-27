<?php

namespace App\Service\Entity;

use App\Repository\HeroRepository;

class Hero
{
    private $heroRepository;

    public function __construct(HeroRepository $heroRepository)
    {
        $this->heroRepository = $heroRepository;
    }

    public function getHerosSquadCommand()
    {
        $arrayReturn = array();
        $herosList = $this->getHerosList(null,array('id','name'));
        foreach ($herosList as $hero) {
            $arrayReturn[$hero['name']] = $hero['id'];
        }
        return $arrayReturn;
    }

    public function getHerosList(?array $filters, ?array $select)
    {
        return $this->heroRepository->getHerosListByFilter($filters,$select);
    }
}