<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Operation;
use AppBundle\Form\Account\AddAccountType;
use AppBundle\Form\Operation\AddOperationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    /**
     * @Route("/accounts/add", name="accounts_add")
     */
    public function addAccountAction(Request $request)
    {
        $account = new Account();
        $form = $this->createForm(AddAccountType::class, $account);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $account->setUser($this->getUser());
            $account->setBalance(0);
            $account->setCreatedAt(new \DateTime());
            $account->setUpdatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute('profile_me');
        }

        return $this->render('account/add_account.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/accounts/{id}", name="accounts_details")
     */
    public function accountDetailsAction($id)
    {
        $account = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Account::class)
            ->find($id);
//            ->loadDetailsAccount($this->getUser()->getId(), $id);

        if (!$account) {
            return new Response(null, 404);
        }

        return $this->render('account/account_details.html.twig', array(
            'account' => $account
        ));
    }

    /**
     * @Route("/accounts/{accountId}/edit", name="account_edit")
     */
    public function editAccountAction(Request $request, $accountId)
    {
        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository(Account::class)->find($accountId);

        $form = $this->createForm(AddAccountType::class, $account);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $account->setUpdatedAt(new \DateTime());
            $em->flush();
            return $this->redirect($this->generateUrl('profile_me'));
        }

        return $this->render('account/add_account.html.twig', array('form' => $form->createView(), 'account' => $account));
    }

    /**
     * @Route("/accounts/{accountId}/remove", name="account_remove")
     */
    public function accountRemoveAction(Request $request, $accountId)
    {
        $em = $this->getDoctrine()->getManager();

        $account = $em->getRepository(Account::class)->find($accountId);

        if ($account != null) {
            $em->remove($account);
            $em->flush();

            return $this->redirect($this->generateUrl('profile_me'));
        }

        return new Response(null, 404);
    }
}