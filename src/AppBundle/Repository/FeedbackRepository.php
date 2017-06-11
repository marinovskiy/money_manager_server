<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FeedbackRepository extends EntityRepository
{
    public function feedbackList()
    {
        return $this
            ->createQueryBuilder('f')
            ->orderBy('f.createdAt', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}