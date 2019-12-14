<?php

namespace App\Service\Entity;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Player;
use App\Service\Api\SwgohGg;
use App\Service\Data\Helper;
use App\Service\Entity\PlayerUnit;

class PlayerHelper {
    
    private $swgoh;
    private $dataHelper;
    private $entityManager;
    private $playerUnit;

    public function __construct(SwgohGg $swgoh, Helper $dataHelper, EntityManagerInterface $entityManager, PlayerUnit $playerUnit) 
    {
        $this->swgoh = $swgoh;
        $this->dataHelper = $dataHelper;
        $this->entityManager = $entityManager;
        $this->playerUnit = $playerUnit;
    }

    public function createPlayer(int $allyCode, bool $characters = false, bool $ships = false)
    {
        $playerDatas = $this->swgoh->fetchPlayer($allyCode);
        if (!isset($playerDatas['error_message'])) {
            $entityField = $this->dataHelper->matchEntityField('player',$playerDatas);
            if (!($player = $this->dataHelper->getDatabaseData("\App\Entity\Player",array('ally_code' => $allyCode)))) {
                $player = new Player();
            }
            foreach($entityField as $key => $value) {
                $function = 'set'.$key;
                $player->$function($value);
            }
            $this->entityManager->persist($player);
            $this->entityManager->flush();

            if ($characters || $ships) {
                foreach($playerDatas['units'] as $key => $value)
                {
                    switch ($value['data']['combat_type']) {
                        case 1:
                            if ($characters) {
                                $retour = $this->playerUnit->createPlayerHero($value['data'],$player);
                            }
                        break;
                        case 2:
                            if ($ships) {
                                $retour = $this->playerUnit->createPlayerShip($value['data'],$player);
                            }
                        break;
                    }
                }
            }

            return $player;
        }
        
        return $playerDatas;
    }
}