<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/9/17
 * Time: 5:46 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Organization;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserController extends Controller
{
    /**
     * @Route("/profile/me", name="profile_me")
     */
    public function profileMeAction(Request $request)
    {
        $accounts = $this->getDoctrine()->getManager()->getRepository(Account::class)->loadAllUserAccounts($this->getUser());
        $organizations = $this->getDoctrine()->getManager()->getRepository(Organization::class)->loadAllOrganizations();

        return $this->render('user/profile.html.twig', [
            'accounts' => $accounts,
            'organizations' => $organizations
        ]);
    }

    /**
 * @Route("/profile/me1", name="profile_me1")
 */
    public function profileMe1Action(Request $request)
    {
        $accounts = $this->getDoctrine()->getManager()->getRepository(Account::class)->loadAllUserAccounts($this->getUser());

        return $this->json(['accounts' => $accounts], 200);
    }
}