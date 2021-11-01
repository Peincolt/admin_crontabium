<?php

namespace App\Repository;

use App\Entity\Ship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ship[]    findAll()
 * @method Ship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ship::class);
    }

    public function getPlayerInformations($id)
    {
        $arrayReturn = array();

        $result = $this->createQueryBuilder('s')
            ->leftjoin('s.shipPlayers','sp','WITH','sp.ship = s.id')
            ->leftjoin('sp.player','p','WITH','sp.player = p.id')
            ->where('s.id = ?1')
            ->setParameter(1, $id)
            ->select('p.name, sp.gear_level, sp.level, sp.number_stars as rarity','sp.protection','sp.life','sp.speed','sp.relic_level')
            ->getQuery()
            ->getResult()
        ;

        //BOUGER CA DANS UNE FONCTION AUTRE QUE LE REPO
        foreach($result as $tab)
        {
            foreach ($tab as $key => $value) {
                if ($key != 'name') {
                    $arrayReturn[$tab['name']][$key] = $value;
                }
            }
        }

        return $arrayReturn;
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
