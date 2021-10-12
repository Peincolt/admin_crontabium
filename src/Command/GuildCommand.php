<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GuildCommand extends Command
{

    protected static $defaultName = 'app:synchro-guild';
    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        parent::__construct();
        $this->entityManagerInterface = $entityManagerInterface;
    }

    protected function configure()
    {
        $this->setDescription('Synchronize the data guild with swgoh.gg api')
            ->setHelp('This command can be use if you want to synchronize your guild data')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the guild. This option is necessary')
            ->addOption('all',null,InputOption::VALUE_NONE, 'Voulez-vous récupérer toutes les informations de la guilde ?')
            ->addOption('players', null, InputOption::VALUE_NONE, 'Do you want to synchronize guild players ?')
            /* Arguments when the user wants to synchronize guild player or player */
            ->addOption('players-heroes', null, InputOption::VALUE_OPTIONAL, 'Do you want to synchronize player\'s characters when you synchronize players ?')
            ->addOption('players-ships', null, InputOption::VALUE_OPTIONAL, 'Do you want to synchronize player\'s ships when you synchronize players ?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $arrayOption = array();
        $guild = $this->entityManagerInterface->getRepository(Guild::class)->findOneBy([
            'id_swgoh' => $input->getArgument('id')
        ]);

        $output->writeln([
            'Début de la commande',
            '===========================',
        ]);

        if ($input->getOption('all')) {
            $output->writeln([
                'Vous avez décidé de tout synchroniser (guilde, joueurs, données des joueurs)',
                '===========================',
                'Début de la synchronisation',
                '==========================='
            ]);
            $result = $this->serviceGuild->updateGuild($input->getArgument('id'),array('players' => true, 'players_heroes' => true, 'players_ship' => true));
        } else {
            $output->writeln([
                'Synchronisation des informations de la guilde',
                '==========================='
            ]);
            if (empty($guild)) {
                $output->writeln([
                    'La guilde n\'existe pas en base de données',
                    'Création de la guilde en cours ...'
                ]);
            } else {
                $output->writeln([
                    'La guilde a été trouvé dans la base de données',
                    'Mis à jour des informations de la guilde en cours ...'
                ]);
            }

            $result = $this->serviceGuild->updateGuild($input->getArgument('id'),$guild);
            
            if (is_array($result)) {
                $output->writeln([
                    'Erreur lors de la synchronisation des informations de la guilde',
                    'L\'erreur est la suivante : '.$result['error_message'],
                    '==========================='
                ]);
            
            }

            $output->writeln([
                'Synchronisation des informations de la guilde terminée',
                '==========================='
            ]);
            
            $output->writeln([
                'Création de la base de données dans la '
            ]);
        }

        if ($input->getOption('players')) {
            if ($input->getOption('player') == "all") {
                $output->writeln([
                    'Vous avez décidé de synchroniser toutes les données (héros et vaisseaux compris) des joueurs',
                    '===========================',
                    'Début de la synchronisation des données des joueurs'
                ]);
            } else {
                $output->writeln([
                    'Vous avez décidé de synchroniser toutes les données des joueurs',
                    '===========================',
                    'Début de la synchronisation des données des joueurs'
                ]);
            }
        }

        if ($playersHeroesSynchro = $input->getOption('players-heroes')) {
            $arrayOption['players-heroes'] = $playersHeroesSynchro;
            if ($playersShipsSynchro)
            $output->writeln('Vous avez décidé de synchroniser les héros des joueurs');
            
        }

        if ($playersShipsSynchro = $input->getOption('players-ships')) {
            $arrayOption['players-ships'] = $playersShipsSynchro;
        }
            
            /*if ($input->getOption('players')) {
                $output->writeln(['You choose to synchronize player\'s data']);
                $arrayOption['players'] = true;
                if ($input->getOption('players-characters')) {
                    $output->writeln(['You choose to synchronize player\'s heroes']);
                    $arrayOption['players_heroes'] = true;
                }
                if ($input->getOption('players-ships')) {
                    $output->writeln(['You choose to synchronize player\'s ships']);
                    $arrayOption['players_ships'] = true;
                }
            } else {
                $output->writeln(['No options are found']);
            }
            $output->writeln([
                '===========================',
                'The synchronize will start now'
                ]);
            $result = $this->serviceGuild->updateGuild($input->getArgument('id'),$arrayOption);
        }
        
        if (!isset($result['error_message'])) {
            $output->writeln([
            'The synchronization of the guild is over and everything is fine',
            '===========================',
            'End of the command'
            ]);
            return 200;
        } else {
            $output->writeln([
                'An error occured where we try to synchronize the data. The error message is : ',
                $result['error_message'],
                'If the error persist, join the admin',
                '===========================',
                'End of the command'
                ]);
            return 500;
        }*/
    }

}