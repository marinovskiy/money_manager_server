<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Account;
use AppBundle\Entity\User;

/**
 * AccountRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountRepository extends \Doctrine\ORM\EntityRepository
{
    public function loadAllAccounts()
    {
        return $this->findAll();
    }

    public function loadAllUserAccounts($userId)
    {
//        return $this
//            ->createQueryBuilder('account')
//            ->where('account.user =:userId')
//            ->setParameter('userId', $userId)
//            ->getQuery()
////            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
//            ->getArrayResult();
        return $this
            ->createQueryBuilder('account')
            ->where('account.user =:userId')
            ->setParameter('userId', $userId)
            ->getQuery()
//            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            ->getArrayResult();
    }

    public function loadDetailsAccount($userId, $accountId)
    {
        return $this
            ->createQueryBuilder('account')
            ->where('account.user =:userId')
            ->andWhere('account.id =:accountId')
            ->setParameter('userId', $userId)
            ->setParameter('accountId', $accountId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function loadAllOrganizationAccounts($organizationId)
    {
        return $this
            ->createQueryBuilder('account')
            ->where('account.organization =:organizationId')
            ->setParameter('organizationId', $organizationId)
            ->getQuery()
            ->getArrayResult();
    }
}