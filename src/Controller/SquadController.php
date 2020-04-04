<?php

namespace App\Controller;

use App\Entity\Squad;
use App\Form\SquadType;
use App\Service\Entity\Squad as SquadHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SquadController extends AbstractController
{
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
     * @Route("/squad/edit/{id}", name="squad_modification")
     */
    public function edit(Request $requet, Squad $squad = null, SquadHelper $squadHelper, $id = null)
    {
        if ($id) {
            if (empty($squad)) {
                $this->addFlash('error','L\'équipe que vous essayez d\'éditer n\'existe pas');
                return $this->redirectToRoute('squad_list');
            } else {
            $dataSquad = $squadHelper->squadToForm($squad);
            }
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
                $this->addFlash('error',$result['error_message']);
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
    public function view(Squad $squad, SquadHelper $squadHelper)
    {
        $squadInformations = $squadHelper->getPlayerSquadInformation($squad->getId());

        return $this->render('squad/view.html.twig',[
            'squad' => $squad,
            'playersInformations' => $squadInformations
        ]);
    }

    /**
     * @Route("/squad/hero/{id}", name="squad_test")
     */
    public function testSquad(SquadHelper $squadHelper)
    {
        $result = $squadHelper->getPlayerSquadInformation(1);

        var_dump($result);
        die('oklm');
    }
}
