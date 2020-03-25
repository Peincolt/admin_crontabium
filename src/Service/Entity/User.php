<?php

namespace App\Service\Entity;

use Exception;
use App\Entity\User as UserEntity;
use App\Entity\UserDemand;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Entity\Security as SecurityHelper;

class User 
{

    private $entityManagerInterface;
    private $securityHelper;

    public function __construct(EntityManagerInterface $entityManagerInterface, SecurityHelper $securityHelper)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->securityHelper = $securityHelper;
    }

    public function isFieldTaken(string $field, string $value, $demand = false)
    {
        $user = $this->entityManagerInterface
            ->getRepository(UserEntity::class)
            ->findBy([$field => $value]);

        if (!empty($user)) {
            return array('error_message' => $this->translate($field).' est déjà utilisé. Veuillez en choisir un autre','block' => true, 'field' => $field);
        } else {
            if ($demand) {
                $userDemand = $this->entityManagerInterface
                    ->getRepository(UserDemand::class)
                    ->findBy([$field => $value]);

                if (!empty($userDemand)) {
                    return array('error_message' => $this->translate($field).' est déjà utilisé dans une demande de compte. Veuillez en choisir un autre','block' => true, 'field' => $field);
                }
            }
            return array('field' => $field);
        }
    }

    public function translate($field)
    {
        switch ($field) {
            case 'username' :
                return "Ce nom d'utilisateur";
            break;

            case 'email' :
                return 'Cette adresse mail';
            break;
        }
    }

    public function updateUser(UserEntity $user)
    {
        $this->entityManagerInterface->persist($user);
        $this->entityManagerInterface->flush();
        return 200;
    }

    public function updatePassword(UserEntity $user, $password) {
        if ($password) {
            if (!$this->securityHelper->isPasswordCorrect($password)) {
                $arrayReturn['error_message'] = 'Your password must contains at least 8 characters composed of 1 digit, 1 capital letter and 1 special char';
                $arrayReturn['error_forms']['password'] = 'Your password must contains at least 8 characters composed of 1 digit, 1 capital letter and 1 special char';
                return $arrayReturn;
            }
        }

        try {
            $hashPassword = $this->securityHelper->hashPassword($user,$password);
            $user->setPassword($hashPassword);
            return $this->updateUser($user);
        } catch (Exception $e) {
            $arrayReturn['error_message'] = 'Une erreur est survenue lors de la sauvegarde des informations. Si l\'erreur persist, veuillez contacter Peincolt';
            return $arrayReturn['error_message'];
        }
    }
}