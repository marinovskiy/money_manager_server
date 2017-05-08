<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/8/17
 * Time: 9:22 AM
 */

namespace AppBundle\Manager;

use AppBundle\Entity\User;

class UserManager
{
    public function registerUser(User $user)
    {
        $user
            ->setRole(User::ROLE_USER)
            ->setEnabled(true);
//            ->setApiKey();
        return $user;
    }
}