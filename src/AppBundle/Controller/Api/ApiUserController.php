<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Form\UpdateProfileType;
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

    /**
     * @Route("/edit", name="api_user_edit")
     * @Method("PUT")
     */
    public function profileUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getUser()->getId());

        $form = $this->createForm(UpdateProfileType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->json(['user' => $user], 200);
        }

        return $this->json('Invalid data', 400);
    }
}