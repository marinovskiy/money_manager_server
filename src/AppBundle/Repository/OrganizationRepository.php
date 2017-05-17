<?php

namespace AppBundle\Repository;

class OrganizationRepository extends \Doctrine\ORM\EntityRepository
{
    public function loadAllOrganizations()
    {
        return $this->findAll();
    }

    public function loadAllUserOrganizations($userId)
    {
        return $this
            ->createQueryBuilder('organization')
            ->where('organization.user =:userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getArrayResult();
    }
}