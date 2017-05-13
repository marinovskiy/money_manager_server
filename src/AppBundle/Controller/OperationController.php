<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Operation;
use AppBundle\Form\Operation\AddOperationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class OperationController extends Controller
{
    /**
     * @Route("/accounts/{id}/operations/add", name="operations_add")
     */
    public function addOperationAction(Request $request, $id)
    {
        $operation = new Operation();
        $form = $this->createForm(AddOperationType::class, $operation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $operation->setAccount($em->getRepository(Account::class)->find($id));

            $em->persist($operation);
            $em->flush();

            return $this->redirectToRoute('profile_me');
        }

        return $this->render('operation/add_operation.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/accounts/{id}/", name="accounts_all")
     */
    public function accountOperationsAction(Request $request, $id)
    {
        $accounts = $this->getDoctrine()->getManager()->getRepository(Account::class)->loadAllAccounts();
        return new JsonResponse($accounts);
    }

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
}