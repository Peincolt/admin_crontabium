<?php

namespace App\Repository;

use App\Entity\Squad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Squad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Squad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Squad[]    findAll()
 * @method Squad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SquadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Squad::class);
    }

    public function findSquadsByType($type)
    {
        $qb = $this->createQueryBuilder('s');
        if ($type != "all") {
            $qb->where('s.used = :type')
                ->setParameter(':type',$type);
        }
        return $qb->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Squad[] Returns an array of Squad objects
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
    public function findOneBySomeField($value): ?Squad
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
