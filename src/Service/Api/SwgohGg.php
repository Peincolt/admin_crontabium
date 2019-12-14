<?php

namespace App\Service\Api;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Fait la même chose que l'autre API. Y a juste pas les textes en français
 */
class SwgohGg {

    private $client;
    private $baseUrl;

    public function __construct($baseUrl, HttpClientInterface $httpClientInterface)
    {
        $this->client = $httpClientInterface;
        $this->baseUrl = $baseUrl;
    }

    public function fetchGuild(string $id)
    {
        try {
            return $this->client->request("GET",$this->baseUrl."guild/".$id)->toArray();
        } catch (Exception $e) {
            $arrayReturn['error_code'] = $e->getCode();
            $arrayReturn['error_message'] = $e->getMessage();
            return $arrayReturn;
        }
    }

    public function fetchPlayer(string $allyCode)
    {
        try {
            return $this->client->request("GET",$this->baseUrl."player/".$allyCode)->toArray();
        } catch (Exception $e) {
            $arrayReturn['error_code'] = $e->getCode();
            $arrayReturn['error_message'] = $e->getMessage();
            return $arrayReturn;
        }
        
    }

    public function fetchHeroOrShip($type,$listId)
    {
        $arrayReturn = array();

        try {
            if ($listId) {
                foreach($listId as $id) {
                    $response = $this->client->request("GET",$this->baseUrl.$type."/".$id)->toArray();
                    array_push($arrayReturn,$response);
                }
                return $arrayReturn;
            } else {
                return $this->client->request("GET",$this->baseUrl.$type)->toArray();
            }
        } catch (Exception $e) {
            $arrayReturn['error_code'] = $e->getCode();
            $arrayReturn['error_message'] = $e->getMessage();
            return $arrayReturn;
        }
    }

    public function fetchHeroes($listId)
    {
        return $this->fetchHeroOrShip('characters',$listId);
    }

    public function fetchShips($listId)
    {
        return $this->fetchHeroOrShip('ships',$listId);
    }

}