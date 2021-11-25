<?php

namespace App\Command;

use App\Entity\Squad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class CreateSquadCommand extends Command 
{

    protected static $defaultName = 'create-squad';

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        parent::__construct();
        $this->entityManagerInterface = $entityManagerInterface;
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
        $entityManagerInterface = $this->entityManagerInterface;

        while (!$exit) {
            $complete = false;
            $question = new ChoiceQuestion(
                'Que souhaitez vous faire ? ',
                $arrayUserChoice,
                '0'
            );

            $userChoice = $helper->ask($input, $output, $question);

            var_dump($userChoice);

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

                if ($userSquadChoice == $arrayUserChoice[1]) {
                    return 200;
                }
            }
        }
    }
}