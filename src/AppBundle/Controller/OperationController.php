<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Operation;
use AppBundle\Form\Operation\AddOperationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class OperationController extends Controller
{
    /**
     * @Route("/accounts/{id}/operations/add", name="account_operations_add", requirements={"id": "\d+"})
     */
    public function addAccountOperationAction(Request $request, $id)
    {
        $operation = new Operation();
        $form = $this->createForm(AddOperationType::class, $operation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $account = $em->getRepository(Account::class)->find($id);
            $operation->setAccount($account);

            $balance = $account->getBalance();
            $sum = $operation->getSum();
            if ($operation->getType() == Operation::TYPE_INCOME) {
                $account->setBalance($balance + $sum);
            } else if ($operation->getType() == Operation::TYPE_EXPENSE) {
                $account->setBalance($balance - $sum);
            }

            $operation->setCreatedAt(new \DateTime());
            $operation->setUpdatedAt(new \DateTime());

            $em->persist($account);
            $em->persist($operation);
            $em->flush();

//            return $this->redirectToRoute('profile_me');
            return $this->redirect($this->generateUrl('accounts_details', array('id' => $id)));
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

    //TODO balance
    /**
     * @Route("/accounts/{accountId}/operations/{operationId}/edit", name="account_operations_edit", requirements={"id": "\d+"})
     */
    public function editAccountOperationAction(Request $request, $accountId, $operationId)
    {
        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository(Account::class)->find($accountId);
        $operation = $em->getRepository(Operation::class)->find($operationId);
        $previousType = $operation->getType();
        $balance = $account->getBalance();
        $sum = $operation->getSum();

        $form = $this->createForm(AddOperationType::class, $operation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($balance >= 0) {
                if ($previousType == Operation::TYPE_INCOME) {
                    $account->setBalance($balance - $sum);
                } else if ($previousType == Operation::TYPE_EXPENSE) {
                    $account->setBalance($balance + $sum);
                }
            } else {
                if ($previousType == Operation::TYPE_INCOME) {
                    $account->setBalance($balance - $sum);
                } else if ($previousType == Operation::TYPE_EXPENSE) {
                    $account->setBalance($balance + $sum);
                }
            }

            if ($operation->getType() == Operation::TYPE_INCOME) {
                $account->setBalance($balance + $sum);
            } else if ($operation->getType() == Operation::TYPE_EXPENSE) {
                $account->setBalance($balance - $sum);
            }

            $operation->setUpdatedAt(new \DateTime());

//            $em->refresh($operation);
            $em->flush();

            return $this->redirect($this->generateUrl('accounts_details', array('id' => $accountId)));
        }

        return $this->render('operation/add_operation.html.twig', ['form' => $form->createView(), 'operation' => $operation]);
    }

    /**
     * @Route("/accounts/{accountId}/operations/{operationId}/remove", name="account_operations_remove")
     */
    public function accountOperationRemoveAction(Request $request, $accountId, $operationId)
    {
        $em = $this->getDoctrine()->getManager();

        $account = $em->getRepository(Account::class)->find($accountId);
        $operation = $em->getRepository(Operation::class)->find($operationId);

        if ($account != null && $operation != null) {
            $balance = $account->getBalance();
            $sum = $operation->getSum();
            if ($balance >= 0) {
                if ($operation->getType() == Operation::TYPE_INCOME) {
                    $account->setBalance($balance - $sum);
                } else if ($operation->getType() == Operation::TYPE_EXPENSE) {
                    $account->setBalance($balance + $sum);
                }
            } else {
                if ($operation->getType() == Operation::TYPE_INCOME) {
                    $account->setBalance($balance - $sum);
                } else if ($operation->getType() == Operation::TYPE_EXPENSE) {
                    $account->setBalance($balance + $sum);
                }
            }

            $em->remove($operation);
            $em->flush();

//            return new Response(null, 200);
            return $this->redirect($this->generateUrl('accounts_details', array('id' => $accountId)));
        }

        return new Response(null, 404);
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