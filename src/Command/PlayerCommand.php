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
            ->addArgument('player-id', InputArgument::REQUIRED, 'The ally code (put all the numbers together)')
            /* Arguments when the user wants to synchronize hero or ships */
            ->addOption('player-guild', null, InputOption::VALUE_REQUIRED, 'Put the id of the player guild')
            ->addOption('player-heroes', null, InputOption::VALUE_NONE, 'Do you want to synchronize player heroes ?')
            ->addOption('player-ships', null, InputOption::VALUE_NONE, 'Do you want to synchronize player ships ?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $isNew = false;
        $ships = false;
        $heroes = false;

        $output->writeln([
            'Start of the command',
            '==========================='
        ]);

        if ($player = $this->dataHelper->getDatabaseData('App\Entity\Player'
            ,array('ally_code' => $input->getArgument('player-id')))
        ) {
            $output->writeln([
                'You choose to synchronize '.$player->getName(),
            ]);
            $guild = $player->getGuild();

        } else {
            $output->writeln([
                'We don\'t find the player in the database. We will create him',
            ]);
            $isNew = true;
            if ($input->getOption('player-guild')) {
                $guild = $this->dataHelper->getDatabaseData('App\Entity\Guild',array('id_swgoh' => $input->getOption('player-guild')));
                if ($guild) {
                    $output->writeln([
                        'We will attach the player to the guild '.$guild->getName()
                    ]);
                } else {
                    $output->writeln([
                        'The guild doesn\'t exist in the database. We can\'t create the user if the guild is not create',
                        '===========================',
                        'End of the command'
                    ]);
                    return 404;
                }
            }
        }

        if ($input->getOption('player-heroes')) {
            $heroes = true;
            $output->writeln([
                'You decided to synchronize player heroes'
            ]);
        }

        if ($input->getOption('player-ships')) {
            $ships = true;
            $output->writeln([
                'You decided to synchronize player ships'
            ]);
        }

        $result = $this->playerHelper->createPlayer($input->getArgument('player-id'),$heroes,$ships,null,$guild);
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