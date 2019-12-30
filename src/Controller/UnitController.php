<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Entity\PlayerUnit;
use App\Service\Entity\Unit as UnitHelper;

class UnitController extends AbstractController
{
    private $unitHelper;

    public function __construct(UnitHelper $unitHelper)
    {
        $this->unitHelper = $unitHelper;
    }
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
     * @Route("/heroes", name="unit_list_hero")
     * @Route("/ships", name="unit_list_ship")
     */
    public function units(Request $request, PlayerUnit $playerHelper)
    {
        $entityInformation = $this->unitHelper
            ->getEntityByRoute($request->attributes->get('_route'));

        $number = $playerHelper->getNumberUnit($entityInformation['player_namespace_class']);

        $units = $this->getDoctrine()
        ->getManager()
        ->getRepository($entityInformation['namespace'])
        ->findAll();

        return $this->render('unit/list.html.twig', [
            'units' => $units,
            'type' => $entityInformation['name'],
            'number' => $number
        ]);
    }

    /**
     * @Route("/hero/{id}", name="unit_hero")
     * @Route("/ship/{id}", name="unit_ship")
     */
    public function unit(Request $request,$id)
    {
        $arrayReturn = array();
        $entityInformation = $this->unitHelper
            ->getEntityByRoute($request->attributes->get('_route'));

        $unit = $this->getDoctrine()
            ->getRepository($entityInformation['namespace'])
            ->find($id);
        $arrayReturn['name'] = $unit->getName();
        $functionName = $entityInformation['function'];
        $unitPlayers = $unit->$functionName();

        for($i=0;$i<count($unitPlayers);$i++) {
            $arrayReturn['players'][$i]['player_name'] = $unitPlayers[$i]->getPlayer()->getName();
            $arrayReturn['players'][$i]['level'] = $unitPlayers[$i]->getLevel();
            $arrayReturn['players'][$i]['stars'] = $unitPlayers[$i]->getNumberStars();
            $arrayReturn['players'][$i]['galactical_puissance'] = $unitPlayers[$i]->getGalacticalPuissance();
            if ($entityInformation['name'] == 'Hero') {
                $arrayReturn['players'][$i]['relic'] = $unitPlayers[$i]->getRelicLevel();
                $arrayReturn['players'][$i]['gear_level'] = $unitPlayers[$i]->getGearLevel();
            }
        }

        $response = new JsonResponse($arrayReturn);

        return $response;
    }
}
