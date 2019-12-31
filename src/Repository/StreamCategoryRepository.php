<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Repository;

use Darkanakin41\StreamBundle\Model\StreamCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class StreamCategoryRepository extends ServiceEntityRepository
{
    /**
     * Find the stream category.
     *
     * @param $provider
     * @param $plateformKey
     *
     * @return StreamCategory|null
     */
    public function findByKey($provider, $plateformKey)
    {
        $qb = $this->createQueryBuilder('c');

        /** @var StreamCategory[] $categories */
        $categories = $qb->getQuery()->getResult();

        $result = null;
        foreach ($categories as $category) {
            foreach ($category->getPlatformKeys() as $key => $value) {
                if ($value != $plateformKey) {
                    continue;
                }
                if (0 !== stripos($key, sprintf('%s_', $provider))) {
                    continue;
                }
                $result = $category;
                break;
            }
        }

        return $result;
    }
}
