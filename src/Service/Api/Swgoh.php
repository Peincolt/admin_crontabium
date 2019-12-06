<?php

namespace App\Service\Api;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Swgoh {

    private $user;
    private $mdp;
    private $grant_type;
    private $client_id;
    private $client_secret;
    private $httpClient;

    public function __construct($user,$mdp,$grant_type,$client_id,$client_secret, HttpClientInterface $httpClient)
    {
        $this->user = $user;
        $this->mdp = $mdp;
        $this->grant_type = $grant_type;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->httpClient = $httpClient;
    }

    public function login()
    {
        try {
            $response = $this->httpClient->request("POST","https://api.swgoh.help/auth/signin",[
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'body' => [
                    'username' => $this->user,
                    'password' => $this->mdp,
                    'grant_type'=> $this->grant_type,
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret
                ]
            ]);

            $responseContent = $response->toArray();

            file_put_contents('./tokenlocation', $responseContent['access_token']);
            return $responseContent['access_token'];

        } catch (Exception $e) {
            return 404;
        }
    }
}