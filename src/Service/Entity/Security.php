<?php

namespace App\Service\Entity;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Security
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function isPasswordCorrect(string $password)
    {
        $uppercase = preg_match("#[A-Z]+#",$password);
        $digit = preg_match("#[0-9]+#",$password);
        $lowercase = preg_match("#[a-z]+#",$password);
        $specialChar = preg_match("#\W+#",$password);
        return ($uppercase && $digit && $lowercase && $specialChar);
    }

    public function hashPassword(User $user, $password)
    {
        return $this->passwordEncoder->encodePassword($user, $password);
    }


}