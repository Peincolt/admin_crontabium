<?php

namespace App\Service\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Service\Data\Helper as DataHelper;
use App\Entity\UserDemand as UserDemandEntity;
use App\Entity\User;
use App\Service\Entity\Security as SecurityHelper;

class UserDemand
{

    private $dataHelper;
    private $entityManagerInterface;
    private $securityHelper;

    public function __construct(DataHelper $dataHelper,
        EntityManagerInterface $entityManagerInterface,
        SecurityHelper $securityHelper
    )
    {
        $this->dataHelper = $dataHelper;
        $this->entityManagerInterface = $entityManagerInterface;
        $this->securityHelper = $securityHelper;
    }

    public function createUserDemand(UserDemandEntity $user)
    {
        $arrayReturn = array();
        $entityName = "\App\Entity\UserDemand";
        $createdAt = new \DateTime();

        $userDemand = $this->dataHelper
            ->getDatabaseData($entityName,array('email' => $user->getEmail()));
        if ($userDemand) {
            $arrayReturn['error_message'] = 'Quelqu\'un a déjà crée un compte avec cette adresse mail';
            return $arrayReturn;
        }

        if (!$this->securityHelper->isPasswordCorrect($user->getPassword())) {
            $arrayReturn['error_message'] = 'Votre mot de passe doit contenir au moins 8 caractéres dont une lettre majuscule, un chiffre et un caractére spécial';
            $arrayReturn['error_forms']['password'] = 'Votre mot de passe doit contenir au moins 8 caractéres dont une lettre majuscule, un chiffre et un caractére spécial';
            return $arrayReturn;
        }

        $user->setCreatedAt($createdAt);

        try {
            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
            return 200;
        } catch (Exception $e) {
            $arrayReturn['error_message'] = 'Une erreur est survenue lors de la sauvegarde de votre demande de compte. Veuillez réessayer plus tard';
        }
    }

    public function transformDemandToAccount(array $ids)
    {
        try {
            foreach($ids as $id) {
                $demand = $this->dataHelper->getDatabaseData("App\Entity\UserDemand",array('id' => $id));
                $user = new User();
                $user->setUsername($demand->getUsername())
                    ->setPassword($this->securityHelper
                        ->hashPassword($user,$demand->getPassword()))
                    ->setEmail($demand->getEmail())
                    ->setRoles('ROLE_'.$demand->getRole());
                $this->entityManagerInterface->persist($user);
                $this->entityManagerInterface->remove($demand);
                $this->entityManagerInterface->flush();
            }
            return 200;
        } catch (Exception $e) {
            $arrayReturn['error_message'] = $e->getMessage();
            $arrayReturn['error_code'] = 404;
            return $arrayReturn;
        }
    }
}
