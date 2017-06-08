<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Organization;
use AppBundle\Entity\User;
use AppBundle\Form\UpdateProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class UserController extends Controller
{
    /**
     * @Route("/profile/me", name="profile_me")
     */
    public function profileMeAction(Request $request)
    {
        $accounts = $this->getDoctrine()->getManager()->getRepository(Account::class)->loadAllUserAccounts($this->getUser());
        $organizations = $this->getDoctrine()->getManager()->getRepository(Organization::class)->loadAllOrganizations();


        $accounts = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Account')
            ->findBy(['user' => $this->getUser()->getId()]);

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

    /**
     * @Route("/users/search", name="users_search")
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

    /**
     * @Route("/profile/update", name="profile_user_update")
     */
    public function profileMeUpdateAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UpdateProfileType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('profile_me');
        }

        return $this->render(
            'user/update_profile.html.twig',
            ['form' => $form->createView(), 'user' => $user]
        );
    }
}