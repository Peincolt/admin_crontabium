<?php

namespace App\Service\Data;

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
            break;

            case 'characters':
                $arrayReturn['BaseId'] = $data['base_id'];
                $arrayReturn['Name'] = $data['name'];
                $arrayReturn['IdSwgoh'] = $data['pk'];
                $arrayReturn['Categories'] = $data['categories'];
            break;

            case 'ships' :
                $arrayReturn['BaseId'] = $data['base_id'];
                $arrayReturn['IdSwgoh'] = 0;
                $arrayReturn['Name'] = $data['name'];
                $arrayReturn['Categories'] = $data['categories'];
            break;

            case 'player_ship':
                $arrayReturn['NumberStars'] = $data['rarity'];
                $arrayReturn['Level'] = $data['level'];
                $arrayReturn['GalacticalPuissance'] = $data['power'];
            break;

            case 'player_hero':
                $arrayReturn['NumberStars'] = $data['rarity'];
                $arrayReturn['Level'] = $data['level'];
                $arrayReturn['GearLevel'] = $data['gear_level'];
                $arrayReturn['GalacticalPuissance'] = $data['power'];
                $arrayReturn['RelicLevel'] = $data['relic_tier'];
            break;
        }

        return $arrayReturn;
    }

    public function convertTypeToEntityName($type)
    {
        switch ($type) {
            case 'characters':
                return 'Hero';
            break;

            case 'ships':
                return 'Ship';
            break;
        }
    }
}