<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\News;
use AppBundle\Entity\Organization;
use AppBundle\Entity\User;
use AppBundle\Form\UpdateProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/users", name="admin_users")
     */
    public function adminUsersAction(Request $request)
    {
        $users = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('admin/admin_users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/newses", name="admin_newses")
     */
    public function adminNewsesACtion(Request $request)
    {
        $newses = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(News::class)
            ->findAll();

        return $this->render('admin/admin_newses.html.twig', [
            'newses' => $newses
        ]);
    }
}