<?php

namespace App\Service\Entity;

use Exception;
use App\Entity\User as UserEntity;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Entity\Security as SecurityHelper;

class User 
{

    private $entityManagerInterface;
    private $securityHelper;

    public function __construct(EntityManagerInterface $entityManagerInterface, SecurityHelper $securityHelper)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->dataHelper = $securityHelper;
    }

    public function updateUser(UserEntity $user)
    {
        if (!$this->securityHelper->isPasswordCorrect($user->getPassword())) {
            $arrayReturn['error_message'] = 'Your password must contains at least 8 characters composed of 1 digit, 1 capital letter and 1 special char';
            $arrayReturn['error_forms']['password'] = 'Your password must contains at least 8 characters composed of 1 digit, 1 capital letter and 1 special char';
            return $arrayReturn;
        }

        $user->setPassword($this->securityHelper->hashPassword($user, $user->getPassword()));

        try {
            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
            return 200;
        } catch (Exception $e) {
            $arrayReturn['error_message'] = 'An error occured while we\'re saving your demand. Please try later or contact an admin if the error persists';
        }
    }
}