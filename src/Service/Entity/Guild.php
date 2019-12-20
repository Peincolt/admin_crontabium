<?php

namespace App\Service\Entity;

use App\Entity\Guild as EntityGuild;
use App\Service\Api\SwgohGg;
use App\Service\Data\Helper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class Guild
{
    private $swgohGg;
    private $playerHelper;
    private $dataHelper;
    private $entityManagerInterface;

    public function __construct(SwgohGg $swgohGg, PlayerHelper $playerHelper, Helper $dataHelper, EntityManagerInterface $entityManagerInterface)
    {
        $this->swgohGg = $swgohGg;
        $this->playerHelper = $playerHelper;
        $this->dataHelper = $dataHelper;
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function updateGuild(string $idGuild, array $options = null)
    {
        try {
            $dataGuild = $this->swgohGg->fetchGuild($idGuild);

            if (!isset($dataGuild['error_message'])) {
                if (!$guild = $this->dataHelper->getDatabaseData("\App\Entity\Guild", array('id_swgoh' => $dataGuild['data']['id']))) {
                    $guild = new EntityGuild();
                }
        
                $entityField = $this->dataHelper->matchEntityField('guild',$dataGuild['data']);
        
                foreach($entityField as $key => $value) {
                    $function = 'set'.$key;
                    $guild->$function($value);
                }
        
                $this->entityManagerInterface->persist($guild);
                $this->entityManagerInterface->flush();
        
                if (isset($options['players'])) {
        
                    if (isset($options['players_heroes'])) {
                        $heros = true;
                    } else {
                        $heros = false;
                    }
        
                    if (isset($options['players_ships'])) {
                        $ships = true;
                    } else {
                        $ships = false;
                    }
        
                    for ($i=0;$i<count($dataGuild['players']);$i++) {
                        $result = $this->playerHelper->createPlayer($dataGuild['players'][$i]['data']['ally_code'],$heros,$ships,$dataGuild['players'][$i],$guild);
                        if (isset($result['error_code'])) {
                        break;
                        return $result;
                        }
                    }
                }
                return array('message' => 'The synchronization is over', 'status' => 200);
            }
            return $dataGuild;
        } catch (Exception $e) {
            $arrayReturn['error_code'] = $e->getCode();
            $arrayReturn['error_message'] = $e->getMessage();
            return $arrayReturn;
        }
    }

    public function getFormGuild()
    {
        $arrayReturn = array();
        $guilds = $this->entityManagerInterface
            ->getRepository('App\Entity\Guild')
            ->findAll();

        foreach($guilds as $guild) {
            $arrayReturn[$guild->getName()] = $guild->getId();
        }

        return $arrayReturn;
    }

    public function getHeroesGalacticalPower(EntityGuild $guild)
    {
        $galacticalPower = 0;
        $players = $guild->getPlayers();
        foreach ($players as $player) {
            $galacticalPower+= intval($player->getCharactersGalacticalPuissance());
        }
        return $galacticalPower;
    }

    public function getShipsGalacticalPower(EntityGuild $guild)
    {
        $galacticalPower = 0;
        $players = $guild->getPlayers();
        foreach ($players as $player) {
            $galacticalPower+= intval($player->getShipsGalacticalPuissance());
        }
        return $galacticalPower;
    }


}