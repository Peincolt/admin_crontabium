<?php

namespace App\Repository;

use App\Entity\HeroPlayer;
use App\Entity\HeroPlayerAbility;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method HeroAbility|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeroAbility|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeroAbility[]    findAll()
 * @method HeroAbility[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeroPlayerAbilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HeroPlayerAbility::class);
    }

    public function getTwOmicron(HeroPlayer $heroPlayer)
    {
        return $this->createQueryBuilder('hpa')
            ->join('hpa.ability', 'a', 'WITH', 'hpa.ability = a and a.omicronMode = 8')
            ->andWhere('hpa.heroPlayer = :heroPlayer')
            ->setParameter('heroPlayer', $heroPlayer)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return HeroAbility[] Returns an array of HeroAbility objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HeroAbility
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
