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
    private $swgohGg;
    private $playerHelper;
    private $dataHelper;
    private $entityManagerInterface;
    private $playerRepository;

    public function __construct(
        SwgohGg $swgohGg, 
        PlayerHelper $playerHelper, 
        Helper $dataHelper, 
        EntityManagerInterface $entityManagerInterface,
        PlayerRepository $playerRepository
    )
    {
        $this->swgohGg = $swgohGg;
        $this->playerHelper = $playerHelper;
        $this->dataHelper = $dataHelper;
        $this->entityManagerInterface = $entityManagerInterface;
        $this->playerRepository = $playerRepository;
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
            return false;
        }
    }

    public function updateGuildPlayers(array $dataGuild, bool $characters = false, bool $ships = false)
    {
        $arrayActualMembers = array();
        $guild = $this->entityManagerInterface->getRepository(EntityGuild::class)->findOneBy(['id_swgoh' => $dataGuild['data']['id']]);
        foreach ($dataGuild['players'] as $playerData) {
            array_push($arrayActualMembers,$playerData['data']['name']);
            $this->playerHelper->updatePlayerGuild($guild,$playerData,$characters,$ships);
        }
        $playersOut = $this->playerRepository->getOldMembers($guild,$arrayActualMembers);
        foreach($playersOut as $player) {
            $this->entityManagerInterface->remove($player);
        }
        $this->entityManagerInterface->flush();
        return 200;
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