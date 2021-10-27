<?php

namespace App\Service\Data;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Helper {

    private $entityManagerInterface;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManagerInterface,
        UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function matchEntityField($entityName, $data)
    {
        $arrayReturn = array();

        switch ($entityName) {

            case 'guild':
                $arrayReturn['Name'] = $data['name'];
                $arrayReturn['Members'] = $data['member_count'];
                $arrayReturn['IdSwgoh'] = $data['id'];
                $arrayReturn['GalacticalPuissance'] = $data['galactic_power'];
            break;

            case 'player':
                if (preg_match("#^[0-9]+$#",$data['data']['last_updated'])) {
                    $date = new \DateTime();
                    $date->setTimestamp($data['data']['last_updated']);
                    $arrayReturn['LastUpdated'] = $date;
                } else {
                    $date = \DateTime::createFromFormat('Y-m-d H:i:s',preg_replace("#[a-zA-Z]+#",' ',$data['data']['last_updated']));
                    $newDate = preg_replace("#[a-zA-Z]+#",' ',$data['data']['last_updated']);
                    $arrayReturn['LastUpdated'] = new \DateTime($date->format('Y-m-d H:i'));
                }
                $arrayReturn['IdSwgoh'] = $data['data']['ally_code'];
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
                if ($data['gear_level'] == 13 && $data['relic_tier'] == 2) {
                    $arrayReturn['RelicLevel'] = $data['relic_tier'];
                } elseif ($data['gear_level'] == 13 && $data['relic_tier'] >= 3) {
                    $arrayReturn['RelicLevel'] = $data['relic_tier'] - 2;
                } else {
                    $arrayReturn['RelicLevel'] = 0;
                }
                $arrayReturn['Speed'] = $data['stats']['5'];
                $arrayReturn['Life'] = $data['stats']['1'];
                $arrayReturn['Protection'] = $data['stats']['28'];
                
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

    public function getDatabaseData(string $entityName, array $data)
    {
        return $this->entityManagerInterface
        ->getRepository($entityName)
        ->findOneBy($data);
    }

    public function getNumbers(array $ids = null, string $type)
    {
        $arrayReturn = array();
        if ($ids) {
            foreach ($ids as $id) {
                if (!preg_match("#[0-9]+#",$id)) {
                    $arrayReturn['wrong_id'][] = $id;
                } else {
                    $this->getDatabaseData("\App\Entity\\".$type,array('id_swgoh' => $id))->getName();
                    $arrayReturn['names'][] = $this->getDatabaseData("\App\Entity\\".$type,array('id_swgoh' => $id))->getName();
                    $arrayReturn['ids'][] = $id;
                }
            }
            return $arrayReturn;
        }
        return $ids;
    }

    public function fillObject(array $array, string $objectName, $object)
    {
        $entityField = $this->matchEntityField($objectName,$array);
        foreach($entityField as $key => $value) {
            $function = 'set'.$key;
            $object->$function($value);
        }
        return $object;
    }
}