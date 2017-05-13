<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/11/17
 * Time: 8:58 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Form\Account\AddAccountType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

///**
// * @Route("/user")
// */
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
     * @Route("/accounts/all", name="accounts_all")
     */
    public function allAccountAction(Request $request)
    {
        $accounts = $this->getDoctrine()->getManager()->getRepository(Account::class)->loadAllAccounts();
        return new JsonResponse($accounts);
    }

    //TODO move to api directory
    /**
     * @Route("/accounts/all1", name="accounts_all1")
     */
    public function all1AccountAction(Request $request)
    {
        $accounts = $this->getDoctrine()->getManager()->getRepository(Account::class)->loadAllAccounts();

        return $this->render('account/all_accounts.html.twig', [
            'accounts' => $accounts
        ]);
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

//        return new JsonResponse($account);
    }
}