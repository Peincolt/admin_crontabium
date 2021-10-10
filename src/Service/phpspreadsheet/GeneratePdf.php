<?php

namespace App\Service\phpspreadsheet;

use App\Repository\SquadRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Service\Entity\Squad as SquadService;

class GeneratePdf
{
    private $squadRepository;
    private $squadService;

    public function __construct(SquadRepository $squadRepository, SquadService $squadService)
    {
        $this->squadRepository = $squadRepository;
        $this->squadService = $squadService;
    }

    public function constructSpreadShit()
    {
        $spreadSheet = new Spreadsheet();
        $arrayHeroColumnStart = array("B","F","J","N","R");
        $arrayHeroColumnEnd = array("E","I","M","Q","U");
        $arrayInformationHero = array ("Niveau","Etoile","Equipement","PG");
        $squads = $this->squadRepository->findAll();
        $spreadSheet->removeSheetByIndex(0);
        foreach ($squads as $squad)
        {
            $compteur = 0;
            $sheet = $spreadSheet->createSheet();
            $sheet->setTitle($squad->getName());
            $sheet->setCellValue('A1','Joueur');
            $sheet->setCellValue('B1','Unités');
            $sheet->mergeCells('B1:U1');
            $sheet->mergeCells('A2:A3');
            foreach ($squad->getHero() as $squadHero) {
                $sheet->setCellValue($arrayHeroColumnStart[$compteur]."2",$squadHero->getName());
                $sheet->mergeCells($arrayHeroColumnStart[$compteur]."2:".$arrayHeroColumnEnd[$compteur]."2");
                $sheet->fromArray($arrayInformationHero,null,$arrayHeroColumnStart[$compteur]."3");
                $compteur++;
            }

            $compteur = 4;
            
            $squadData = $this->squadService->getPlayerSquadInformation($squad->getId());
            foreach($squadData as $player => $data) {
                $sheet->setCellValue('A'.$compteur,$player);
                $startLetter = "B";
                foreach ($data as $arrayValueHero) {
                    $sheet->setCellValue($startLetter.$compteur,$arrayValueHero['level']);
                    $startLetter++;
                    $sheet->setCellValue($startLetter.$compteur,$arrayValueHero['rarity']);
                    $startLetter++;
                    $sheet->setCellValue($startLetter.$compteur,$arrayValueHero['gearLevel']);
                    $startLetter++;
                    $sheet->setCellValue($startLetter.$compteur,$arrayValueHero['power']);
                    $startLetter++;
                }
                $compteur++;
            }
        }
        $writer = new Xlsx($spreadSheet);
        $writer->save('F:\Code\admin_crontabium\public\heroes.xlsx');
    }
}