<?php

namespace App\Repository;

use App\Entity\Hero;
use App\Entity\Player;
use App\Entity\HeroPlayer;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Hero|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hero|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hero[]    findAll()
 * @method Hero[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeroPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HeroPlayer::class);
    }

    public function getPlayerInformations(Hero $hero, Player $player)
    {
        return $this->createQueryBuilder('hp')
            ->andWhere('hp.hero = :hero')
            ->andWhere('hp.player = :player')
            ->setParameter('hero', $hero)
            ->setParameter('player', $player)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Hero[] Returns an array of Hero objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Hero
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
