<?php

namespace App\Repository;

use App\Entity\Hero;
use App\Entity\Squad;
use App\Entity\Player;
use App\Entity\HeroPlayer;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Character|null find($id, $lockMode = null, $lockVersion = null)
 * @method Character|null findOneBy(array $criteria, array $orderBy = null)
 * @method Character[]    findAll()
 * @method Character[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hero::class);
    }

    public function getHeroPlayerInformationsByHero(Hero $hero)
    {
        return $this->createQueryBuilder('h')
            ->leftjoin('h.heroPlayers','hp','WITH','hp.hero = h.id')
            ->leftjoin('hp.player','p','WITH','hp.player = p.id')
            ->where('h = :hero')
            ->setParameter('hero', $hero)
            ->select('p.name, hp.gear_level, hp.level, hp.number_stars as rarity, hp.protection, hp.life, hp.speed, hp.relic_level')
            ->getQuery()
            ->getResult();
    }

    public function getPlayerInformations($id)
    {
        $arrayReturn = array();

        $result = $this->createQueryBuilder('h')
            ->leftjoin('h.heroPlayers','hp','WITH','hp.hero = h.id')
            ->leftjoin('hp.player','p','WITH','hp.player = p.id')
            ->where('h.id = ?1')
            ->setParameter(1, $id)
            ->select('p.name, hp.gear_level, hp.level, hp.number_stars as rarity, hp.protection, hp.life, hp.speed, hp.relic_level')
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

    public function getHerosListByFilter(?array $filters, ?array $select)
    {
        // Partie filtre à faire plus tard si besoin
        $alias = 'h';
        $query = $this->createQueryBuilder($alias);
        if (is_array($select) && count($select) > 0) {
            array_walk($select,function(&$value) use ($alias){
                $value = $alias.'.'.$value;
            });
            $query->select(implode(',',$select));
        }
        return $query->getQuery()->getResult();
    }

    /*SELECT p.name FROM `hero` as h LEFT JOIN hero_player as hp ON hp.hero_id = h.id LEFT JOIN player as p ON p.id = hp.player_id WHERE hp.hero_id = 1*/

    // /**
    //  * @return Character[] Returns an array of Character objects
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
    public function findOneBySomeField($value): ?Character
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
