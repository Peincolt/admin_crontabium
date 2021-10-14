<?php

namespace App\Service\Api;

use Exception;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Fait la même chose que l'autre API. Y a juste pas les textes en français
 */
class SwgohGg {

    private $client;
    private $baseUrl;
    private $cache;

    public function __construct(
        $baseUrl, 
        HttpClientInterface $httpClientInterface,
        AdapterInterface $cache
    )
    {
        $this->client = $httpClientInterface;
        $this->baseUrl = $baseUrl;
        $this->cache = $cache;
    }

    public function fetchGuild(string $id)
    {
        try {
            $guild = $this->client->request("GET",$this->baseUrl."guild/".$id)->toArray();
            if (!empty($guild)) {
                $guildCache = $this->cache->getItem('guild'.$id);
                if (!$guildCache->isHit()) {
                    $guildCache->set($guild);
                    $guildCache->expiresAfter(3600);
                }
                return $guild;
            }
            return null;
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