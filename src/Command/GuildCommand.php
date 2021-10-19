<?php

namespace App\Command;

use App\Entity\Guild;
use App\Service\Entity\Guild as ServiceGuild;
use App\Service\Entity\PlayerHelper;
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
    private $serviceGuild;
    private $playerHelper;

    public function __construct(
        EntityManagerInterface $entityManagerInterface, 
        ServiceGuild $serviceGuild,
        PlayerHelper $playerHelper
    )
    {
        parent::__construct();
        $this->entityManagerInterface = $entityManagerInterface;
        $this->serviceGuild = $serviceGuild;
        $this->playerHelper = $playerHelper;
    }

    protected function configure()
    {
        $this->setDescription('Synchronize the data guild with swgoh.gg api')
            ->setHelp('This command can be use if you want to synchronize your guild data')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the guild. This option is necessary')
            ->addOption('all',null,InputOption::VALUE_NONE, 'Voulez-vous récupérer toutes les informations de la guilde ?')
            ->addOption('players', null, InputOption::VALUE_OPTIONAL, 'Do you want to synchronize guild players ?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

        $dataGuild = $this->serviceGuild->updateGuild($input->getArgument('id'),$guild);
        
        if (isset($dataGuild['error_message'])) {
            $output->writeln([
                'Erreur lors de la synchronisation des informations de la guilde',
                'L\'erreur est la suivante : '.$dataGuild['error_message'],
                '===========================',
                'Fin de la commande'
            ]);
            exit();
        }

        $output->writeln([
            'Synchronisation des informations de la guilde terminée',
            '==========================='
        ]);

        if ($option = $input->getOption('players')) {
            $ships = $heroes = false;
            if ($option == "all" || isset($all)) {
                $ships = $heroes = true;
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

            $result = $this->playerHelper->updatePlayers($dataGuild,$ships,$heroes);
        }
    }
}