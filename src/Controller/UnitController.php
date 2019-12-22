<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function units(Request $request)
    {
        $array = explode('_',$request->attributes->get('_route'));
        $className = ucfirst(substr($array[1],0,strlen($array[1])-2));
        $entityName = 'App\Entity\\'.$className;

        $units = $this->getDoctrine()
        ->getManager()
        ->getRepository($entityName)
        ->findAll();

        $functionName = ucfirst($className).'Player';

        return $this->render('unit/list.html.twig', [
            'units' => $units,
            'type' => $array[1],
            'unitsCount' => $functionName
        ]);
    }

    /**
     * @Route("/hero/{id}", name="unit_hero")
     * @Route("/ship/{id}", name="unit_ship")
     */
    public function unit(Request $request, $id)
    {
        $unit = $this->getDoctrine()
        ->getManager()
        ->getRepository(ucfirst($request->attributes->get('_route')))
        ->find($id);

        return $this->render('unit/list.html.twig', [
            'unit' => $unit,
        ]);
    }
}
