<?php

namespace App\Controller;

use App\Entity\Player;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Entity\Guild;
use App\Service\phpspreadsheet\GeneratePdf;
use Knp\Component\Pager\PaginatorInterface;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="home_test")
     */
    public function home(GeneratePdf $generatePdf)
    {
        //$var = "AB";
        //$var+=4;
        //var_dump($var);

        $generatePdf->constructSpreadShit(3);
        die('oklm');

        /*$entityManager = $this->getDoctrine()->getManager();
        $guild = $entityManager->getRepository("App\Entity\Guild")->findOneBy(['id' => 1]);
        $playersEntity = $entityManager->getRepository("App\Entity\Player")->findBy(['guild' => $guild]);
        $players = $paginatorInterface->paginate($playersEntity,1,50);
        return $this->render('admin/index.html.twig', [
            'guild' => $guild,
            'players' => $players,
            'guildHeroesGp' => $guildHelper->getHeroesGalacticalPower($guild),
            'guildShipsGp' => $guildHelper->getShipsGalacticalPower($guild)
        ]);*/
    }
}
