<?php

namespace App\Service\Entity;

use App\Entity\Guild;
use App\Entity\Player;
use App\Service\Api\SwgohGg;
use App\Service\Data\Helper;
use App\Service\Entity\PlayerUnit;
use Doctrine\ORM\EntityManagerInterface;

class PlayerHelper {
    
    private $_swgoh;
    private $_dataHelper;
    private $_entityManager;
    private $_playerUnit;

    public function __construct(
        SwgohGg $swgoh, 
        Helper $dataHelper, 
        EntityManagerInterface $entityManager, 
        PlayerUnit $playerUnit
    ) 
    {
        $this->_swgoh = $swgoh;
        $this->_dataHelper = $dataHelper;
        $this->_entityManager = $entityManager;
        $this->_playerUnit = $playerUnit;
    }

    public function updatePlayer(array $arrayDataPlayer, bool $characters = false, bool $ships = false)
    {
        if (!($player = $this->_entityManager->getRepository(Player::class)->findOneBy(['id_swgoh' => $arrayDataPlayer['data']['ally_code']]))) {
            $player = new Player();
            $this->_entityManager->persist($player);
        }
        $this->_dataHelper->fillObject($arrayDataPlayer, 'player', $player);
        $this->_entityManager->flush();
        if ($characters || $ships) {
            foreach ($arrayDataPlayer['units'] as $unit) {
                switch ($unit['data']['combat_type']) {
                    case 1:
                        if ($characters) {
                            $this->_playerUnit->createPlayerHero(
                                $unit['data'],
                                $player
                            );
                        }
                    break;
                    case 2:
                        if ($ships) {
                            $this->_playerUnit->createPlayerShip(
                                $unit['data'],
                                $player
                            );
                        }
                    break;
                }
            }
        }
        return $player;
    }

    public function updatePlayerGuild(Guild $guild, array $arrayDataPlayer, bool $characters = false, bool $ships = false)
    {
        $player = $this->updatePlayer($arrayDataPlayer, $characters, $ships);
        $player->setGuild($guild);
        $this->_entityManager->persist($player);
        $this->_entityManager->flush();
    }

    public function updatePlayerByApi(int $allyCode, bool $characters = false, bool $ships = false)
    {
        $playerDatas = $this->_swgoh->fetchPlayer($allyCode);
        if (is_array($playerDatas)) {
            $this->updatePlayer($playerDatas, $characters, $ships);
            return true;
        }
        return false;
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