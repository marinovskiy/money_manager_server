<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Organization;
use AppBundle\Entity\User;
use AppBundle\Form\Account\AddAccountType;
use AppBundle\Form\CreateOrganizationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/organizations")
 */
class OrganizationController extends Controller
{
    /**
     * @Route("/add", name="organization_create")
     */
    public function createOrganizationAction(Request $request)
    {
        $organization = new Organization();
        $form = $this->createForm(CreateOrganizationType::class, $organization);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $organization->setCreator($this->getUser());
            $organization->addMember($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($organization);
            $em->flush();

            return $this->redirectToRoute('profile_me');
        }

        return $this->render('organization/add_organization.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{id}", name="organization_details", requirements={"id": "\d+"})
     */
    public function organizationDetailsAction(Request $request, $id)
    {
        $organization = $this->getDoctrine()->getManager()->getRepository(Organization::class)->find($id);
        $accounts = $this->getDoctrine()->getManager()->getRepository(Account::class)->loadAllOrganizationAccounts($id);
        return $this->render('organization/organization_details.html.twig', [
            'organization' => $organization,
            'accounts' => $accounts
        ]);
    }

    /**
     * @Route("/{id}/accounts/new", name="organization_create_account")
     */
    public function qAction(Request $request, $id)
    {
        $account = new Account();
        $form = $this->createForm(AddAccountType::class, $account);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $organization = $em->getRepository(Organization::class)->find($id);

            $account->setOrganization($organization);
            $account->setBalance(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute('profile_me');
        }

        return $this->render('account/add_account.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{id}/members/search", name="organization_members_search")
     */
    public function organizationSearchMembersAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $organization = $em->getRepository(Organization::class)->find($id);

        return $this->render('organization/add_member.html.twig', [
            'organization' => $organization
        ]);
    }

    /**
     * @Route("/{id}/members/add", name="organization_member_add")
     */
    public function organizationAddMemberAction(Request $request, $id)
    {
        $userId = $request->request->get('userId');

        $em = $this->getDoctrine()->getManager();
//        $organization = new Organization();
        $organization = $em->getRepository(Organization::class)->find($id);
        $user = $em->getRepository(User::class)->find($userId);

        $logger = $this->get('logger');
        $logger->info('I just got the logger');
        foreach ($organization->getMembers() as $member) {
            $logger->info($member->getId());
        }

        if (!$organization->getMembers()->contains($user)) {
            $logger->info('200');
            $organization->addMember($user);
            $em->flush();

            $logger->info('200');
            return new Response(null, 200);
        }

        $logger->info('409');
        return new Response(null, 409);
    }

    // AJAX

    /**
     * @Route("/qwerty123", name="qwerty123")
     */
    public function qwerty123Action()
    {
        return $this->json(['user' => $this->getUser()]);
    }

//    /**
//     * @Route("/qwerty123", name="qwerty123")
//     */
//    public function qwerty123Action()
//    {
//        $category = $this
//            ->getDoctrine()
//            ->getManager()
//            ->getRepository(Category::class)
//            ->find(1);
//
//        return $this->json(['category' => $category]);
////        return new Response(json_encode($category));
//    }
}