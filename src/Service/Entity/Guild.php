<?php

namespace App\Service\Entity;

use Exception;
use App\Entity\Player;
use App\Service\Api\SwgohGg;
use App\Service\Data\Helper;
use App\Entity\Guild as EntityGuild;
use Doctrine\ORM\EntityManagerInterface;

class Guild
{
    private $swgohGg;
    private $playerHelper;
    private $dataHelper;
    private $entityManagerInterface;

    public function __construct(
        SwgohGg $swgohGg, 
        PlayerHelper $playerHelper, 
        Helper $dataHelper, 
        EntityManagerInterface $entityManagerInterface
    )
    {
        $this->swgohGg = $swgohGg;
        $this->playerHelper = $playerHelper;
        $this->dataHelper = $dataHelper;
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function updateGuild(string $idGuild,EntityGuild $guild = null)
    {
        if (empty($guild)) {
            $guild = new EntityGuild();
            $this->entityManagerInterface->persist($guild);
        }

        try {
            $dataGuild = $this->swgohGg->fetchGuild($idGuild);
            $this->dataHelper->fillObject($dataGuild['data'],'guild',$guild);
            $this->entityManagerInterface->flush();
            return $dataGuild;
        } catch (Exception $e) {
            $arrayReturn['error_code'] = $e->getCode();
            $arrayReturn['error_message'] = $e->getMessage();
            return $arrayReturn;
        }
    }

    public function updateGuildPlayers(array $dataGuild, bool $characters = false, bool $ships = false)
    {
        $arrayActualMembers = array();
        $guild = $this->entityManagerInterface->getRepository(EntityGuild::class)->findOneBy(['id_swgoh' => $dataGuild['data']['id']]);
        foreach ($dataGuild['players'] as $playerData) {
            array_push($arrayActualMembers,$playerData['data']['name']);
            //$this->playerHelper->updatePlayerGuild($guild,$playerData,$characters,$ships);
        }
        $playersOut = $this->entityManagerInterface->getRepository(EntityGuild::class)->deleteOldMembers($arrayActualMembers);
        return 200;
    }

    /*public function updateGuildPlayers(array $guild, bool $heroes, bool $ships)
    {
        $dataGuild = $this->swgohGg->fetchGuild($guild->getIdSwgoh());
        for ($i=0;$i<count($dataGuild['players']);$i++) {
            $result = $this->playerHelper->createPlayer($dataGuild['players'][$i]['data']['ally_code'],$heroes,$ships,$dataGuild['players'][$i],$guild);
            array_push($arrayPlayer,$dataGuild['players'][$i]['data']['name']);
            if (isset($result['error_code'])) {
                break;
            }
        }
    }*/

    public function updateGuild2(string $idGuild, array $options = null)
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
                    $arrayPlayer = array();
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
                        array_push($arrayPlayer,$dataGuild['players'][$i]['data']['name']);
                        if (isset($result['error_code'])) {
                            break;
                        }
                    }

                    $players = $guild->getPlayers();
                    foreach ($players as $player) {
                        if (!in_array($player->getName(),$arrayPlayer)) {
                            $this->entityManagerInterface
                                ->remove($player);
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

    public function getHeroesNumber(EntityGuild $guild)
    {
        $heroesNumber = 0;
        $players = $guild->getPlayers();
        foreach ($players as $player) {
            $heroesNumber+= intval(count($player->getCharacters()));
        }
        return $heroesNumber;
    }

    public function getShipsNumber(EntityGuild $guild)
    {
        $shipsNumber = 0;
        $players = $guild->getPlayers();
        foreach ($players as $player) {
            $shipsNumber+= intval(count($player->getShips()));
        }
        return $shipsNumber;
    }


}