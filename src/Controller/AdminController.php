<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Entity\Guild;
use App\Service\Entity\PlayerHelper;
use App\Service\Entity\Guild as GuildHelper;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(PlayerHelper $playerHelper)
    {
        $tab['form_error']['password'] = 'test';
        $tab['form_error']['test'] = 'test2';
        
        foreach($tab['form_error'] as $key => $value) {
            var_dump($key);
            var_dump($value);
        }
    }

    /**
     * @Route("/", name="home")
     */
    public function home(Guild $guildHelper)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $guild = $entityManager->getRepository("App\Entity\Guild")->findOneBy(['id' => 1]);

        $players = $guild->getPlayers();
        return $this->render('admin/index.html.twig', [
            'guild' => $guild,
            'players' => $players,
            'guildHeroesGp' => $guildHelper->getHeroesGalacticalPower($guild),
            'guildShipsGp' => $guildHelper->getShipsGalacticalPower($guild)
        ]);
    }
}
