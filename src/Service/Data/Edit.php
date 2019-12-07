<?php

namespace App\Service\Data;

use App\Entity\Player;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class Edit {

    public function matchEntityField($entityName, $data)
    {
        $arrayReturn = array();

        switch ($entityName) {

            case 'player':
                $date = \DateTime::createFromFormat('Y-m-d H:i:s.u',preg_replace("#[a-zA-Z]+#",'',$data['data']['last_updated']));
                $arrayReturn['LastUpdated'] = new \DateTime($date->format('Y-m-d H:i'));
                $arrayReturn['AllyCode'] = $data['data']['ally_code'];
                $arrayReturn['Name'] = $data['data']['name'];
                $arrayReturn['Level'] = $data['data']['level'];
                $arrayReturn['GalacticalPuissance'] = $data['data']['galactic_power'];
                $arrayReturn['CharactersGalacticalPuissance'] = $data['data']['character_galactic_power'];
                $arrayReturn['ShipsGalacticalPuissance'] = $data['data']['ship_galactic_power'];
                $arrayReturn['GearGiven'] = $data['data']['guild_exchange_donations'];
                return $arrayReturn;
            break;

            case 'character':
                $arrayReturn['BaseId'] = $data['base_id'];
                $arrayReturn['Name'] = $data['name'];
                $arrayReturn['NumberStars'] = $data['rarity'];
                $arrayReturn['Level'] = $data['level'];
                $arrayReturn['GearLevel'] = $data['gear_level'];
                $arrayReturn['GalacticalPuissance'] = $data['power'];
                $arrayReturn['RelicLevel'] = $data['relic_tier']; 
            break;

            case 'ship' :
            break;
        }

    }

}