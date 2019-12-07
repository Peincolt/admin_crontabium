<?php

namespace App\Service\Entity;

use App\Entity\Player;
use App\Service\Api\Swgoh;
use App\Service\Data\Edit;
use Doctrine\ORM\EntityManagerInterface;

class PlayerHelper {
    
    private $swgoh;
    private $localeApi;
    private $editData;
    private $entityManager;

    public function __construct($localeApi, Swgoh $swgoh, Edit $editData, EntityManagerInterface $entityManager) 
    {
        $this->localeApi = $localeApi;
        $this->swgoh = $swgoh;
        $this->editData = $editData;
        $this->entityManager = $entityManager;
    }

    public function createPlayer(int $allyCode, bool $characters, bool $ships)
    {
        $playerDatas = $this->swgoh->fetchPlayer($allyCode,$this->localeApi);
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

            if ($characters) {

            }

            if ($ships) {

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