<?php

namespace App\Service\phpspreadsheet;

use App\Repository\GuildRepository;
use App\Repository\SquadRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Service\Entity\Squad as SquadService;

class GeneratePdf
{
    private $squadRepository;
    private $squadService;
    private $guildRepository;

    public function __construct(SquadRepository $squadRepository, SquadService $squadService, GuildRepository $guildRepository)
    {
        $this->squadRepository = $squadRepository;
        $this->squadService = $squadService;
        $this->guildRepository = $guildRepository;
    }

    public function constructSpreadShit($idGuild)
    {
        $spreadSheet = new Spreadsheet();
        $arrayHeroColumnStart = array("B","D","F","H","J");
        $arrayHeroColumnEnd = array("C","E","G","I","K");
        $arrayInformationHero = array ("Etoile Gear Relic (Speed)","Protection/Vie");
        $squads = $this->squadRepository->findAll();
        $numberPlayers = $this->guildRepository->countMembers($idGuild)[1];

        $spreadSheet->removeSheetByIndex(0);
        foreach ($squads as $squad)
        {
            $compteur = 0;
            $startData = 4;
            $NbSiFormulaStart = $startData + $numberPlayers;
            $sheet = $spreadSheet->createSheet();
            $sheet->setTitle($squad->getName());
            $sheet->setCellValue('A1','Joueur');
            $sheet->setCellValue('B1','Unités');
            $sheet->mergeCells('B1:U1');
            $sheet->mergeCells('A2:A3');
            foreach ($squad->getHero() as $squadHero) {
                $sheet->setCellValue($arrayHeroColumnStart[$compteur]."2",$squadHero->getName());
                //$sheet->setCellValue($arrayHeroColumnStart[$compteur].($NbSiFormulaStart),'=NB.SI('.$arrayHeroColumnStart[$compteur].'4:'.$arrayHeroColumnStart[$compteur].($NbSiFormulaStart-1).',"*G13*")');
                $sheet->setCellValue($arrayHeroColumnStart[$compteur].($NbSiFormulaStart),'=COUNTIF('.$arrayHeroColumnStart[$compteur].'4:'.$arrayHeroColumnStart[$compteur].($NbSiFormulaStart-1).',"*G13*")');
                $sheet->getCell($arrayHeroColumnStart[$compteur].($NbSiFormulaStart))->getStyle()->setQuotePrefix(true);
                $sheet->mergeCells($arrayHeroColumnStart[$compteur]."2:".$arrayHeroColumnEnd[$compteur]."2");
                $sheet->fromArray($arrayInformationHero,null,$arrayHeroColumnStart[$compteur]."3");
                $compteur++;
            }
            $sheet->setCellValue('A'.$NbSiFormulaStart,'Nombre de G13 :');
            

            $squadData = $this->squadService->getPlayerSquadInformation($squad->getId());
            foreach($squadData as $player => $data) {
                $sheet->setCellValue('A'.$startData,$player);
                $startLetter = "B";
                foreach ($data as $arrayValueHero) {
                    $sheet->setCellValue($startLetter.$startData,$arrayValueHero['rarity'].'* G'.$arrayValueHero['gear_level'].' R'.$arrayValueHero['relic_level'].' ('.$arrayValueHero['speed'].')');
                    $startLetter++;
                    $sheet->setCellValue($startLetter.$startData,$arrayValueHero['protection'].'/'.$arrayValueHero['life']);
                    $startLetter++;
                }
                $startData++;
                
            }
        }
        $writer = new Xlsx($spreadSheet);
        $writer->save('F:\Code\admin_crontabium\public\heroesTest.xlsx');
    }
}