<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Operation;
use AppBundle\Form\Account\AddAccountType;
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
            ->loadDetailsAccount($this->getUser()->getId(), $id);

        return $this->render('account/account_details.html.twig', array(
            'account' => $account
        ));
    }
}