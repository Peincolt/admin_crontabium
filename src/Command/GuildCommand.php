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

    protected static $defaultName = 'synchro-guild';
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
        $this->setDescription('Commande qui permet de récupérer les informatiosn d\'une guilde grâce à l\'api de swgoh.gg')
            ->addArgument('id', InputArgument::REQUIRED, 'Id de la guilde ')
            ->addOption('all',null,InputOption::VALUE_NONE, 'Voulez-vous récupérer toutes les informations de la guilde (guilde + joueurs + unités des joueurs) ?')
            ->addOption('joueurs', null, InputOption::VALUE_OPTIONAL, 'Voulez vous synchroniser toutes les données des joueurs ?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ships = $heroes = false;
        $idGuild = $input->getArgument('id');
        $guild = $this->entityManagerInterface->getRepository(Guild::class)->findOneBy([
            'id_swgoh' => $idGuild
        ]);

        $output->writeln([
            'Début de la commande',
            '===========================',
        ]);

        if ($input->getOption('all')) {
            $ships = $heroes = $all = true;
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

        
        if (isset($all)) {
            $output->writeln([
                'Vous avez décidé de synchroniser toutes les données (héros et vaisseaux compris) des joueurs',
                '===========================',
                'Début de la synchronisation des données de toutes les données des joueurs',
                'Synchronisation en cours ...'
            ]);
        } elseif ($input->getOption('joueurs')) {
            switch ($input->getOption('joueurs')) {
                case 'heros' :
                    $heroes = true;
                    $output->writeln([
                        'Vous avez décidé de synchroniser toutes les données des joueurs et de leurs héros',
                        '===========================',
                        'Début de la synchronisation des données des joueurs et de leurs héros',
                        'Synchronisation en cours ...'
                    ]);
                break;
                
                case 'vaisseaux' :
                    $ships = true;
                    $output->writeln([
                        'Vous avez décidé de synchroniser toutes les données des joueurs et de leurs vaisseaux',
                        '===========================',
                        'Début de la synchronisation des données des joueurs et de leurs vaisseaux',
                        'Synchronisation en cours ...'
                    ]);
                break;
            }
        } else {
            $output->writeln([
                'Vous avez décidé de synchroniser toutes les données des joueurs',
                '===========================',
                'Début de la synchronisation des données des joueurs',
                'Synchronisation en cours ...'
            ]);
        }

        try {
            $this->serviceGuild->updateGuildPlayers($dataGuild,$ships,$heroes);
            $output->writeln([
                'Synchronisation terminée',
                '===========================',
                'Fin de la commande'
            ]);
        } catch (\Exception $e) {
            $output->writeln([
                'Une erreur est survenue lors de la synchronisation. Voilà le message d\'erreur :',
                $e->getMessage(),
                '===========================',
                'Fin de la commande'
            ]);
        }
    }
}