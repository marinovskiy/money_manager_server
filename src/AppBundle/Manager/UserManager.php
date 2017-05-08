<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/8/17
 * Time: 9:22 AM
 */

namespace AppBundle\Manager;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class UserManager
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function registerUser(User $user)
    {
        $user
            ->setRole(User::ROLE_USER)
            ->setEnabled(true)
            ->setApiKey();
        return $user;
    }

    public function findUserByEmail($email)
    {
        return $this
            ->entityManager
            ->getRepository('AppBundle\Entity\User')
            ->createQueryBuilder('user')
            ->where('user.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();

//        return $this->createQueryBuilder('user')
//            ->where('user.email = :email')
//            ->setParameter('email', $email)
//            ->getQuery()
//            ->getOneOrNullResult();
    }
}