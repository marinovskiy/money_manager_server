<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Organization;
use AppBundle\Entity\User;
use AppBundle\Form\CreateOrganizationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/organizations")
 */
class ApiOrganizationController extends Controller
{
    /**
     * @Route("/new", name="api_organizations_new")
     * @Method({"POST"})
     */
    public function apiNewOrganizationAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $organization = new Organization();
        $form = $this->createForm(CreateOrganizationType::class, $organization);
        $form->submit($data['organization']);

        if ($form->isSubmitted() && $form->isValid()) {
            $organization->setCreator($this->getUser());
            $organization->addMember($this->getUser());
            $organization->setEnabled(true);
            $organization->setCreatedAt(new \DateTime());
            $organization->setUpdatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($organization);
            $em->flush();

            return $this->json(['organization' => $organization], 200);
        }

        return $this->json('Invalid data', 400);
    }

    /**
     * @Route("/all", name="api_organizations_all")
     * @Method("GET")
     */
    public function apiAllOrganizationDetailsAction()
    {
        $organizations = $this->getUser()->getOrganizations();

        return $this->json(['organizations' => $organizations], 200);
    }

    /**
     * @Route("/{id}", name="api_organizations_details")
     * @Method("GET")
     */
    public function apiOrganizationDetailsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $organization = $em->getRepository(Organization::class)->find($id);

        if (!$organization) {
            return $this->json('Not found', 404);
        }

        if (!in_array($this->getUser(), $organization->getMembers()->toArray())) {
            return $this->json('You are not a member', 403);
        }

        return $this->json(['organization' => $organization], 200);
    }

//    /**
//     * @Route("/created", name="api_organizations_creator_all")
//     * @Method({"GET"})
//     */
//    public function apiAllOrganizationsCreatorAction()
//    {
//        $encoders = array(new JsonEncoder());
//        $normalizers = array(new ObjectNormalizer());
//        $serializer = new Serializer($normalizers, $encoders);
//
//        $userId = $this->getUser()->getId();
//
//        $organization = $this
//            ->getDoctrine()
//            ->getManager()
//            ->getRepository(Organization::class)
//            ->loadAllUserCreatorOrganizations($userId);
//
//        $response = new Response(
//            $serializer->serialize(
//                $organization,
//                'json'
//            ),
//            200
//        );
//        $response->headers->set('Content-Type', 'application/json');
//        return $response;
//    }
//
//    /**
//     * @Route("/member", name="api_organizations_members_all")
//     * @Method({"GET"})
//     */
//    public function apiAllOrganizationsMembersAction()
//    {
//        $encoders = array(new JsonEncoder());
//        $normalizers = array(new ObjectNormalizer());
//        $serializer = new Serializer($normalizers, $encoders);
//
//        $userId = $this->getUser()->getId();
//
//        $organization = $this
//            ->getDoctrine()
//            ->getManager()
//            ->getRepository(Organization::class)
//            ->loadAllUserMemberOrganizations($userId);
//
//        $response = new Response(
//            $serializer->serialize(
//                $organization,
//                'json'
//            ),
//            200
//        );
//        $response->headers->set('Content-Type', 'application/json');
//        return $response;
//    }

    /**
     * @Route("/{id}/edit", name="api_organizations_edit")
     * @Method({"PUT"})
     */
    public function apiEditOrganizationAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $organization = $em->getRepository(Organization::class)->find($id);

        if (!$organization) {
            return $this->json('Not found', 404);
        }
        if (!$organization->getEnabled()) {
            return $this->json('Organization is disabled', 404);
        }

        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('You are not creator of this organization', 403);
        }

        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(CreateOrganizationType::class, $organization);
        $form->submit($data['organization']);

        if ($form->isSubmitted() && $form->isValid() && $organization) {
            $organization->setUpdatedAt(new \DateTime());
            $em->flush();
            return $this->json(['organization' => $organization], 200);
        }

        return $this->json('Invalid data', 400);
    }

//    /**
//     * @Route("/{id}/delete", name="api_organizations_delete")
//     * @Method({"DELETE"})
//     */
//    public function apiDeleteOrganizationAction(Request $request, $id)
//    {
//
//    }

    /**
     * @Route("/{id}/disable", name="api_organizations_disable")
     * @Method({"PUT"})
     */
    public function apiDisableOrganizationAction(Request $request, $id)
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
            return $this->json('Disabled', 200);
        }

        return $this->json('Not found', 404);
    }

    /**
     * @Route("/{id}/enable", name="api_organizations_enable")
     * @Method({"PUT"})
     */
    public function apiEnableOrganizationAction(Request $request, $id)
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
            return $this->json('Enabled', 200);
        }

        return $this->json('Not found', 404);
    }

    /**
     * @Route("/{id}/members/{userId}/add", name="api_organizations_members_add")
     * @Method("PUT")
     */
    public function apiOrganizationAddMemberAction(Request $request, $id, $userId)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }
        if (!$organization->getEnabled()) {
            return $this->json('Organization is disabled', 404);
        }

        $user = $em->getRepository(User::class)->find($userId);
        if (!$user) {
            return $this->json('User not found', 404);
        }

        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('You are not creator of this organization', 403);
        }

        $membersIds = array();
        foreach ($organization->getMembers() as $member) {
            array_push($membersIds, $member->getId());
        }

        if (!in_array($userId, $membersIds)) {
            return $this->json('Not a member', 404);
        }
        $logger = $this->get('logger');
        $logger->info('LOGS');
        $logger->info('$members.size = ' . sizeof($organization->getMembers()));
        $logger->info('$membersIds.size = ' . sizeof($membersIds));
        foreach ($membersIds as $membersId) {
            $logger->info('memberId = ' . $membersId);
        }
        $logger->info('userID = ' . $userId);

        $organization->addMember($user);
        $em->flush();

        return $this->json('Added', 200);
    }

    /**
     * @Route("/{id}/members", name="api_organization_members_all")
     * @Method("GET")
     */
    public function apiOrganizationMembersAllAction(Request $request, $id)
    {

    }

    /**
     * @Route("/{id}/members/{userId}/remove", name="api_organizations_members_remove")
     * @Method("DELETE")
     */
    public function apiOrganizationMembersRemoveAction(Request $request, $id, $userId)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }
        if (!$organization->getEnabled()) {
            return $this->json('Organization is disabled', 404);
        }

        $user = $em->getRepository(User::class)->find($userId);
        if (!$user) {
            return $this->json('User not found', 404);
        }

        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('You are not creator of this organization', 403);
        }

        $membersIds = array();
        foreach ($organization->getMembers() as $member) {
            array_push($membersIds, $member->getId());
        }

        if (!in_array($userId, $membersIds)) {
            return $this->json('Not a member', 404);
        }

        $organization->removeMember($user);
        $em->flush();

        return $this->json('Removed', 200);
    }
}