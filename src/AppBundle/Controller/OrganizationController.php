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
            $organization->setEnabled(true);
            $organization->setCreatedAt(new \DateTime());
            $organization->setUpdatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($organization);
            $em->flush();

            return $this->redirectToRoute('profile_me');
        }

        return $this->render('organization/add_organization.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{id}/edit", name="organization_edit")
     */
    public function editOrganizationAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return new Response('Organization not found', 404);
        }
        if (!$organization->getEnabled()) {
            return $this->json('Organization is disabled', 404);
        }

        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return new Response('Not creator', 403);
        }

        $form = $this->createForm(CreateOrganizationType::class, $organization);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $organization->setUpdatedAt(new \DateTime());
            $em->flush();

            return $this->redirectToRoute('organization_details', array(
                'id' => $organization->getId()
            ));
        }

        return $this->render('organization/add_organization.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{id}", name="organization_details")
     */
    public function organizationDetailsAction(Request $request, $id)
    {
        $organization = $this->getDoctrine()->getManager()->getRepository(Organization::class)->find($id);
//        $accounts = $this->getDoctrine()->getManager()->getRepository(Account::class)->loadAllOrganizationAccounts($id);

        if (!$organization) {
            return $this->json('Organization not found', 404);
        }
//        if (!$organization->getEnabled()) {
//            return $this->json('Organization is disabled', 404);
//        }

        $membersIds = array();
        foreach ($organization->getMembers() as $member) {
            array_push($membersIds, $member->getId());
        }

        if (!in_array($this->getUser()->getId(), $membersIds)) {
            return $this->json('Not a member', 404);
        }

        $accounts = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Account')
            ->findBy(['organization' => $id]);

        return $this->render('organization/organization_details.html.twig', [
            'organization' => $organization,
            'accounts' => $accounts
        ]);
    }

    /**
     * @Route("/{id}/accounts/new", name="organization_create_account")
     */
    public function organizationAddAccountAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('Not creator', 403);
        }

        $account = new Account();
        $form = $this->createForm(AddAccountType::class, $account);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $account->setOrganization($organization);
            $account->setBalance(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            return $this->redirect($this->generateUrl('organization_details', array('id' => $id)));
        }

        return $this->render('account/add_account.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{id}/accounts/{accountId}/remove", name="organization_account_remove")
     */
    public function organizationAccountRemoveAction(Request $request, $id, $accountId)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('Not creator', 403);
        }

        $account = $em->getRepository(Account::class)->find($accountId);

        if ($account != null) {
            $em->remove($account);
            $em->flush();

            return $this->redirect($this->generateUrl('organization_details', ['id' => $id]));
        }

        return new Response(null, 404);
    }

    /**
     * @Route("/{id}/accounts/{accountId}/edit", name="organization_account_edit")
     */
    public function organizationEditAccountAction(Request $request, $id, $accountId)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('Not creator', 403);
        }

        $account = $em->getRepository(Account::class)->find($accountId);

        $form = $this->createForm(AddAccountType::class, $account);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('organization_details', ['id' => $id]));
        }

        return $this->render('account/add_account.html.twig', array('form' => $form->createView(), 'account' => $account));
    }

    /**
     * @Route("/{id}/members/search", name="organization_members_search")
     */
    public function organizationSearchMembersAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('Not creator', 403);
        }

        return $this->render('organization/add_member.html.twig', [
            'organization' => $organization
        ]);
    }

    /**
     * @Route("/{id}/members/add", name="organization_member_add")
     */
    public function organizationAddMemberAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('Not creator', 403);
        }

        $userId = $request->request->get('userId');

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }
//        if (!$organization->getEnabled()) {
//            return $this->json('Organization is disabled', 400);
//        }

        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('Not creator', 403);
        }

        $user = $em->getRepository(User::class)->find($userId);
        if (!$user) {
            return $this->json('User not found', 404);
        }

        if ($organization->getMembers()->contains($user)) {
            return $this->json('Already member', 409);
        }

        $organization->addMember($user);
        $em->flush();
        return new Response(null, 200);
    }

    /**
     * @Route("/{id}/members/{userId}/delete", name="organization_member_delete")
     */
    public function organizationDeleteMemberAction(Request $request, $id, $userId)
    {
//        $userId = $request->request->get('userId');

        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }
//        if (!$organization->getEnabled()) {
//            return $this->json('Organization is disabled', 404);
//        }

        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('Not creator', 403);
        }

        $user = $em->getRepository(User::class)->find($userId);
        if (!$user) {
            return $this->json('User not found', 404);
        }

        if (!$organization->getMembers()->contains($user)) {
            return $this->json('Already removed', 409);
        }

        if ($organization->getCreator()->getId() == $user->getId()) {
            return $this->json('Creator can not be removed', 403);
        }

        $organization->removeMember($user);
//        $this->getUser()->removeOrganization($organization);
        $em->flush();

        return $this->redirectToRoute('organization_details', ['id' => $id]);
    }

    /**
     * @Route("/{id}/exit", name="organization_exit")
     */
    public function organizationExitAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }
//        if (!$organization->getEnabled()) {
//            return $this->json('Organization is disabled', 404);
//        }

        $membersIds = array();
        foreach ($organization->getMembers() as $member) {
            array_push($membersIds, $member->getId());
        }
        if (!in_array($this->getUser()->getId(), $membersIds)) {
            return $this->json('Not a member', 404);
        }

        if (!$organization->getMembers()->contains($this->getUser())) {
            return $this->json('Already exit', 409);
        }

        if ($organization->getCreator()->getId() == $this->getUser()->getId()) {
            return $this->json('Creator can not exit', 400);
        }

        $organization->removeMember($this->getUser());
//        $this->getUser()->removeOrganization($organization);
        $em->flush();

        return $this->redirectToRoute('profile_me');
    }

    /**
     * @Route("/{id}/disable", name="organizations_disable")
     */
    public function disableOrganizationAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $organization = $em->getRepository(Organization::class)->find($id);

        if ($organization) {
            if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
                return $this->json('You are not creator of this organization', 403);
            }
            if (!$organization->getEnabled()) {
                return $this->json('Already disabled', 400);
            }
            $organization->setEnabled(false);
            $em->flush();
            return $this->redirectToRoute('organization_details', ['id' => $id]);
        }

        return $this->json('Not found', 404);
    }

    /**
     * @Route("/{id}/enable", name="organizations_enable")
     */
    public function enableOrganizationAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $organization = $em->getRepository(Organization::class)->find($id);

        if ($organization) {
            if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
                return $this->json('You are not creator of this organization', 403);
            }
            if ($organization->getEnabled()) {
                return $this->json('Already enabled', 400);
            }
            $organization->setEnabled(true);
            $em->flush();
            return $this->redirectToRoute('organization_details', ['id' => $id]);
        }

        return $this->json('Not found', 404);
    }

    /**
     * @Route("/{id}/enabled", name="organizations_enabled")
     */
    public function enabledOrganizationAccount(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $organization = $em->getRepository(Organization::class)->find($id);

        if ($organization) {
            $membersIds = array();
            foreach ($organization->getMembers() as $member) {
                array_push($membersIds, $member->getId());
            }
            if (!in_array($this->getUser()->getId(), $membersIds)) {
                return $this->json('Not a member', 404);
            }

            return $this->json(['enabled' => $organization->getEnabled()], 400);
        }

        return $this->json('Not found', 404);
    }

    // AJAX
    /**
     * @Route("/qwerty123", name="qwerty123")
     */
    public function qwerty123Action()
    {
        return $this->json(['user' => $this->getUser()]);
    }
}