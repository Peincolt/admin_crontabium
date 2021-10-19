<?php

namespace App\Service\Entity;

use Exception;
use App\Entity\Guild;
use App\Entity\Player;
use App\Service\Api\SwgohGg;
use App\Service\Data\Helper;
use App\Service\Entity\PlayerUnit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class PlayerHelper {
    
    private $swgoh;
    private $dataHelper;
    private $entityManager;
    private $playerUnit;

    public function __construct(
        SwgohGg $swgoh, 
        Helper $dataHelper, 
        EntityManagerInterface $entityManager, 
        PlayerUnit $playerUnit, 
        AdapterInterface $adapterInterface
    ) 
    {
        $this->swgoh = $swgoh;
        $this->dataHelper = $dataHelper;
        $this->entityManager = $entityManager;
        $this->playerUnit = $playerUnit;
    }

    public function updatePlayers(array $guildData, bool $characters = false, bool $ships = false)
    {
        $guild = $this->entityManager->getRepository(Guild::class)->findOneBy(['id_swgoh' => $guildData['id_swgoh']]);
        $players = $guildData['players'];
        foreach ($players as $arrayDataPlayer)
        {
            if (!($player = $this->dataHelper->getDatabaseData("\App\Entity\Player",array('ally_code' => $arrayDataPlayer['data']['ally_code'])))) {
                $player = new Player();
            }
            $entityField = $this->dataHelper->matchEntityField('player',$arrayDataPlayer['data']); // Voir pour faire une classe générique qui a cette fonction. Elle est utilisée partout.
            foreach($entityField as $key => $value) {
                $function = 'set'.$key;
                $player->$function($value);
            }

            $player->setGuild($guild);

            $this->entityManager->persist($player);
            if (empty($player->getId())) { // Voir pour faire l'auto pesrsist
                $this->entityManager->flush();
            }

            if ($characters || $ships) {
                foreach($arrayDataPlayer['units'] as $key => $value)
                {
                    switch ($value['data']['combat_type']) {
                        case 1:
                            if ($characters) {
                                $this->playerUnit->createPlayerHero($value['data'],$player);
                            }
                        break;
                        case 2:
                            if ($ships) {
                                $this->playerUnit->createPlayerShip($value['data'],$player);
                            }
                        break;
                    }
                }
            }
            return 200;
        }
    }

    public function createPlayer(int $allyCode, bool $characters = false, bool $ships = false)
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