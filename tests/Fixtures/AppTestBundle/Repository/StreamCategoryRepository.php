<?php

namespace AppTestBundle\Repository;

use AppTestBundle\Entity\StreamCategory;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method StreamCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method StreamCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method StreamCategory[]    findAll()
 * @method StreamCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StreamCategoryRepository extends \Darkanakin41\StreamBundle\Repository\StreamCategoryRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StreamCategory::class);
    }

    // /**
    //  * @return StreamCategory[] Returns an array of StreamCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StreamCategory
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
