<?php

namespace App\Service\Entity;

class User 
{
    public function isPasswordCorrect(string $password)
    {
        $uppercase = preg_match("#[A-Z]+#",$password);
        $digit = preg_match("#[0-9]+#",$password);
        $lowercase = preg_match("#[a-z]+#",$password);
        $specialChar = preg_match("#\W+#",$password);
        return ($uppercase && $digit && $lowercase && $specialChar);
    }
}