<?php

namespace App\Service\Entity;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Player;
use App\Service\Api\SwgohGg;
use App\Service\Data\Edit;
use App\Service\Entity\PlayerUnit;

class PlayerHelper {
    
    private $swgoh;
    private $editData;
    private $entityManager;
    private $playerUnit;

    public function __construct(SwgohGg $swgoh, Edit $editData, EntityManagerInterface $entityManager, PlayerUnit $playerUnit) 
    {
        $this->swgoh = $swgoh;
        $this->editData = $editData;
        $this->entityManager = $entityManager;
        $this->playerUnit = $playerUnit;
    }

    public function createPlayer(int $allyCode, bool $characters = false, bool $ships = false)
    {
        $playerDatas = $this->swgoh->fetchPlayer($allyCode);
        if (!isset($playerDatas['error_message'])) {
            $entityField = $this->editData->matchEntityField('player',$playerDatas);
            if (!($player = $this->isPlayerIsOnTheGuild($allyCode))) {
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

    public function isPlayerIsOnTheGuild(int $allyCode)
    {
        return $this->entityManager->getRepository(Player::class)->findOneBy(['ally_code' => $allyCode]);
    }
}