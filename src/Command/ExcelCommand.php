<?php

namespace App\Command;

use App\Entity\Guild;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\phpspreadsheet\GenerateExcel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExcelCommand extends Command 
{

    protected static $defaultName = 'generate-excel';
    private $generateExcel;
    private $entityManagerInterface;

    public function __construct(GenerateExcel $generateExcel, EntityManagerInterface $entityManagerInterface)
    {
        parent::__construct();
        $this->generateExcel = $generateExcel;
        $this->entityManagerInterface = $entityManagerInterface;
    }

    protected function configure()
    {
        $this->setDescription('Commande qui permet de générer le fichier CSV contenant l\'ensemble des templates de la base de données')
            ->addArgument('id', InputArgument::REQUIRED, 'Id de la guilde a utilisé afin de générer les exports CSV');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln([
            'Début de la commande',
            '==========================='
        ]);

        $guild = $this->entityManagerInterface->getRepository(Guild::class)
            ->findOneBy(['id_swgoh' => $input->getArgument('id')]);

        if (!empty($guild)) {
            $output->writeln([
                'Vous souhaitez générer un fichier Excel à partir des informations de la guilde '.$guild->getName(),
                '===========================',
                'Début de la génération de la matrice Excel...',
            ]);
            $this->generateExcel->constructSpreadShit($guild->getId());
            $output->writeln([
                'Fin de la génération de la matrice Excel',
                '===========================',
                'Fin de la commande'
            ]);
        } else {
            $output->writeln([
                'Erreur : Impossible de trouver la guilde dans la base de données',
                '===========================',
                'Fin de la commande'
            ]);
        }
    }
}