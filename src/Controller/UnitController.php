<?php

namespace App\Controller;

use App\Service\Entity\PlayerUnit;
use App\Service\Entity\Unit as UnitHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\ItemInterface;

class UnitController extends AbstractController
{
    private $unitHelper;
    private $cache;

    public function __construct(UnitHelper $unitHelper)
    {
        $this->unitHelper = $unitHelper;
        $this->cache = new FilesystemAdapter();
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
            'type' => $entityInformation['name'].'s',
            'number' => $number
        ]);
    }

    /**
     * @Route("/hero/{id}", name="unit_hero")
     * @Route("/ship/{id}", name="unit_ship")
     */
    public function unit(Request $request,UnitHelper $unitHelper, $id = null)
    {
        if (empty($id)) {
            return new JsonResponse(array('erorr_message' => 'Impossible de trouver l\'unité que vous cherchez'));
        }

        return new JsonResponse($unitHelper->getFrontUnitInformation($request->attributes->get('_route'),$id));
    }

    /**
     * @Route("/get/{type}", name="unit_api_get")
     */
    public function getUnitsByType($type)
    {
        $data = $this->unitHelper->getUnits($type);
        return new JsonResponse($data);
    }

    /**
     * @Route("/units", name="unit_api_get_all")
     */
    public function getAllUnits()
    {
        //$item = new ItemInterface();
        $data = /*$this->cache->get('units',*/$this->unitHelper->getAllUnits();//);
        return new JsonResponse($data);
    }
}
