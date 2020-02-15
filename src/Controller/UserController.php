<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ModifUserType;
use Symfony\Component\Form\FormError;
use App\Service\Entity\User as UserHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $userHelper;

    public function __construct(UserHelper $userHelper)
    {
        $this->userHelper = $userHelper;
    }

    /**
     * @Route("/user/create", name="security_creation_user")
     */
    public function createUser(Request $request, User $user = null, $id = null)
    {
        if (empty($user)) {
            $user = new User();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->userHelper->updateUser($user);
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

        return $this->render('user/create.html.twig',[
            'formUser' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/edit/{id}", name="user_edit")
     */
    public function editUser(User $user = null, $id = null, Request $request)
    {
        if (empty($user)) {
            $this->addFlash('error','L\'utilisateur que vous souhaitez modifier n\'existe pas');
            return $this->redirectToRoute('home');
        }

        $currentUser = $this->getUser();
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        if (!$hasAccess) {
            if (!$currentUser || $currentUser->getId() != $id) {
                $this->addFlash('error','Il faut être connecté et admin pour pouvoir modifier le compte d\'un autre utilisateur');
                return $this->redirectToRoute('home');
            }
        }

        $form = $this->createForm(ModifUserType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->userHelper->updateUser($user);
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

        return $this->render('user/edit.html.twig',[
            'formUser' => $form->createView()
        ]);
    }

}
