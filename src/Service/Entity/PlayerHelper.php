<?php

namespace App\Service\Entity;

use App\Entity\Guild;
use App\Entity\Player;
use App\Service\Api\SwgohGg;
use App\Service\Data\Helper;
use App\Service\Entity\PlayerUnit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

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

    public function createPlayer(int $allyCode, bool $characters = false, bool $ships = false, array $playerDatas = null, $guild = false)
    {
        if (!$playerDatas) {
            $playerDatas = $this->swgoh->fetchPlayer($allyCode);
        }

        if (!isset($playerDatas['error_message'])) {
            try {
                $entityField = $this->dataHelper->matchEntityField('player',$playerDatas);
                if (!($player = $this->dataHelper->getDatabaseData("\App\Entity\Player",array('ally_code' => $allyCode)))) {
                    $player = new Player();
                }
                foreach($entityField as $key => $value) {
                    $function = 'set'.$key;
                    $player->$function($value);
                }
    
                if ($guild) {
                    $player->setGuild($guild);
                }
    
                $this->entityManager->persist($player);
                $this->entityManager->flush();
    
                if ($characters || $ships) {
                    foreach($playerDatas['units'] as $key => $value)
                    {
                        switch ($value['data']['combat_type']) {
                            case 1:
                                if ($characters) {
                                    $result = $this->playerUnit->createPlayerHero($value['data'],$player);
                                }
                            break;
                            case 2:
                                if ($ships) {
                                    $result = $this->playerUnit->createPlayerShip($value['data'],$player);
                                }
                            break;
                        }
                        if (isset($result['error_message'])) {
                        break;
                        return $result;
                        }
                    }
                }
                return array('message' => 'Player is save on the database', 'code' => 200);
            } catch (Exception $e) {
                $arrayReturn['error_code'] = $e->getCode();
                $arrayReturn['error_message'] = $e->getMessage();
                return $arrayReturn;
            }
        }
        return $playerDatas;
    }

    public function getFields()
    {
        $arrayReturn = array();
        $arrayReturn['name'] = 'Nom';
        $arrayReturn['galactical_puissance'] = 'Puissance galactique';
        $arrayReturn['characters_galactical_puissance'] = 'Puissance galactique des héros';
        $arrayReturn['ships_galactical_puissance'] = 'Puissance galactique des vaisseaux';
        return $arrayReturn;
    }
}