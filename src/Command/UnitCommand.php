<?php

namespace App\Command;

use App\Service\Data\Helper as DataHelper;
use App\Service\Entity\Unit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UnitCommand extends Command 
{

    protected static $defaultName = 'app:synchro-unit';
    private $unit;
    private $dataHelper;

    public function __construct(Unit $unit, DataHelper $dataHelper)
    {
        parent::__construct();
        $this->unit = $unit;
        $this->dataHelper = $dataHelper;
    }

    protected function configure()
    {
        $this->setDescription('Synchronize data unit with swgoh.gg api')
            ->setHelp('This command can be use if you want to synchronize Star Wars Galaxy Of Heroes Hero/Ship from swgoh.gg api ')
            ->addArgument('data-name', InputArgument::REQUIRED, 'What do you want to synchronize ? (Possible values : Hero, Ship')
            /* Arguments when the user wants to synchronize hero or ships */
            ->addArgument('unit-ids', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'List the heroes you wanna synchronize (wich id must be seperated with a space)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Start of the command',
            '==========================='
        ]);

        switch (ucfirst($input->getArgument('data-name'))) {
            case 'Hero':
                $type = 'Hero';
                $entityName = 'characters';
                $output->writeln([
                    'You choose to synchronize heroes'
                ]);
            break;

            case 'Ship':
                $type = 'Ship';
                $entityName = 'ships';
                $output->writeln([
                    'You choose to synchronize ships'
                ]);
            break;
            
            default:
                $output->writeln([
                    'With this command, you can synchronize Hero or Ship. Type Hero or Ship after the command name',
                    '===========================',
                    'End of the command'
                ]);
                return 500;
            break;
        }

        if ($array = $this->dataHelper->getNumbers($input->getArgument('unit-ids'),$type)) {
            if (isset($array['wrong_ids'])) {
                $output->writeln([
                    'You tried to update some heroes but the following ids doesn\'t match'
                ]);
                $output->writeln($array['wrong_ids']);
            }

            if (isset($array['names'])) {
                $output->writeln([
                    'You decided to update these '.$type.' : '
                ]);
                $output->writeln($array['names']);
                $output->writeln([
                    '===========================',
                    'The synchronization will start',
                ]);
                $result = $this->unit->updateUnit($entityName,$array['ids']);
                if (!isset($result['error_message'])) {
                    $output->writeln([
                        '===========================',
                        'The synchronization is over and everything is fine',
                    ]);
                    return 200;
                } else {
                    $output->writeln([
                        '===========================',
                        'The synchronization stopped because we encounter an error',
                        'There is the message : ',
                        $result['error_message']
                    ]);
                    return 404;
                }
            }

            $output->writeln([
                '===========================',
                'We can\'t synchronize '.$type.' because you put wrong informations',
            ]);
            return 404;
        } else {
            $output->writeln([
                'You choose to synchronize all '.$type.'s',
                '===========================',
                'The synchronization will start',
            ]);
            $result = $this->unit->updateUnit($entityName,false);
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

}