<?php

namespace App\Controller;

use App\Entity\UserDemand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\UserDemandType;
use App\Service\Entity\User;

class UserController extends AbstractController
{
    /*private $userHelper;

    public function __construct(UserHelper $userHelper)
    {
        $this->userHelper = $userHelper;
    }*/

    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/demand-access", name="security_demand_access")
     */
    public function userDemand(Request $request)
    {
        $user = new UserDemand();
        $form = $this->createForm(UserDemandType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->userHelper->updateUser($user, $form->getData(),'demand');
            if (!isset($result['error_message'])) {
                $this->addFlash('success','We register your demand. We will send you an email to tell you if you can acces or not to the website');
                return $this->redirectToRoute('security_login');
            } else {
                $this->addFlash('error','An error occured while we\'re trying to save your demand. Please try again or contact an admin if the error persist');
            }
        }

        return $this->render('user/demand.html.twig',[
            'formDemand' => $form->createView()
        ]);
    }
}
