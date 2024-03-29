<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\HeroPlayer;
use App\Entity\ShipPlayer;
use App\Service\Entity\Unit as UnitHelper;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\Entity\Guild as GuildHelper;
use App\Service\Entity\PlayerHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlayerController extends AbstractController
{
    /**
     * @Route("/players", name="players_list")
     */
    public function players(GuildHelper $guildHelper, PlayerHelper $playerHelper, PaginatorInterface $paginatorInterface)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $guild = $entityManager->getRepository("App\Entity\Guild")->findOneBy(['id' => 1]);
        $playersEntity = $entityManager->getRepository("App\Entity\Player")->findBy(['guild' => $guild]);
        $players = $paginatorInterface->paginate($playersEntity,1,50);
        $playerFields = $playerHelper->getFields();
        return $this->render('player/list.html.twig', [
            'guild' => $guild,
            'players' => $players,
            'playerFields' => $playerFields,
            'guildHeroesGp' => $guildHelper->getHeroesGalacticalPower($guild),
            'guildShipsGp' => $guildHelper->getShipsGalacticalPower($guild),
            'guildNumberHeroes' => $guildHelper->getHeroesNumber($guild),
            'guildNumberShips' => $guildHelper->getShipsNumber($guild)
        ]);
    }

    /**
     * @Route("/player/{name}", name="player_viewer")
     */
    public function player(Player $player, $name = null, PaginatorInterface $paginatorInterface, UnitHelper $unitHelper)
    {
        $options['player'] = $player;
        $dqlPlayerHeroes = $this->getDoctrine()->getRepository(HeroPlayer::class)->findBy(['player' => $player]);
        $dqlPlayerShips = $this->getDoctrine()->getRepository(ShipPlayer::class)->findBy(['player' => $player]);
        if (count($dqlPlayerHeroes) > 0) {
            $options['heroesFields'] = $unitHelper->setFields('Hero');
            $options['playerHeroes'] = $paginatorInterface->paginate($dqlPlayerHeroes,1,500);
        }

        if (count($dqlPlayerShips) > 0) {
            $options['siphsFields'] = $unitHelper->setFields('Ship');
            $options['playerShips'] = $paginatorInterface->paginate($dqlPlayerShips,1,500);
        }

        return $this->render('player/player.html.twig',$options);
    }
}
