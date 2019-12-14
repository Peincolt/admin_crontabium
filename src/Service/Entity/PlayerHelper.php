<?php

namespace App\Service\Entity;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Player;
use App\Service\Api\SwgohGg;
use App\Service\Data\Edit;
use App\Service\Entity\HeroShipPlayerHelper;

class PlayerHelper {
    
    private $swgoh;
    private $editData;
    private $entityManager;
    private $heroShipPlayerHelper;
    private $shipPlayerHelper;

    public function __construct(SwgohGg $swgoh, Edit $editData, EntityManagerInterface $entityManager, HeroShipPlayerHelper $heroShipPlayerHelper) 
    {
        $this->swgoh = $swgoh;
        $this->editData = $editData;
        $this->entityManager = $entityManager;
        $this->heroShipPlayerHelper = $heroShipPlayerHelper;
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
                    switch ($value['combat_type']) {
                        case 1:
                            if ($characters) {
                                $this->heroShipPlayerHelper->createPlayerHero($value,$player);
                            }
                        break;
                        case 2:
                            if ($ships) {
                                $this->heroShipPlayerHelper->createPlayerShip($value,$player);
                            }
                        break;
                    }
                    var_dump($value);
                    die('ok');
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