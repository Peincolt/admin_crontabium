<?php

namespace App\Command;

use App\Entity\Hero;
use App\Entity\Ship;
use App\Entity\Squad;
use App\Entity\SquadUnit;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Entity\Hero as HeroService;
use App\Service\Entity\Ship as ShipService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class CreateSquadCommand extends Command 
{

    protected static $defaultName = 'create-squad';
    private $entityManagerInterface;
    private $heroService;
    private $shipService;

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        HeroService $heroService,
        ShipService $shipService
    )
    {
        parent::__construct();
        $this->entityManagerInterface = $entityManagerInterface;
        $this->heroService = $heroService;
        $this->shipService = $shipService;
    }

    protected function configure()
    {
        $this->setDescription('Commande qui permet de créer une escouade.');
        }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exit = false;
        $helper = $this->getHelper('question');
        $arrayUserChoice = array('Créer une team','Quitter le programme');
        $arrayTeamPosition = array('attaque' => 'attack','défense' => 'defense');
        $arrayTeamType = array('héros' => 'hero','vaisseaux' => 'ship');
        $arraySquadChoice = array('Ajouter une unité','Sauvegarder mon escouade');
        $arrayListHero = $this->heroService->getHerosSquadCommand(null,array('id','name'));
        $arrayListShip = $this->shipService->getShipsSquadCommand(null,array('id','name'));
        $entityManagerInterface = $this->entityManagerInterface;

        while (!$exit) {
            $complete = false;
            $question = new ChoiceQuestion(
                'Que souhaitez vous faire ? ',
                $arrayUserChoice,
                '0'
            );

            $userChoice = $helper->ask($input, $output, $question);

            $output->writeln('Vous avez décidé de '.lcfirst($userChoice));
            
            if ($userChoice == $arrayUserChoice[1]) {
                return 200;
            }

            $squad = new Squad();

            $questionSquadName = new Question('Veuillez indiquer le nom de l\'escouade : ');
            $questionSquadName->setValidator(function ($answer) use ($entityManagerInterface) {
                $squad = $entityManagerInterface->getRepository(Squad::class)->findOneBy(['name' => strtolower($answer)]);
                if (!empty($squad)) {
                    throw new \RuntimeException(
                        'Une escouade porte déjà ce nom. Veuillez en choisir un autre'
                    );
                }
                return $answer;
            });
            $squad->setName($helper->ask($input, $output, $questionSquadName));

            $questionSquadPosition = new ChoiceQuestion(
                'Est-ce une team à poser en attaque ou en défense ? ',
                array_keys($arrayTeamPosition),
                '0'
            );

            $squad->setUsed($arrayTeamPosition[$helper->ask($input, $output, $questionSquadPosition)]);

            $questionSquadType = new ChoiceQuestion(
                'Est-ce une team à poser en attaque ou en défense ? ',
                array_keys($arrayTeamType),
                '0'
            );

            $squad->setType($arrayTeamType[$helper->ask($input, $output, $questionSquadType)]);

            while (!$complete) {
                $questionSquadChoice = new ChoiceQuestion(
                    'Que souhaitez vous faire pour l\'escouade '.$squad->getName(),
                    $arraySquadChoice,
                    '0'
                );
                $questionSquadChoice->setValidator(function ($answer) use ($squad) {
                    if ($answer == 1 && count($squad->getSquadUnits()) == 0) {
                        throw new \RuntimeException(
                            'Votre escouade doit comporter au moins une unité.'
                        );
                    }
                    return $answer;
                });

                $userSquadChoice = $helper->ask($input, $output, $questionSquadChoice);

                if ($userSquadChoice == 1) {
                    $this->entityManagerInterface->flush($squad);
                    $output->writeln('L\'escouade a bien été sauvegardée');
                    $complete = true;
                } else {
                    if ($squad->getType() == "hero") {
                        $listUnit = array_keys($arrayListHero);
                        $repo = $this->entityManagerInterface->getRepository(Hero::class);
                    } else {
                        $listUnit = array_keys($arrayListShip);
                        $repo = $this->entityManagerInterface->getRepository(Ship::class);
                    }
                    
                    $questionSquadAddUnit = new Question('Veuillez entrer le nom de l\'unité à ajouter dans l\'escouade : ');
                    $questionSquadAddUnit->setAutocompleterValues($listUnit);
                    $questionSquadAddUnit->setValidator(function ($answer) use ($squad,$repo) {
                        $unit = $repo->findOneBy(['name' => $answer]);
                        if (!isset($unit)) {
                            throw new \RuntimeException(
                                'L\'unité que vous essayez d\'ajouter à l\'escouade n\'esxiste pas.'
                            );
                        }

                        if (count($squad->getSquadUnits()) > 0) {
                            foreach($squad->getSquadUnits() as $unitSquad) {
                                if ($unitSquad->getHero() == $unit || $unitSquad->getShip() == $unit) {
                                    throw new \RuntimeException(
                                        'L\'unité que vous essayez d\'ajouter à l\'escouade est déjà présente dans l\'escouade'
                                    );
                                }
                            }
                        }
                        return $answer;
                    });

                    $squadUnit = new SquadUnit();
                    $squadUnit->setSquad($squad);
                    $this->entityManagerInterface->persist($squadUnit);
                    if ($squad->getType() == 'hero') {
                        $squadUnit->setHero($repo->find($arrayListHero[$helper->ask($input, $output, $questionSquadAddUnit)]));
                    } else {
                        $squadUnit->setShip($repo->find($arrayListShip[$helper->ask($input, $output, $questionSquadAddUnit)]));
                    }

                    $questionSquadAddUnitPosition = new Question('Veuillez indiquer la position de l\'unité dans l\'escouade : ');
                    $questionSquadAddUnitPosition->setValidator(function ($answer) use ($squadUnit) {
                        if (!is_numeric($answer)) {
                            throw new \RuntimeException(
                                'La position doit être un nombre'
                            );
                        }
                        return $answer;
                    });

                    $squadUnit->setShowOrder($helper->ask($input,$output,$questionSquadAddUnitPosition));
                    $this->entityManagerInterface->flush();
                    $squad->addSquadUnit($squadUnit);
                }
            }
        }
    }
}