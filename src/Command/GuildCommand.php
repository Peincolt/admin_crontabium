<?php

namespace App\Command;

use App\Entity\Guild;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->addOption('players', null, InputOption::VALUE_OPTIONAL, 'Do you want to synchronize guild players ?')
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
            $all = true;
            $output->writeln([
                'Vous avez décidé de tout synchroniser (guilde, joueurs, données des joueurs)',
                '===========================',
                'Début de la synchronisation',
                '==========================='
            ]);
        }

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
                '===========================',
                'Fin de la commande'
            ]);
            exit();
        }

        $output->writeln([
            'Synchronisation des informations de la guilde terminée',
            '==========================='
        ]);

        $guild = $result;


        if ($option = $input->getOption('players')) {
            if ($option == "all" || isset($all)) {
                $ship = $heroes = true;
                $output->writeln([
                    'Vous avez décidé de synchroniser toutes les données (héros et vaisseaux compris) des joueurs',
                    '===========================',
                    'Début de la synchronisation des données de toutes les données des joueurs',
                    'Synchronisation en cours ...'
                ]);
            } else {
                switch ($option) {
                    case 'heroes' :
                        $heroes = true;
                        $output->writeln([
                            'Vous avez décidé de synchroniser toutes les données des joueurs et de leurs héros',
                            '===========================',
                            'Début de la synchronisation des données des joueurs et de leurs héros',
                            'Synchronisation en cours ...'
                        ]);
                    break;
                    
                    case 'ships' :
                        $ships = true;
                        $output->writeln([
                            'Vous avez décidé de synchroniser toutes les données des joueurs et de leurs vaisseaux',
                            '===========================',
                            'Début de la synchronisation des données des joueurs et de leurs vaisseaux',
                            'Synchronisation en cours ...'
                        ]);
                    break;

                    default :
                        $output->writeln([
                            'Vous avez décidé de synchroniser toutes les données des joueurs',
                            '===========================',
                            'Début de la synchronisation des données des joueurs',
                            'Synchronisation en cours ...'
                        ]);
                    break;
                }
            }

            $result = $this->serviceGuild->updateGuild($input->getArgument('id'),$arrayOption);
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