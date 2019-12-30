<?php

namespace App\Controller;

use App\Entity\Squad;
use App\Form\SquadType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SquadController extends AbstractController
{
    /**
     * @Route("/squad", name="squad")
     */
    public function index()
    {
        return $this->render('squad/index.html.twig', [
            'controller_name' => 'SquadController',
        ]);
    }

    /**
     * @Route("/squads", name="squad_list")
     */
    public function list()
    {
        return $this->render('squad/list.html.twig');
    }

    /**
     * @Route("/squad/create", name="squad_creation")
     * @Route("/squad/modify/{id}", name="squad_modification")
     */
    public function edit(Request $requet, Squad $squad)
    {
        $form = $this->createForm(SquadType::class,$squad);
        $form->handleRequest($requet);

        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->render('squad/edit.html.twig');

    }
}
