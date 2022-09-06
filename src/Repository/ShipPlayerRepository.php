<?php

namespace App\Repository;

use App\Entity\Ship;
use App\Entity\Player;
use App\Entity\ShipPlayer;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Ship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ship[]    findAll()
 * @method Ship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipPlayer::class);
    }

    public function getPlayerInformations(Ship $ship, Player $player)
    {
        return $this->createQueryBuilder('hp')
            ->andWhere('hp.ship = :ship')
            ->andWhere('hp.player = :player')
            ->setParameter('ship', $ship)
            ->setParameter('player', $player)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Ship[] Returns an array of Ship objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ship
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
