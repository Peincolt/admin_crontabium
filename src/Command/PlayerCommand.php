<?php

namespace App\Command;

use App\Service\Entity\PlayerHelper;
use App\Service\Data\Helper as DataHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlayerCommand extends Command 
{

    protected static $defaultName = 'synchro-player';
    private $playerHelper;
    private $dataHelper;

    public function __construct(PlayerHelper $playerHelper, DataHelper $dataHelper)
    {
        parent::__construct();
        $this->playerHelper = $playerHelper;
        $this->dataHelper = $dataHelper;
    }

    protected function configure()
    {
        $this->setDescription('Commande qui permet de récupérer les informatiosn d\'un joueur grâce à l\'api de swgoh.gg')
            ->addArgument('code', InputArgument::REQUIRED, 'Le code allié du joueur (retirez les - du code joueur)')
            /* Arguments when the user wants to synchronize hero or ships */
            ->addOption('data',null,InputOption::VALUE_REQUIRED, 'Que souhaitez-vous synchroniser ? (tout = héros + vaisseaux, héros, vaisseaux)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ships = $heroes = false;

        $output->writeln([
            'Début de la commande',
            '==========================='
        ]);

        if ($player = $this->dataHelper->getDatabaseData('App\Entity\Player',array('id_swgoh' => $input->getArgument('code')))) {
            $output->writeln([
                'Vous souhaitez synchroniser les données du joueur '.$player->getName(),
            ]);
        }

        switch ($input->getOption('data')) {
            case 'tout':
            default:
                $ships = $heroes = true;
                $output->writeln([
                    'Vous souhaitez synchroniser toutes les données du joueur.'
                ]);
            break;

            case 'vaisseaux':
                $ships = true;
                $output->writeln([
                    'Vous souhaitez synchroniser les données du joueur ainsi que celles de ses vaisseaux.',
                ]);
            break;

            case 'héros':
                $heroes = true;
                $output->writeln([
                    'Vous souhaitez synchroniser les données du joueur ainsi que celles de ses héros.',
                ]);
            break;

        }

        $output->writeln([
            '===========================',
            'Début de la synchronisation'
        ]);

        $result = $this->playerHelper->updatePlayerByApi($input->getArgument('code'),$heroes,$ships,null);

        if (!$result) {
            $output->writeln([
                'Une erreur est survenue lors de la synchronisation des données du joueur',
                '===========================',
                'Fin de la commande'
            ]);
            return 404;
        } else {
            $output->writeln([
                'La synchronisation des données du joueur est terminée',
                '===========================',
                'Fin de la commande'
            ]);
            return 200;
        }
    }

}