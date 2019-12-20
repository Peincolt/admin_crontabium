<?php

namespace App\Controller;

use App\Service\Entity\Guild as GuildHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Player;

class PlayerController extends AbstractController
{
    /**
     * @Route("/players", name="home")
     */
    public function players(GuildHelper $guildHelper)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $guild = $entityManager->getRepository("App\Entity\Guild")->findOneBy(['id' => 1]);
        $players = $guild->getPlayers();
        return $this->render('player/list.html.twig', [
            'guildMembers' => $guild->getMembers(),
            'players' => $players,
            'guildHeroesGp' => $guildHelper->getHeroesGalacticalPower($guild),
            'guildShipsGp' => $guildHelper->getShipsGalacticalPower($guild),
            'guildNumberHeroes' => $guildHelper->getHeroesNumber($guild),
            'guildNumberShips' => $guildHelper->getShipsNumber($guild)
        ]);
    }

    /**
     * @Route("/player/{id}", name="player_viewer")
     */
    public function player(Player $player)
    {
        
    }
}
