<?php

namespace App\Service\Entity;

use App\Service\Data\Helper as DataHelper;
use App\Entity\UserDemand as UserDemandEntity;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class UserDemand
{

    private $dataHelper;
    private $entityManagerInterface;

    public function __construct(DataHelper $dataHelper, EntityManagerInterface $entityManagerInterface)
    {
        $this->dataHelper = $dataHelper;
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function createUserDemand(UserDemandEntity $user)
    {
        $arrayReturn = array();
        $entityName = "\App\Entity\UserDemand";
        $createdAt = new \DateTime();

        $userDemand = $this->dataHelper
            ->getDatabaseData($entityName,array('email' => $user->getEmail()));
        if ($userDemand) {
            $arrayReturn['error_message'] = 'Someone has already tried to do a request with this email';
            return $arrayReturn;
        }

        if (!$this->dataHelper->isPasswordCorrect($user->getPassword())) {
            $arrayReturn['error_message'] = 'Your password must contains at least 8 characters composed of 1 digit, 1 capital letter and 1 special char';
            $arrayReturn['error_forms']['password'] = 'Your password must contains at least 8 characters composed of 1 digit, 1 capital letter and 1 special char';
            return $arrayReturn;
        }

        $user->setCreatedAt($createdAt);

        try {
            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
            return 200;
        } catch (Exception $e) {
            $arrayReturn['error_message'] = 'An error occured while we\'re saving your demand. Please try later or contact an admin if the error persists';
        }
    }

    public function transformDemandToAccount(int $idDemand)
    {

    }
}
