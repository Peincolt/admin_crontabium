<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Entity\PlayerUnit;

class UnitController extends AbstractController
{
    /**
     * @Route("/hero", name="hero")
     */
    public function index()
    {
        return $this->render('unit/index.html.twig', [
            'controller_name' => 'HeroController',
        ]);
    }

    /**
     * @Route("/heroes", name="unit_heroes")
     * @Route("/ships", name="unit_ships")
     */
    public function units(Request $request, PlayerUnit $playerHelper)
    {
        $array = explode('_',$request->attributes->get('_route'));
        if (stristr("ships",$array[1])) {
            $className = ucfirst(substr($array[1],0,strlen($array[1])-1));
        } else {
            $className = ucfirst(substr($array[1],0,strlen($array[1])-2));
        }
        $entityName = 'App\Entity\\'.$className;
        $number = $playerHelper->getNumberUnit($className);

        $units = $this->getDoctrine()
        ->getManager()
        ->getRepository($entityName)
        ->findAll();

        return $this->render('unit/list.html.twig', [
            'units' => $units,
            'type' => $array[1],
            'number' => $number
        ]);
    }

    /**
     * @Route("/hero/{id}", name="unit_hero")
     * @Route("/ship/{id}", name="unit_ship")
     */
    public function unit(Request $request, $id)
    {
        $arrayReturn = array();
        $array = explode('_',$request->attributes->get('_route'));
        $entityName = ucfirst($array[1]);
        $entityFullName = "App\Entity\\".$entityName;
        $function = 'get'.$entityName.'Players';
        $unit = $this->getDoctrine()
        ->getManager()
        ->getRepository($entityFullName)
        ->find($id);
        $arrayReturn['name'] = $unit->getName();
        $unitPlayers = $unit->$function();

        for($i=0;$i<count($unitPlayers);$i++) {
            $arrayReturn['players'][$i]['player_name'] = $unitPlayers[$i]->getPlayer()->getName();
            $arrayReturn['players'][$i]['level'] = $unitPlayers[$i]->getLevel();
            $arrayReturn['players'][$i]['stars'] = $unitPlayers[$i]->getNumberStars();
            $arrayReturn['players'][$i]['galactical_puissance'] = $unitPlayers[$i]->getGalacticalPuissance();
            if ($entityName == 'Hero') {
                $arrayReturn['players'][$i]['relic'] = $unitPlayers[$i]->getRelicLevel();
                $arrayReturn['players'][$i]['gear_level'] = $unitPlayers[$i]->getGearLevel();
            }
        }

        $response = new JsonResponse($arrayReturn);

        return $response;
        /*return $this->render('unit/list.html.twig', [
            'unit' => $unit,
        ]);*/
    }
}
