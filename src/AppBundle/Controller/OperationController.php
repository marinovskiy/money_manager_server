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
        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository(Account::class)->find($id);
        if (!$account) {
            return new Response("Not found", 404);
        }
//        if ($account->getUser()->getId() != $this->getUser()->getId()) {
//            return new Response("Not owner", 403);
//        }

        $operation = new Operation();
        $form = $this->createForm(AddOperationType::class, $operation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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

            return $this->redirect($this->generateUrl('accounts_details', array('id' => $id)));
        }

        return $this->render('operation/add_operation.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/accounts/{accountId}/operations/{operationId}/edit", name="account_operations_edit", requirements={"id": "\d+"})
     */
    public function editAccountOperationAction(Request $request, $accountId, $operationId)
    {
        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository(Account::class)->find($accountId);
        $operation = $em->getRepository(Operation::class)->find($operationId);
        $previousType = $operation->getType();
        $previousBalance = $account->getBalance();
        $previousSum = $operation->getSum();

        if (!$account) {
            return new Response("Account not found", 404);
        }
        if ($account->getUser()->getId() != $this->getUser()->getId()) {
            return new Response("Not owner", 403);
        }
        if (!$operationId) {
            return new Response("Operation not found", 404);
        }

        $form = $this->createForm(AddOperationType::class, $operation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newSum = $operation->getSum();
            $newBalance = $previousBalance;
            $newType = $operation->getType();

            if ($previousBalance >= 0) {
                if ($previousType == Operation::TYPE_INCOME) {
                    $newBalance = $previousBalance - $previousSum;
                } else if ($previousType == Operation::TYPE_EXPENSE) {
                    $newBalance = $previousBalance + $previousSum;
                }
            } else {
                if ($previousType == Operation::TYPE_INCOME) {
                    $newBalance = $previousBalance - $previousSum;
                } else if ($previousType == Operation::TYPE_EXPENSE) {
                    $newBalance = $previousBalance + $previousSum;
                }
            }

            if ($newType == Operation::TYPE_INCOME) {
                $newBalance = $newBalance + $newSum;
            } else if ($newType == Operation::TYPE_EXPENSE) {
                $newBalance = $newBalance - $newSum;
            }

            $account->setBalance($newBalance);
            $operation->setUpdatedAt(new \DateTime());

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

        if (!$account) {
            return new Response("Account not found", 404);
        }
        if ($account->getUser()->getId() != $this->getUser()->getId()) {
            return new Response("Not owner", 403);
        }
        if (!$operationId) {
            return new Response("Operation not found", 404);
        }

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