<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/8/17
 * Time: 1:41 PM
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Account;
use AppBundle\Entity\Operation;
use AppBundle\Form\Operation\AddOperationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/accounts")
 */
class ApiOperationController extends Controller
{
    /**
     * @Route("/{accountId}/operations/add", name="api_account_operations_add")
     */
    public function apiAccountOperationsAddAction(Request $request, $accountId)
    {
        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository(Account::class)->find($accountId);

        $data = json_decode($request->getContent(), true);

        $operation = new Operation();
        $form = $this->createForm(AddOperationType::class, $operation);
        $form->submit($data['operation']);

        if ($account && $form->isSubmitted() && $form->isValid()) {
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

            $em->persist($operation);
            $em->flush();

            return $this->json(['account' => $account], 200);
        }

        return $this->json('Invalid data', 400);
    }

    /**
     * @Route("/{accountId}/operations", name="api_account_operations_list")
     * @Method("GET")
     */
    public function apiAccountOperationsListAction(Request $request, $accountId)
    {
        $account = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Account::class)
            ->find($accountId);

        if ($account) {
            return $this->json(['operations' => $account->getOperations()], 200);
        }

        return $this->json('Account not found', 404);
    }

    /**
     * @Route("/{accountId}/operations/{operationId}/edit", name="api_account_operations_edit")
     * @Method({"PUT"})
     */
    public function apiAccountOperationsEditAction(Request $request, $accountId, $operationId)
    {
        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository(Account::class)->find($accountId);
        $operation = $em->getRepository(Operation::class)->find($operationId);
        $previousType = $operation->getType();
        $previousBalance = $account->getBalance();
        $previousSum = $operation->getSum();

        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(AddOperationType::class, $operation);
        $form->submit($data['operation']);

        if ($account != null && $form->isSubmitted() && $form->isValid()) {
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

            $em->flush();
            return $this->json(['operation' => $operation], 200);
        }

        return $this->json('Invalid data', 400);
    }

    /**
     * @Route("/{accountId}/operations/{operationId}/delete", name="api_account_operations_delete")
     * @Method({"DELETE"})
     */
    public function apiAccountOperationsDeleteAction(Request $request, $accountId, $operationId)
    {
        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository(Account::class)->find($accountId);

        if ($account != null) {
            $operation = $em->getRepository(Operation::class)->find($operationId);

            if ($operation) {
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
            }

            return new Response('Successful deleted', 200);
        }

        return $this->json('Invalid data', 400);
    }
}