<?php

namespace AppBundle\Repository;

class CurrencyRepository extends \Doctrine\ORM\EntityRepository
{
    public function loadCurrencies()
    {
        return $this
            ->createQueryBuilder('currency')
            ->getQuery()
            ->getArrayResult();
    }
}