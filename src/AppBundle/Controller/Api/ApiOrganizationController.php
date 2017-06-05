<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Organization;
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

        $logger = $this->get('logger');
        $logger->info('LOG INFO BEGIN');
        foreach ($form->getErrors() as $err) {
            echo $err->getMessage();
            $logger->info($err->getMessage());
        }
        $logger->info('LOG INFO END');

        if ($form->isSubmitted() && $form->isValid()) {
            $organization->setCreator($this->getUser());
            $organization->addMember($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($organization);
            $em->flush();

            return $this->json(['organization   ' => $organization], 200);
        }

        return $this->json('Invalid data', 400);
    }

    /**
     * @Route("/created", name="api_organizations_creator_all")
     * @Method({"GET"})
     */
    public function apiAllOrganizationsCreatorAction()
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $userId = $this->getUser()->getId();

        $organization = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Organization::class)
            ->loadAllUserCreatorOrganizations($userId);

        $response = new Response(
            $serializer->serialize(
                $organization,
                'json'
            ),
            200
        );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/member", name="api_organizations_members_all")
     * @Method({"GET"})
     */
    public function apiAllOrganizationsMembersAction()
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $userId = $this->getUser()->getId();

        $organization = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Organization::class)
            ->loadAllUserMemberOrganizations($userId);

        $response = new Response(
            $serializer->serialize(
                $organization,
                'json'
            ),
            200
        );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}/edit", name="api_organizations_edit")
     * @Method({"PUT"})
     */
    public function apiEditOrganizationAction(Request $request, $id)
    {

    }

    /**
     * @Route("/{id}/delete", name="api_organizations_delete")
     * @Method({"DELETE"})
     */
    public function apiDeleteOrganizationAction(Request $request, $id)
    {

    }

    /**
     * @Route("/{id}/members/add", name="api_organizations_members_add")
     * @Method("POST")
     */
    public function apiOrganizationAddMemberAction(Request $request, $id)
    {
        
    }

    /**
     * @Route("/{id}/members", name="api_organization_members_all")
     * @Method("GET")
     */
    public function apiOrganizationMembersAllAction(Request $request, $id)
    {

    }

    /**
     * @Route("/{id}/members/remove", name="api_organizations_members_remove")
     * @Method("DELETE")
     */
    public function apiOrganizationMembersRemoveAction(Request $request, $id)
    {

    }
}