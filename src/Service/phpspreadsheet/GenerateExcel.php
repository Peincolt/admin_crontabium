<?php

namespace App\Service\phpspreadsheet;

use App\Repository\GuildRepository;
use App\Repository\SquadRepository;
use App\Repository\SquadUnitRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Service\Entity\Squad as SquadService;

class GenerateExcel
{
    private $squadRepository;
    private $squadUnitRepository;
    private $squadService;
    private $guildRepository;

    public function __construct(
        SquadRepository $squadRepository, 
        SquadService $squadService, 
        GuildRepository $guildRepository,
        SquadUnitRepository $squadUnitRepository
    )
    {
        $this->squadRepository = $squadRepository;
        $this->squadService = $squadService;
        $this->guildRepository = $guildRepository;
        $this->squadUnitRepository = $squadUnitRepository;
    }

    public function constructSpreadShit($guild,$folder,$type)
    {
        $spreadSheet = new Spreadsheet();
        $arrayInformationHero = array ("Etoile Gear Relic (Speed)");
        $arrayInformationShip = array ("Protection/Vie (Speed)");
        $squads = $this->squadRepository
            ->findSquadsByType($this->translateType($type));
        $numberPlayers = $this->guildRepository->countMembers($guild->getId())[1];
        $spreadSheet->removeSheetByIndex(0);
        foreach ($squads as $squad) {
            $compteur = 0;
            $startData = 4;
            $numberLineStatUnit = $startData + $numberPlayers;
            $startLetter = "B";
            if ($squad->getType() == "hero") {
                $arrayInformations = $arrayInformationHero;
            } else {
                $arrayInformations = $arrayInformationShip;
            }

            $sheet = $spreadSheet->createSheet();
            $sheet->setTitle($squad->getName());
            $sheet->setCellValue('A1', 'Joueur');
            $sheet->setCellValue('B1', 'Unités');
            $sheet->mergeCells('B1:U1');
            $sheet->mergeCells('A2:A3');

            // Affichage du nom des unités, des stats gear et de la première colonne du tableau (Protection/Vie (Speed))
            foreach ($squad->getSquadUnits() as $squadUnit) {
                $sheet->setCellValue(
                    $startLetter."2",
                    $squadUnit->getUnitByType($squad->getType())->getName()
                );
                if ($squad->getType() == "hero") {
                    $sheet->setCellValue(
                        $startLetter.($numberLineStatUnit),
                        "=COUNTIF(".
                        $startLetter.
                        "4:".
                        $startLetter.
                        ($numberLineStatUnit-1).",\"*G13*\")"
                    );
                }
                $sheet->getCell($startLetter.($numberLineStatUnit))
                    ->getStyle()->setQuotePrefix(true);
                $sheet->fromArray($arrayInformations, null, $startLetter."3");
                $compteur++;
                $startLetter++;
            }

            $startLetter++;

            // Affichage du tableau stats des joueurs
            /*$sheet->setCellValue($startLetter."2", "Stats des persos du joueur");
            $sheet->setCellValue($startLetter."3", "Nombre de gear 13");
            $startLetter++;
            $sheet->setCellValue($startLetter."3", "Nombre de gear 12");
            $startLetter++;
            $sheet->setCellValue($startLetter."3", "Nombre de gear <= 11");

            $sheet->setCellValue($startLetter[$compteur].($NbSiFormulaStart),"=COUNTIF(B4:".$startLetter.($NbSiFormulaStart-1).",\"*G13*\")");
            $sheet->setCellValue($startLetter[$compteur].($NbSiFormulaStart),"=COUNTIF(".$startLetter."4:".$startLetter.($NbSiFormulaStart-1).",\"*G13*\")");*/

            if ($squad->getType() == "hero") {
                $sheet->setCellValue('A'.$numberLineStatUnit, 'Nombre de G13 :');
            }
            
            $squadData = $this->squadService
                ->getPlayerSquadInformation($squad, $guild);
            foreach ($squadData as $player => $data) {
                $sheet->setCellValue('A'.$startData, $player);
                $startLetter = "B";
                $sheet->setCellValue($this->incrementLetter($startLetter,count($squad->getSquadUnits()) + 1).($startData),"=COUNTIF(".$startLetter.$startData.":".$this->incrementLetter($startLetter,count($squad->getSquadUnits())).($startData).",\"*G13*\")");
                $sheet->setCellValue($this->incrementLetter($startLetter,count($squad->getSquadUnits()) + 2).($startData),"=COUNTIF(".$startLetter.$startData.":".$this->incrementLetter($startLetter,count($squad->getSquadUnits())).($startData).",\"*G12*\")");
                $sheet->setCellValue($this->incrementLetter($startLetter,count($squad->getSquadUnits()) + 3).($startData),"=".count($squad->getSquadUnits())."-".$this->incrementLetter($startLetter,count($squad->getSquadUnits()) + 2).($startData)."-".$this->incrementLetter($startLetter,count($squad->getSquadUnits()) + 1).($startData));
                foreach ($data as $arrayValueUnit) {
                    if ($squad->getType() == "hero") {
                        $chain = $arrayValueUnit['rarity'].'* G'.$arrayValueUnit['gear_level'].' R'.$arrayValueUnit['relic_level'].' ('.$arrayValueUnit['speed'].')';
                        if (!empty($arrayValueUnit['omicrons'])) {
                            $chain.=' omicron(s): ';
                            for ($i = 0; $i < count($arrayValueUnit['omicrons']); $i++) {
                                if ($i == count($arrayValueUnit['omicrons']) - 1) {
                                    $chain.=$arrayValueUnit['omicrons'][$i];
                                } else {
                                    $chain.=$arrayValueUnit['omicrons'][$i].',';
                                }
                            }
                        }
                        $sheet->setCellValue($startLetter.$startData, $chain);
                        $sheet->getStyle($startLetter.$startData)->applyFromArray($this->getStyleByGear($arrayValueUnit['gear_level']));
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
        $writer->save($folder.'/'.$guild->getName().' - '.$type.'.xlsx');
    }

    public function getStyleByGear(String $gearLevel)
    {
        switch ($gearLevel) {
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

    private function translateType(string $type)
    {
        switch ($type) {
            case "attaque":
                return "attack";
                break;
            case "défense":
                return "defense";
                break;
            default:
                return "all";
                break;
        }
    }

    /**
     * Fonction qui permet d'incrémenter les lettres pour les tableaux Excel
     * 
     * @param string $letter Lettre à incrémenter
     * @param int    $number Nombre qui va servir dans la boucle for
     * 
     * @return $letter
     */
    public function incrementLetter(string $letter, int $number)
    {
        for ($i = 1; $i <= $number; $i++) {
            $letter++;
        }
        return $letter;
    }
}