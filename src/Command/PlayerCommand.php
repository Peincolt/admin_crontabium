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

    protected static $defaultName = 'app:synchro-player';
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
        $this->setDescription('Synchronize player data with swgoh.gg api')
            ->setHelp('This command can be use if you want to synchronize Star Wars Galaxy Of Heroes player from swgoh.gg api ')
            ->addArgument('ally-code', InputArgument::REQUIRED, 'The ally code (put all the numbers together)')
            /* Arguments when the user wants to synchronize hero or ships */
            ->addOption('data',null,InputOption::VALUE_REQUIRED, 'Voulez-vous récupérer toutes les informations du joueur ?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ships = $heroes = false;

        $output->writeln([
            'Début de la commande',
            '==========================='
        ]);

        if ($player = $this->dataHelper->getDatabaseData('App\Entity\Player',array('ally_code' => $input->getArgument('ally-code')))) {
            $output->writeln([
                'Vous souhaitez synchroniser les données du joueur '.$player->getName(),
            ]);
        }

        switch ($input->getOption('data')) {
            case 'all':
                $ships = $heroes = true;
                $output->writeln([
                    'Vous souhaitez synchroniser toutes les données du joueur.'
                ]);
            break;

            case 'ships':
                $ships = true;
                $output->writeln([
                    'Vous souhaitez synchroniser les données du joueur ainsi que celles de ses vaisseaux.',
                ]);
            break;

            case 'heroes':
                $heroes = true;
                $output->writeln([
                    'Vous souhaitez synchroniser les données du joueur ainsi que celles de ses héros.',
                ]);
            break;
        }

        $output->writeln([
            '===========================',
            'Début de la synchronisation',
            '===========================',
        ]);

        $result = $this->playerHelper->createPlayer($input->getArgument('ally-code'),$heroes,$ships,null);
        if (isset($result['error_message'])) {
            $output->writeln([
                '===========================',
                'The synchronization stopped because we encounter an error',
                'There is the message : ',
                $result['error_message']
            ]);
            return 404;
        } else {
            $output->writeln([
                '===========================',
                'The synchronization is over and everything is fine',
            ]);
            return 200;
        }
    }

}