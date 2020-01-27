<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Squad;
use App\Form\SquadType;
use App\Service\Entity\Squad as SquadHelper;

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
        return $this->render('squad/list.html.twig',[
            'squads' => $this->getDoctrine()
                ->getRepository(Squad::class)
                ->findAll()
        ]);
    }

    /**
     * @Route("/squad/create", name="squad_creation")
     * @Route("/squad/modify/{id}", name="squad_modification")
     */
    public function edit(Request $requet, Squad $squad = null, SquadHelper $squadHelper, $id = null)
    {
        if ($id) {
            $squad = $this->getDoctrine()
                ->getRepository(Squad::class)
                ->find($id);
            $dataSquad = $squadHelper->squadToForm($squad);
        } else {
            $squad = new Squad();
            $dataSquad = null;
        }

        $form = $this->createForm(SquadType::class,$dataSquad);
        $form->handleRequest($requet);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $result = $squadHelper->createSquad($squad,$data);
            if (isset($result['error_message'])) {
                $this->addFlash('error','An error occurred where we try to create your squad');
            } else {
                $this->addFlash('success','We create your squad');
                $this->redirectToRoute('home');
            }
        }

        return $this->render('squad/edit.html.twig',[
            'formChoice' => $form->createView(),
            'squad' => $squad
        ]);

    }

    /**
     * @Route("/squad/{id}", name="squad_view")
     */
    public function view(Squad $squad)
    {
        return $this->render('squad/view.html.twig',[
            'squad' => $squad
        ]);
    }
}
