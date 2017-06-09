<?php

namespace AppBundle\Repository;

class OrganizationRepository extends \Doctrine\ORM\EntityRepository
{
    public function loadAllOrganizations()
    {
        return $this->findAll();
    }

    public function loadAllUserCreatorOrganizations($userId)
    {
        return $this
            ->createQueryBuilder('organization')
            ->where('organization.creator =:userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getArrayResult();
    }

    public function loadAllUserMemberOrganizations($userId)
    {
        $qb = $this->createQueryBuilder('organization');
        return $qb
            ->where(
                $qb->expr()->in('organization.members', ':userId')
            )
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getArrayResult();
    }
}