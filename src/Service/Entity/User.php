<?php

namespace App\Service\Entity;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserDemand;

class User 
{

    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function createUser (User $user)
    {
        return $this->updateAccount($user,'User');
    }

    public function isPasswordCorrect(string $password)
    {
        $uppercase = preg_match("#[A-Z]+#",$password);
        $digit = preg_match("#[0-9]+#",$password);
        $lowercase = preg_match("#[a-z]+#",$password);
        $specialChar = preg_match("#\W+#",$password);
        return ($uppercase && $digit && $lowercase && $specialChar);
    }
}