<?php

namespace App\Service\Api;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class Swgoh {

    /**
     * Variable qui permettent de s'authentifier
     */
    private $user;
    private $mdp;
    private $grant_type;
    private $client_id;
    private $client_secret;
    private $httpClient;

    /**
     * Variable représentant les différentes urls de l'API
     */
    private $url_login;
    private $url_player;

    /**
     * Variable de cache
     */
    private $cache;



    public function __construct($user,$mdp,$grant_type,$client_id,$client_secret, $url_login, $url_player, HttpClientInterface $httpClient, AdapterInterface $cache)
    {
        $this->user = $user;
        $this->mdp = $mdp;
        $this->grant_type = $grant_type;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->url_login = $url_login;
        $this->url_player = $url_player;
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    private function jwt_request($token, $post, $fetchUrl) 
    {
        $response = $this->httpClient->request("POST",$fetchUrl,[
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'auth_bearer' => $token,
            'body' => $post,
        ]);
        
        return $response->toArray();
    }

    private function fetchAPI($fetchUrl, $payload) 
    {
        try {
            $token = $this->getToken();
            return $this->jwt_request($token, $payload, $fetchUrl);
        } catch(Exception $e) {
            throw $e;
        }
    }

    public function getToken()
    {
        try {
            $token = $this->cache->get('token', function(ItemInterface $item) {
                $item->expiresAfter(200);
                $response = $this->httpClient->request("POST",$this->url_login,[
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
                return $response->toArray()['access_token'];
            });

            return $token;

        } catch (Exception $e) {
            return 404;
        }
    }

    public function fetchPlayer($allycode, $lang = "eng_us", $project = null)
    {
        try {
          $myObj = new \stdClass();
          $myObj->allycode = array_map('intval', explode(',', $allycode));
          $myObj->language = $lang;
          $myObj->project = $project;
          return $this->fetchAPI($this->url_player, json_encode($myObj, JSON_NUMERIC_CHECK));
        } catch (Exception $e) {
            $arrayReturn['error_message'] = $e->getMessage();
            $arrayReturn['error_code'] = $e->getCode();
            return $arrayReturn;
        }
    }

}