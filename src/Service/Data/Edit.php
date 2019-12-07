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
                $arrayReturn['IdSwgoh'] = $data[0]['id'];
                $arrayReturn['AllyCode'] = $data[0]['allyCode'];
                $arrayReturn['Name'] = $data[0]['name'];
                $arrayReturn['Level'] = $data[0]['level'];
                $arrayReturn['GalacticalPuissance'] = $data[0]['stats'][0]['value'];
                $arrayReturn['CharactersGalacticalPuissance'] = $data[0]['stats'][1]['value'];
                $arrayReturn['ShipsGalacticalPuissance'] = $data[0]['stats'][2]['value'];
                $arrayReturn['GearGiven'] = $data[0]['stats'][11]['value'];
                return $arrayReturn;
            break;
            case 'character':
            break;
            case 'ship' :
            break;
        }

    }

}