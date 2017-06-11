<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Account;
use AppBundle\Entity\Organization;
use AppBundle\Form\Account\AddAccountType;
use HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/organizations")
 */
class ApiOrganizationAccountController extends Controller
{
    /**
     * @Route("/{id}/accounts/new", name="api_organization_accounts_add")
     * @Method({"POST"})
     */
    public function apiOrganizationNewAccountAction(Request $request, $id)
    {
//        $encoders = array(new JsonEncoder());
//        $normalizer = new ObjectNormalizer(null);
////        $normalizer->setIgnoredAttributes(array('email'));
//        $normalizer->setCircularReferenceHandler(function ($object) {
//            return $object->getId();
//        });
//        $normalizers = array($normalizer);
//        $serializer = new Serializer($normalizers, $encoders);

        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }
        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('Not creator', 403);
        }

//        $membersIds = array();
//        foreach ($organization->getMembers() as $member) {
//            array_push($membersIds, $member->getId());
//        }
//        if (!in_array($this->getUser()->getId(), $membersIds)) {
//            return $this->json('Not a member', 404);
//        }

        $data = json_decode($request->getContent(), true);

        $account = new Account();
        $form = $this->createForm(AddAccountType::class, $account);
        $form->submit($data['account']);

        if ($form->isSubmitted() && $form->isValid()) {
            $account->setOrganization($organization);
            $account->setBalance(0);
            $account->setCreatedAt(new \DateTime());
            $account->setUpdatedAt(new \DateTime());

            $em->persist($organization);
            $em->persist($account);
            $em->flush();

            $this->get('logger')->info("test");

//            return $this->json(['account' => $account], 200, [], [AbstractNormalizer::GROUPS => ['group2']]);
            return $this->json(['account' => $account], 200);
//            return new JsonResponse($account);

//            return new Response($serializer->serialize($account, 'json'));
        }

        return $this->json('Invalid data', 400);
    }

    /**
     * @Route("/{id}/accounts/all", name="api_organization_accounts_all")
     * @Method({"GET"})
     */
    public function apiOrganizationAllAccountsAction($id)
    {
        $accounts = $this
            ->getDoctrine()
            ->getRepository(Organization::class)
            ->find($id)
            ->getAccounts();
        return $this->json(['accounts' => $accounts], 200);
    }

    /**
     * @Route("/{id}/accounts/{accountId}/edit", name="api_organization_accounts_edit")
     * @Method({"PUT"})
     */
    public function apiOrganizationEditAccountAction(Request $request, $id, $accountId)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }
        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('Not creator', 403);
        }

//        $membersIds = array();
//        foreach ($organization->getMembers() as $member) {
//            array_push($membersIds, $member->getId());
//        }
//        if (!in_array($this->getUser()->getId(), $membersIds)) {
//            return $this->json('Not a member', 404);
//        }

        $account = $em->getRepository(Account::class)->find($accountId);
        if (!$account) {
            return $this->json('Account not found', 404);
        }

        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(AddAccountType::class, $account);
        $form->submit($data['account']);

        if ($account != null && $form->isSubmitted() && $form->isValid()) {
            $account->setUpdatedAt(new \DateTime());
            $em->flush();
            return $this->json(['account' => $account], 200);
        }

        return $this->json('Invalid data', 400);
    }

    /**
     * @Route("/{id}/accounts/{accountId}/delete", name="api_organization_accounts_delete")
     * @Method({"DELETE"})
     */
    public function apiOrganizationDeleteAccountAction(Request $request, $id, $accountId)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }
        if ($organization->getCreator()->getId() != $this->getUser()->getId()) {
            return $this->json('Not creator', 403);
        }

//        $membersIds = array();
//        foreach ($organization->getMembers() as $member) {
//            array_push($membersIds, $member->getId());
//        }
//        if (!in_array($this->getUser()->getId(), $membersIds)) {
//            return $this->json('Not a member', 404);
//        }

        $account = $em->getRepository(Account::class)->find($accountId);
        if (!$account) {
            return $this->json('Account not found', 404);
        }

        $em->remove($account);
        $em->flush();

        return $this->json(['msg' => 'Deleted'], 200);
    }

    /**
     * @Route("/{id}/accounts/{accountId}/details", name="api_organization_account_details")
     * @Method("GET")
     */
    public function apiOrganizationAccountAction($id, $accountId)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }

        $membersIds = array();
        foreach ($organization->getMembers() as $member) {
            array_push($membersIds, $member->getId());
        }
        if (!in_array($this->getUser()->getId(), $membersIds)) {
            return $this->json('Not a member', 404);
        }

        $account = $em->getRepository(Account::class)->find($accountId);
        if (!$account) {
            return $this->json('Account not found', 404);
        }

        return $this->json(['account' => $account], 200);
    }
}