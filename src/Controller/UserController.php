<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Service\Entity\UserDemand as UserDemandHelper;

class UserController extends AbstractController
{

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
     * @Route("/user/create", name="security_creation_user")
     * @Route("/user/update/{id}", name="security_update_iser")
     * @Route("/user-demand/transform/{id}", name="security_transform_demand")
     */
    public function createUser(int $id = null, User $user = null)
    {

    }
 
}
