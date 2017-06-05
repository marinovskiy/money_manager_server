<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function loadUserByEmail($email)
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function searchUserByEmail($email)
    {
        $qb = $this->createQueryBuilder('u');
        return $qb
            ->where(
                $qb->expr()->like('u.email', ':email')
            )
            ->setParameter('email', $email)
            ->getQuery()
            ->getArrayResult();
    }
}