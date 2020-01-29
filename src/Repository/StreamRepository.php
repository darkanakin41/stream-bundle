<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Repository;

use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Nomenclature\StatusNomenclature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;

abstract class StreamRepository extends ServiceEntityRepository
{
    /**
     * Find the stream category.
     *
     * @param $provider
     *
     * @return Stream[]|null
     */
    public function findToUpdate($provider)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->where('s.platform = :platform');
        $qb->orderBy('s.updated', 'ASC');
        $qb->setMaxResults(100);
        $qb->setParameter('platform', $provider);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get a random stream.
     *
     * @return Stream|null
     *
     * @throws NonUniqueResultException
     */
    public function findRandom()
    {
        $fields = array('viewers', 'updated', 'language', 'platform', 'viewers', 'id');
        $orders = array('ASC', 'DESC');

        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.category', 'sc');
        $qb->where('s.status = :status');
        $qb->andWhere('sc.displayed = 1');

        $qb->orderBy(sprintf('s.%s', $fields[array_rand($fields)]), $orders[array_rand($orders)]);

        $qb->setParameter('status', StatusNomenclature::ONLINE);
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
