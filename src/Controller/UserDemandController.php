<?php

namespace App\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\UserDemand;
use App\Form\UserDemandType;
use App\Service\Entity\UserDemand as userDemandHelper;

class UserDemandController extends AbstractController
{

    private $userDemandHelper;

    public function __construct(UserDemandHelper $userDemandHelper)
    {
        $this->userDemandHelper = $userDemandHelper;
    }

    /**
     * @Route("/user/demand", name="user_demand")
     */
    public function index()
    {
        return $this->render('user_demand/index.html.twig', [
            'controller_name' => 'UserDemandController',
        ]);
    }

    /**
     * @Route("/demand-access", name="user_demand_access")
     */
    public function userDemand(Request $request)
    {
        $userDemand = new UserDemand();
        $form = $this->createForm(UserDemandType::class, $userDemand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->userDemandHelper->createUserDemand($userDemand);
            if (!isset($result['error_message'])) {
                $this->addFlash('success','We register your demand. We will send you an email to tell you if you can acces or not to the website');
                return $this->redirectToRoute('security_login');
            } else {
                if (isset($result['error_forms'])) {
                    foreach($result['error_forms'] as $key => $value) {
                        $form->get($key)->addError(new FormError($value));
                    }
                }
                $this->addFlash('error','An error occured while we\'re trying to save your demand. Please try again or contact an admin if the error persist');
            }
        }

        return $this->render('user/demand.html.twig',[
            'formDemand' => $form->createView()
        ]);
    }

    /**
     * @Route("/demand-access/transform", name="user_demand_transform")
     */
    public function transformDemand(Request $request)
    {
        $ids = $request->request->get('ids');
        if ($ids) {
            $result = $this->userDemandHelper->transformDemandToAccount($ids);
            if (isset($result['error_message'])) {
                var_dump($result['error_message']);
            }
        }
        die('oklm');
        return 404;
    }
}
