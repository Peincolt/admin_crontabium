<?php

namespace App\Service\phpspreadsheet;

use App\Repository\GuildRepository;
use App\Repository\SquadRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Service\Entity\Squad as SquadService;

class GenerateExcel
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
        $arrayColumnStart = array("B","D","F","H","J","L","N","P","R","T","V","X","Z");
        $arrayColumnEnd = array("C","E","G","I","K","M","O","Q","S","U","W","Y","AB");
        $arrayInformationHero = array ("Etoile Gear Relic (Speed)","Protection/Vie");
        $arrayInformationShip = array ("Protection/Vie (Speed)");
        $squads = $this->squadRepository->findAll();
        $numberPlayers = $this->guildRepository->countMembers($idGuild)[1];

        $spreadSheet->removeSheetByIndex(0);
        foreach ($squads as $squad)
        {
            $compteur = 0;
            $startData = 4;
            $NbSiFormulaStart = $startData + $numberPlayers;
            if ($squad->getType() == "hero") {
                $list = $squad->getHero();
                $arrayInformations = $arrayInformationHero;
            } else {
                $list = $squad->getShip();
                $arrayInformations = $arrayInformationShip;
            }

            $sheet = $spreadSheet->createSheet();
            $sheet->setTitle($squad->getName());
            $sheet->setCellValue('A1','Joueur');
            $sheet->setCellValue('B1','Unités');
            $sheet->mergeCells('B1:U1');
            $sheet->mergeCells('A2:A3');

            foreach ($list as $squadUnit) {
                $sheet->setCellValue($arrayColumnStart[$compteur]."2",$squadUnit->getName());
                //$sheet->setCellValue($arrayHeroColumnStart[$compteur].($NbSiFormulaStart),'=NB.SI('.$arrayHeroColumnStart[$compteur].'4:'.$arrayHeroColumnStart[$compteur].($NbSiFormulaStart-1).',"*G13*")');
                if ($squad->getType() == "hero") {
                    $sheet->setCellValue($arrayColumnStart[$compteur].($NbSiFormulaStart),"=COUNTIF(".$arrayColumnStart[$compteur]."4:".$arrayColumnStart[$compteur].($NbSiFormulaStart-1).",\"*G13*\")");
                }
                $sheet->getCell($arrayColumnStart[$compteur].($NbSiFormulaStart))->getStyle()->setQuotePrefix(true);
                $sheet->mergeCells($arrayColumnStart[$compteur]."2:".$arrayColumnEnd[$compteur]."2");
                $sheet->fromArray($arrayInformations,null,$arrayColumnStart[$compteur]."3");
                $compteur++;
            }

            if ($squad->getType() == "hero") {
                $sheet->setCellValue('A'.$NbSiFormulaStart,'Nombre de G13 :');
            }
            
            $squadData = $this->squadService->getPlayerSquadInformation($squad->getId());
            foreach($squadData as $player => $data) {
                $sheet->setCellValue('A'.$startData,$player);
                $startLetter = "B";
                foreach ($data as $arrayValueUnit) {
                    if ($squad->getType() == "hero") {
                        $sheet->setCellValue($startLetter.$startData,$arrayValueUnit['rarity'].'* G'.$arrayValueUnit['gear_level'].' R'.$arrayValueUnit['relic_level'].' ('.$arrayValueUnit['speed'].')');
                        $sheet->getStyle($startLetter.$startData)->applyFromArray($this->getStyleByGear($arrayValueUnit['gear_level']));
                        $startLetter++;
                        $sheet->setCellValue($startLetter.$startData,$arrayValueUnit['protection'].'/'.$arrayValueUnit['life']);
                        $startLetter++;
                    } else {
                        $sheet->setCellValue($startLetter.$startData,$arrayValueUnit['protection'].'/'.$arrayValueUnit['life'].' ('.$arrayValueUnit['speed'].')');
                        $startLetter++;
                    }
                }
                $startData++;
                
            }
        }
        $writer = new Xlsx($spreadSheet);
        $writer->save('C:\wamp64\www\admin_crontabium\public\heroesTest.xlsx');
    }

    public function getStyleByGear(String $gearLevel)
    {
        switch ($gearLevel)
        {
            case 13:
                $color = 'FF0000';
            break;

            case 12:
                $color = 'FFC90E';
            break;

            default:
                $color = '800080';
            break;
        }

        return array(
            'font' => [
                'bold' => true,
                'color' => array('rgb' => $color)
            ]
        );
    }
}