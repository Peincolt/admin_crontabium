<?php

namespace App\Command;

use App\Service\Data\Helper as DataHelper;
use App\Service\Entity\Ability as AbilityService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbilitiesCommand extends Command 
{

    protected static $defaultName = 'synchro-abilities';

    private $_abilityService;

    public function __construct(AbilityService $abilityService)
    {
        parent::__construct();
        $this->_abilityService = $abilityService;
    }

    protected function configure()
    {
        $this->setDescription('Commande qui permet de récupérer les compétences des héros');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Début de la commande',
            '===========================',
            'Début de la synchronisation des compétences'
        ]);

            $result = $this->_abilityService->updateAbilities();

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