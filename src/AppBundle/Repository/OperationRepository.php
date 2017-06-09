<?php

namespace AppBundle\Repository;

class OperationRepository extends \Doctrine\ORM\EntityRepository
{
    public function loadAllOperations()
    {
        return $this->findAll();
    }

    public function loadAllAccountOperations($accountId)
    {
        return $this
            ->createQueryBuilder("operation")
            ->where('operation.account =:accountId')
            ->setParameter('accountId', $accountId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}