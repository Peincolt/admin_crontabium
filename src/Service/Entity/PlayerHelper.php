<?php

namespace App\Service\Entity;

use Exception;
use App\Entity\Guild;
use App\Entity\Player;
use App\Service\Api\SwgohGg;
use App\Service\Data\Helper;
use App\Service\Entity\PlayerUnit;
use Doctrine\ORM\EntityManagerInterface;

class PlayerHelper {
    
    private $swgoh;
    private $dataHelper;
    private $entityManager;
    private $playerUnit;

    public function __construct(
        SwgohGg $swgoh, 
        Helper $dataHelper, 
        EntityManagerInterface $entityManager, 
        PlayerUnit $playerUnit
    ) 
    {
        $this->swgoh = $swgoh;
        $this->dataHelper = $dataHelper;
        $this->entityManager = $entityManager;
        $this->playerUnit = $playerUnit;
    }

    public function updatePlayers(array $dataGuild, bool $characters = false, bool $ships = false)
    {
        $guild = $this->entityManager->getRepository(Guild::class)->findOneBy(['id_swgoh' => $dataGuild['id']]);
        $players = $dataGuild['players'];
        foreach ($players as $arrayDataPlayer)
        {
            $this->updatePlayerByJson($arrayDataPlayer['data']['ally_code'],$characters,$ships,$guild);
        }
    }

    public function updatePlayer(array $arrayDataPlayer, bool $characters = false, bool $ships = false)
    {
        if (!($player = $this->entityManager->getRepository(Player::class)->findOneBy(['ally_code' => $arrayDataPlayer['data']['ally_code']]))) {
            $player = new Player();
            $this->entityManager->persist($player);
        }
        $this->dataHelper->fillObject($arrayDataPlayer,'player',$player);
        $this->entityManager->flush();
        if ($characters || $ships) {
            foreach($arrayDataPlayer['units'] as $unit)
            {
                switch ($unit['data']['combat_type']) {
                    case 1:
                        if ($characters) {
                            $this->playerUnit->createPlayerHero($unit['data'],$player);
                        }
                    break;
                    case 2:
                        if ($ships) {
                            $this->playerUnit->createPlayerShip($unit['data'],$player);
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
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /*public function updatePlayerByApi(int $allyCode, bool $characters = false, bool $ships = false)
    {
        if (!$playerDatas) {
            $playerDatas = $this->swgoh->fetchPlayer($allyCode);
        }

        if (!isset($playerDatas['error_message'])) {
            $guild = $this->entityManager->getRepository(Guild::class)->findOneBy(['id_swgoh' => $test]);
            try {
                $entityField = $this->dataHelper->matchEntityField('player',$playerDatas);
                if (!($player = $this->dataHelper->getDatabaseData("\App\Entity\Player",array('ally_code' => $allyCode)))) {
                    $player = new Player();
                }
                foreach($entityField as $key => $value) {
                    $function = 'set'.$key;
                    $player->$function($value);
                }
    
                if (!empty($guild)) {
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
    }*/

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