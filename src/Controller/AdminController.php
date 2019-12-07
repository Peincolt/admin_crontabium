<?php

namespace App\Controller;

use App\Service\Entity\PlayerHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(PlayerHelper $playerHelper)
    {
        $dataPlayer = $playerHelper->createPlayer('246639295',false,false);
        var_dump($dataPlayer);
        die('ok');
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
