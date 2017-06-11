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
use AppBundle\Entity\Organization;
use AppBundle\Form\Operation\AddOperationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/organizations")
 */
class ApiOrganizationOperationController extends Controller
{
    /**
     * @Route("/{id}/accounts/{accountId}/operations/add", name="api_organizations_account_operations_add")
     */
    public function apiOrganizationAccountOperationsAddAction(Request $request, $id, $accountId)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }

        $membersIds = array();
        foreach ($organization->getMembers() as $member) {
            array_push($membersIds, $member->getId());
        }
        if (!in_array($this->getUser()->getId(), $membersIds)) {
            return $this->json('Not a member', 404);
        }

        $account = $em->getRepository(Account::class)->find($accountId);
        if (!$account) {
            return $this->json('Account not found', 404);
        }

        $data = json_decode($request->getContent(), true);

        $operation = new Operation();
        $form = $this->createForm(AddOperationType::class, $operation);
        $form->submit($data['operation']);

        if ($account && $form->isSubmitted() && $form->isValid()) {
            if ($operation->getType() != $operation->getCategory()->getType()) {
                return $this->json('Wrong operation/category type', 400);
            }

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
     * @Route("/{id}/accounts/{accountId}/operations", name="api_organizations_account_operations_list")
     * @Method("GET")
     */
    public function apiOrganizationAccountOperationsListAction(Request $request, $id, $accountId)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }

        $membersIds = array();
        foreach ($organization->getMembers() as $member) {
            array_push($membersIds, $member->getId());
        }
        if (!in_array($this->getUser()->getId(), $membersIds)) {
            return $this->json('Not a member', 404);
        }

        $account = $em->getRepository(Account::class)->find($accountId);
        if (!$account) {
            return $this->json('Account not found', 404);
        }

        return $this->json(['operations' => $account->getOperations()], 200);
    }

    /**
     * @Route("/{id}/accounts/{accountId}/operations/{operationId}/edit", name="api_organizations_account_operations_edit")
     * @Method({"PUT"})
     */
    public function apiOrganizationAccountOperationsEditAction(Request $request, $id, $accountId, $operationId)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }

        $membersIds = array();
        foreach ($organization->getMembers() as $member) {
            array_push($membersIds, $member->getId());
        }
        if (!in_array($this->getUser()->getId(), $membersIds)) {
            return $this->json('Not a member', 404);
        }

        $account = $em->getRepository(Account::class)->find($accountId);
        if (!$account) {
            return $this->json('Account not found', 404);
        }

        $operation = $em->getRepository(Operation::class)->find($operationId);
        if (!$operation) {
            return $this->json('Operation not found', 404);
        }

        $previousType = $operation->getType();
        $previousBalance = $account->getBalance();
        $previousSum = $operation->getSum();

        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(AddOperationType::class, $operation);
        $form->submit($data['operation']);

        if ($account != null && $form->isSubmitted() && $form->isValid()) {
            if ($operation->getType() != $operation->getCategory()->getType()) {
                return $this->json('Wrong operation/category type', 400);
            }
            
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
     * @Route("/{id}/accounts/{accountId}/operations/{operationId}/delete", name="api_organizations_account_operations_delete")
     * @Method({"DELETE"})
     */
    public function apiOrganizationAccountOperationsDeleteAction(Request $request, $id, $accountId, $operationId)
    {
        $em = $this->getDoctrine()->getManager();

        $organization = $em->getRepository(Organization::class)->find($id);
        if (!$organization) {
            return $this->json('Organization not found', 404);
        }

        $membersIds = array();
        foreach ($organization->getMembers() as $member) {
            array_push($membersIds, $member->getId());
        }
        if (!in_array($this->getUser()->getId(), $membersIds)) {
            return $this->json('Not a member', 404);
        }

        $account = $em->getRepository(Account::class)->find($accountId);
        if (!$account) {
            return $this->json('Account not found', 404);
        }

        $operation = $em->getRepository(Operation::class)->find($operationId);
        if (!$operation) {
            return $this->json('Operation not found', 404);
        }

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

        return new $this->json(['msg' => 'Deleted'], 200);
    }
}