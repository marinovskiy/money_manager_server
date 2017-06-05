<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/users")
 */
class ApiUserController extends Controller
{
    /**
     * @Route("/search", name="api_users_search")
     * @Method("GET")
     */
    public function apiUsersSearchAction(Request $request)
    {
        $email = '%' . $request->query->get('email') . '%';

        $users = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
            ->searchUserByEmail($email);

        return $this->json(['users' => $users], 200);
    }
}