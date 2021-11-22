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

    public function __construct(Unit $unit, DataHelper $dataHelper)
    {
        parent::__construct();
        $this->unit = $unit;
        $this->dataHelper = $dataHelper;
    }

    protected function configure()
    {
        $this->setDescription('Commande qui permet de récupérer les héros/vaisseaux du jeu grâce à l\'api de swgoh.gg')
            ->addArgument('type', InputArgument::REQUIRED, 'Que souhaitez-vous synchroniser ? (Valeurs possibles : héros, vaisseaux, tout');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Début de la commande',
            '==========================='
        ]);

        switch ($input->getArgument('type')) {
            case 'héros':
                $output->writeln([
                    'Vous avez choisi de synchroniser les héros',
                    '===========================',
                    'Début de la synchronisation des héros ...',
                ]);
                $result = $this->unit->updateUnit('characters');
            break;

            case 'vaisseaux':
                $output->writeln([
                    'Vous avez choisi de synchroniser les vaisseaux',
                    '===========================',
                    'Début de la synchronisation des vaisseaux ...',
                ]);
                $result = $this->unit->updateUnit('ships');
            break;

            case 'tout':
                $output->writeln([
                    'Vous avez choisi de synchroniser les héros et les vaisseaux',
                    '===========================',
                    'Début de la synchronisation des héros ...',
                ]);
                $result = $this->unit->updateUnit('characters');
                if ($result) {
                    $output->writeln([
                        'Fin de la synchronisation des héros',
                        'Début de la synchronisation des vaisseaux ...',
                    ]);
                    $result = $this->unit->updateUnit('ships');
                }
            break;
            
            default:
                $output->writeln([
                    'Veuillez renseigner une valeur valide',
                    '===========================',
                    'Fin de la commande'
                ]);
                return 500;
            break;
        }

        if ($result) {
            $output->writeln([
                'Fin de la synchronisation',
                '===========================',
                'Fin de la commande'
            ]);
            return 500;
        } else {
            $output->writeln([
                'Erreur lors de la synchronisation',
                '===========================',
                'Fin de la commande'
            ]);
            return 200;
        }
    }
}