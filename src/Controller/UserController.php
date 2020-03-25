<?php

namespace App\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Service\Entity\User as UserHelper;
use App\Entity\User;
use App\Form\UserType;

class UserController extends AbstractController
{
    private $userHelper;

    public function __construct(UserHelper $userHelper)
    {
        $this->userHelper = $userHelper;
    }

    /* ADMIN PART */

    /**
     * @Route("/user/list", name="security_user_list")
     */
    public function list()
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('user/list.html.twig',[
            'users' => $users
        ]);
    }

    /**
     * @Route("/user/create", name="security_creation_user")
     * @Route("/user/edit/{id}", name="user_edit")
     */
    public function edit(Request $request, User $user = null, $id = null)
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN');

        // Si jamais le parameter converter n'a rien donné
        if (empty($user)) {
            // Et si jamais on a pas d'id
            if (empty($id)) {
                // C'est qu'on essaie de créer un compte. Si on est pas admin, on met un message d'erreur
                if (!$hasAccess) {
                    $this->addFlash('error','Il faut être administrateur pour pouvoir créer un compte');
                    return $this->redirectToRoute('home');
                // Si on est admin, on instancie notre objet User
                } else {
                    $user = new User();
                    $new = true;
                }
                // Si jamais on a le paramètre id mais qu'on a pas de User, c'est que le comtpe avec l'id en question n'existe pas
            } else {
                // On notifie l'utilisateur et on le renvoie vers la home page
                $this->addFlash('error','Le compte que vous essayez de modifier n\'existe pas');
                    return $this->redirectToRoute('home');
            }
        // Si l'auto wiring a bien fonctionné, c'est qu'on veut modifier un utilisateur
        } else {
            $new = false;
            // On récupére l'user courant
            $currentUser = $this->getUser();
            // Si les deux ids sont différents
            if ($currentUser->getId() != $id) {
                // On regarde si l'utilisateur courant est admin
                if (!$hasAccess) {
                    // Si c'est pas le cas, il n'a pas le droit de modifier le compte d'un autre donc on le redirige vers la homepage
                    $this->addFlash('error','Il faut être admin pour pouvoir modifier le compte d\'un autre utilisateur');
                    return $this->redirectToRoute('home');
                }
            }
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('password')->getData()) {
                $result = $this->userHelper->updatePassword($user,$form->get('password')->getData());
            } else {
                $result = $this->userHelper->updateUser($user);
            }

            if (!isset($result['error_message'])) {
                if ($new) {
                    $this->addFlash('success','Le compte a été crée avec succés');
                } else {
                    $this->addFlash('success','Le compte a été modifié avec succés');
                }
                return $this->redirectToRoute('home');
            } else {
                if (isset($result['error_forms'])) {
                    foreach($result['error_forms'] as $key => $value) {
                        $form->get($key)->addError(new FormError($value));
                    }
                }
                $this->addFlash('error',$result['error_message']);
            }
        }

        return $this->render('user/edit.html.twig',[
            'formUser' => $form->createView(),
            'user' => $user
        ]);
    }

    /* AJAX PART */

    /**
     * @Route("/ajax/user/isFieldTaken", name="ajax_user_field_taken")
     * @Method({"POST"})
     */
    public function isUsernameTake(Request $request, UserHelper $userHelper)
    {
        $field = $request->request->get('field');
        $value = $request->request->get('value');

        if (empty($field) || empty($value)) {
            return new JsonResponse(false);
        }

        return new JsonResponse($userHelper->isFieldTaken($field,$value));
    }
}
