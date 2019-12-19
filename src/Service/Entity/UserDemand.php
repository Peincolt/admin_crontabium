<?php

namespace App\Service\Entity;

use Exception;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Data\Helper as DataHelper;
use App\Entity\UserDemand as UserDemandEntity;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDemand
{

    private $dataHelper;
    private $entityManagerInterface;
    private $passwordEncoder;

    public function __construct(DataHelper $dataHelper,
        EntityManagerInterface $entityManagerInterface,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->dataHelper = $dataHelper;
        $this->entityManagerInterface = $entityManagerInterface;
        $this->passwordEncoder = $passwordEncoder;
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

    public function transformDemandToAccount(array $ids)
    {
        try {
            foreach($ids as $id) {
                $demand = $this->dataHelper->getDatabaseData("App\Entity\UserDemand",array('id' => $id));
                $user = new User();
                $user->setUsername($demand->getUsername())
                    ->setPassword($this->passwordEncoder
                        ->encodePassword($user,$demand->getPassword()))
                    ->setEmail($demand->getEmail())
                    ->setRoles(array('ROLE_'.$demand->getRoles()));
                $this->entityManagerInterface->persist($user);
                $this->entityManagerInterface->flush();
            }
            return 200;
        } catch (Exception $e) {
            $arrayReturn['error_message'] = $e->getMessage();
            $arrayReturn['error_code'] = 404;
            return $arrayReturn;
        }
    }

    public function hashPassword(User $user, $password)
    {
        return $this->passwordEncoder->encodePassword($user, $password);
    }
}
