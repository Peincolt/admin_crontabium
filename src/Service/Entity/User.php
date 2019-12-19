<?php

namespace App\Service\Entity;

use Exception;
use App\Entity\User as UserEntity;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Data\Helper as DataHelper;

class User 
{

    private $entityManagerInterface;
    private $dataHelper;

    public function __construct(EntityManagerInterface $entityManagerInterface, DataHelper $dataHelper)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->dataHelper = $dataHelper;
    }

    public function updateUser(UserEntity $user)
    {
        if (!$this->dataHelper->isPasswordCorrect($user->getPassword())) {
            $arrayReturn['error_message'] = 'Your password must contains at least 8 characters composed of 1 digit, 1 capital letter and 1 special char';
            $arrayReturn['error_forms']['password'] = 'Your password must contains at least 8 characters composed of 1 digit, 1 capital letter and 1 special char';
            return $arrayReturn;
        }

        $user->setPassword($this->dataHelper->hashPassword($user, $user->getPassword()));

        try {
            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
            return 200;
        } catch (Exception $e) {
            $arrayReturn['error_message'] = 'An error occured while we\'re saving your demand. Please try later or contact an admin if the error persists';
        }
    }
}