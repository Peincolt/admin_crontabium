<?php

namespace App\Command;

use App\Service\Data\Helper as DataHelper;
use App\Service\Entity\Guild as ServiceGuild;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GuildCommand extends Command
{

    protected static $defaultName = 'app:synchro-guild';
    private $serviceGuild;
    private $dataHelper;

    public function __construct(ServiceGuild $serviceGuild, DataHelper $dataHelper)
    {
        parent::__construct();
        $this->serviceGuild = $serviceGuild;
        $this->dataHelper = $dataHelper;
    }

    protected function configure()
    {
        $this->setDescription('Synchronize the data guild with swgoh.gg api')
            ->setHelp('This command can be use if you want to synchronize your guild data')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the guild. This option is necessary')
            ->addArgument('players', InputArgument::OPTIONAL, 'Do you want to synchronize guild players ?')
            /* Arguments when the user wants to synchronize guild player or player */
            ->addArgument('players-characters', InputArgument::OPTIONAL, 'Do you want to synchronize player\'s characters when you synchronize players ?')
            ->addArgument('players-ships', InputArgument::OPTIONAL, 'Do you want to synchronize player\'s ships when you synchronize players ?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Start of the command',
            '===========================',
            'Retrieving guild data from the database'
            ]);
        if ($guild = $this->dataHelper->getDatabaseData("\App\Entity\Guild",array('id_swgoh' => $input->getArgument('id')))) {
            $output->writeln([
                'We found the guild on the database',
                '==========================='
                ]);
            $arrayOption = array();
            if ($input->getArgument('players')) {
                $output->writeln(['You choose to synchronize player\'s data',]);
                $arrayOption['players'] = true;
                if ($input->getArgument('players-characters')) {
                    $output->writeln(['You choose to synchronize player\'s heroes']);
                    $arrayOption['players_heroes'] = true;
                }
                if ($input->getArgument('players-ships')) {
                    $output->writeln(['You choose to synchronize player\'s ships']);
                    $arrayOption['players_ships'] = true;
                }
            }
            $output->writeln([
                '===========================',
                'The synchronize will start now'
                ]);
            $result = $this->serviceGuild->updateGuild($input->getArgument('id'),$arrayOption);
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
            }
        }

        $output->writeln([
            'The guild doesn\'t exist. You can get the id of yout guild when you\'re in the admin panel',
            '===========================',
            'End of the command'
        ]);
        return 500;
    }

}