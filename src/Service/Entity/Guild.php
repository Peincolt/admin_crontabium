<?php

namespace App\Service\Entity;

use Exception;
use App\Entity\Player;
use App\Service\Api\SwgohGg;
use App\Service\Data\Helper;
use App\Entity\Guild as EntityGuild;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;

class Guild
{
    private $_swgohGg;
    private $_playerHelper;
    private $_dataHelper;
    private $_entityManagerInterface;
    private $_playerRepository;

    public function __construct(
        SwgohGg $swgohGg, 
        PlayerHelper $playerHelper, 
        Helper $dataHelper, 
        EntityManagerInterface $entityManagerInterface,
        PlayerRepository $playerRepository
    )
    {
        $this->_swgohGg = $swgohGg;
        $this->_playerHelper = $playerHelper;
        $this->_dataHelper = $dataHelper;
        $this->_entityManagerInterface = $entityManagerInterface;
        $this->_playerRepository = $playerRepository;
    }

    public function updateGuild(string $idGuild,EntityGuild $guild = null)
    {
        if (empty($guild)) {
            $guild = new EntityGuild();
            $this->_entityManagerInterface->persist($guild);
        }

        try {
            $dataGuild = $this->_swgohGg->fetchGuild($idGuild);
            $this->_dataHelper->fillObject($dataGuild['data'], 'guild', $guild);
            $this->_entityManagerInterface->flush();
            return $dataGuild;
        } catch (Exception $e) {
            var_dump($e->getMessage());
            die('gzgqegq');
            return false;
        }
    }

    public function updateGuildPlayers(array $dataGuild, bool $characters = false, bool $ships = false)
    {
        $arrayActualMembers = array();
        $guild = $this->_entityManagerInterface
            ->getRepository(EntityGuild::class)
            ->findOneBy(
                [
                    'id_swgoh' => $dataGuild['data']['guild_id']
                ]
            );
        foreach ($dataGuild['data']['members'] as $guildPlayerData) {
            array_push($arrayActualMembers, $guildPlayerData['player_name']);
            $playerData = $this->_swgohGg->fetchPlayer(
                $guildPlayerData['ally_code']
            );
            $this->_playerHelper->updatePlayerGuild(
                $guild,
                $playerData,
                $characters,
                $ships
            );
        }
        $playersOut = $this->_playerRepository->getOldMembers(
            $guild,
            $arrayActualMembers
        );
        foreach ($playersOut as $player) {
            $this->_entityManagerInterface->remove($player);
        }
        $this->_entityManagerInterface->flush();
        return 200;
    }

    public function getFormGuild()
    {
        $arrayReturn = array();
        $guilds = $this->_entityManagerInterface
            ->getRepository('App\Entity\Guild')
            ->findAll();

        foreach ($guilds as $guild) {
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