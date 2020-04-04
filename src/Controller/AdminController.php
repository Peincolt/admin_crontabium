<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Entity\Guild;
use Knp\Component\Pager\PaginatorInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(Guild $guildHelper, PaginatorInterface $paginatorInterface)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $guild = $entityManager->getRepository("App\Entity\Guild")->findOneBy(['id' => 1]);
        $playersEntity = $entityManager->getRepository("App\Entity\Player")->findBy(['guild' => $guild]);
        $players = $paginatorInterface->paginate($playersEntity,1,50);
        return $this->render('admin/index.html.twig', [
            'guild' => $guild,
            'players' => $players,
            'guildHeroesGp' => $guildHelper->getHeroesGalacticalPower($guild),
            'guildShipsGp' => $guildHelper->getShipsGalacticalPower($guild)
        ]);
    }
}
