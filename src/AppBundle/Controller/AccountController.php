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
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

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
            $account->setCurrency('USD');
            $account->setBalance(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('account/add_account.html.twig', array('form' => $form->createView()));
    }
}