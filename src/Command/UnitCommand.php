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

    protected static $defaultName = 'synchro-unit';
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
        $this->setDescription('Commande qui permet de récupérer les héros/vaisseaux du jeu grâce à l\'api de swgoh.gg')
            ->addArgument('type', InputArgument::REQUIRED, 'Que souhaitez-vous synchroniser ? (Valeurs possibles : heros, vaisseaux, tout');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Début de la commande',
            '==========================='
        ]);

        switch ($input->getArgument('type')) {
            case 'heros':
                $output->writeln([
                    'Vous avez choisi de synchroniser les héros',
                    '===========================',
                    'Début de la synchronisation des héros ...',
                ]);
                try {
                    $this->unit->updateUnit('characters');
                    $output->writeln([
                        'Fin de la synchronisation des héros',
                        'Fin de la commande',
                    ]);
                } catch (\Exception $e) {
                    $output->writeln([
                        'Une erreur est survenue lors de la synchronisation. Voilà le message d\'erreur :',
                        $e->getMessage(),
                        '===========================',
                        'Fin de la commande',
                    ]);
                }
            break;

            case 'vaisseaux':
                $output->writeln([
                    'Vous avez choisi de synchroniser les vaisseaux',
                    '===========================',
                    'Début de la synchronisation des vaisseaux ...',
                ]);
                try {
                    $this->unit->updateUnit('ships');
                    $output->writeln([
                        'Fin de la synchronisation des vaisseaux',
                        'Fin de la commande',
                    ]);
                } catch (\Exception $e) {
                    $output->writeln([
                        'Une erreur est survenue lors de la synchronisation. Voilà le message d\'erreur :',
                        $e->getMessage(),
                        '===========================',
                        'Fin de la commande',
                    ]);
                }
            break;

            case 'tout':
                $output->writeln([
                    'Vous avez choisi de synchroniser les héros et les vaisseaux',
                    '===========================',
                    'Début de la synchronisation des héros ...',
                ]);
                try {
                    $this->unit->updateUnit('characters');
                    $output->writeln([
                        'Fin de la synchronisation des héros',
                        'Début de la synchronisation des vaisseaux ...'
                    ]);
                    $this->unit->updateUnit('ships');
                    $output->writeln([
                        'Fin de la synchronisation des vaisseaux',
                        '===========================',
                        'Fin de la commande',
                    ]);
                } catch (\Exception $e) {
                    $output->writeln([
                        'Une erreur est survenue lors de la synchronisation. Voilà le message d\'erreur :',
                        $e->getMessage(),
                        '===========================',
                        'Fin de la commande',
                    ]);
                }
            break;
            
            default:
                $output->writeln([
                    'L\'option type ne peut prendre que les valeurs suivantes : heros, vaisseaux, tout',
                    '===========================',
                    'Fin de la commande'
                ]);
                return 500;
            break;
        }
    }
}