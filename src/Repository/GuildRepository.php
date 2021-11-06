<?php

namespace App\Repository;

use App\Entity\Guild;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Guild|null find($id, $lockMode = null, $lockVersion = null)
 * @method Guild|null findOneBy(array $criteria, array $orderBy = null)
 * @method Guild[]    findAll()
 * @method Guild[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuildRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guild::class);
    }

    public function countMembers($idGuild)
    {
        return $this->createQueryBuilder('g')
            ->join('App\Entity\Player','p','p.guild = g')
            ->select('count(p)')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteOldMembers(Guild $guild, array $actuelMembers)
    {
        return $this->createQueryBuilder('g')
            ->delete('p')
            ->join('App\Entity\Player','p','WITH','p.guild = g')
            ->where('p.name not in :array')
            ->andWhere('g.id = :g')
            ->setParameter(':g',$guild)
            ->setParameter(':array',$actuelMembers,\Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Guild[] Returns an array of Guild objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Guild
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
