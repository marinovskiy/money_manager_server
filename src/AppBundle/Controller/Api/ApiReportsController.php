<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Account;
use AppBundle\Entity\Category;
use AppBundle\Entity\Operation;
use AppBundle\Entity\Organization;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/report")
 */
class ApiReportsController extends Controller
{
//    /**
//     * @Route("/test", name="api_report")
//     * @Method("GET")
//     */
//    public function apiReportAction1(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $searchType = $request->query->get('searchType');
//        if ($searchType) {
////            if ($searchType != 'user' || $searchType != 'organization') {
////                return $this->json('Wrong search type', 400);
////            }
//
//            $accountsIds = array();
//
//            if ($searchType == 'user') {
//                foreach ($this->getUser()->getAccounts() as $account) {
//                    array_push($accountsIds, $account->getId());
//                }
//            } else if ($searchType == 'organization') {
//                $organizationId = $request->query->get('organizationId');
//                if (!$organizationId) {
//                    return $this->json('No organizationId provided', 400);
//                }
//
//                $organization = $em->getRepository(Organization::class)->find($organizationId);
//                if (!$organization) {
//                    return $this->json('Organization not found', 404);
//                }
//
//                $membersIds = array();
//                foreach ($organization->getMembers() as $member) {
//                    array_push($membersIds, $member->getId());
//                }
//                if (!in_array($this->getUser()->getId(), $membersIds)) {
//                    return $this->json('Not a member', 403);
//                }
//
//                foreach ($organization->getAccounts() as $account) {
//                    array_push($accountsIds, $account->getId());
//                }
//            } else {
//                return $this->json('Wrong search type', 400);
//            }
//        } else {
//            $accountsIds = $request->query->get('accountId');
//        }
//
////        $accounts = $this
////            ->getDoctrine()
////            ->getRepository(Account::class)
////            ->loadAccounts($accountsIds);
//        $type = $request->query->get('operationType');
//        $categoriesIds = $request->query->get('categoryId');
//
//        $categories = array();
//        if ($categoriesIds) {
//            foreach ($categoriesIds as $categoryId) {
//                $category = $em->getRepository(Category::class)->find($categoryId);
//                if (!$category) {
//                    continue;
//                }
//                array_push($categories, $category);
//            }
//        }
//
//        $operations = array();
//
//        foreach ($accountsIds as $accountId) {
//            $account = $em->getRepository(Account::class)->find($accountId);
//            if (!$account) {
//                continue;
//            }
//
//            if ($account->getUser()) {
//                if ($account->getUser()->getId() != $this->getUser()->getId()) {
//                    continue;
//                }
//            } else if ($account->getOrganization()) {
//                $organization = $em->getRepository(Organization::class)->find($account->getOrganization()->getId());
//                if (!$organization) {
//                    continue;
//                }
//
//                $membersIds = array();
//                foreach ($organization->getMembers() as $member) {
//                    array_push($membersIds, $member->getId());
//                }
//                if (!in_array($this->getUser()->getId(), $membersIds)) {
//                    continue;
//                }
//            }
//
//            foreach ($account->getOperations() as $operation) {
//                if ($type) {
//                    if ($operation->getType() == $type) {
//                        array_push($operations, $operation);
//                    }
//                } else if ($categories) {
//                    if (in_array($operation->getCategory(), $categories)) {
//                        array_push($operations, $operation);
//                    }
//                } else {
//                    array_push($operations, $operation);
//                }
//            }
//        }
//
//        return $this->json(['operations' => $operations], 200);
//    }

    /**
     * @Route("/get", name="api_report")
     * @Method("GET")
     */
    public function apiReportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $accounts = array();

        if ($request->query->has('accountId')) {
            $accountId = $request->query->get('accountId');
            $account = $em->getRepository(Account::class)->find($accountId);

            if (!$account) {
                return $this->json(['msg' => 'not found'], 404);
            }
            if ($this->getUser()->getId() != $account->getUser()->getId()) {
                return $this->json(['msg' => 'not owner'], 403);
            }

            array_push($accounts, $account);
        } else if ($request->query->has('organizationId')) {
            $organizationId = $request->query->get('organizationId');
            $organization = $em->getRepository(Organization::class)->find($organizationId);

            if (!$organization) {
                return $this->json(['msg' => 'not found'], 404);
            }

            $membersIds = array();
            foreach ($organization->getMembers() as $member) {
                array_push($membersIds, $member->getId());
            }
            if (!in_array($this->getUser()->getId(), $membersIds)) {
                return $this->json(['msg' => 'not a member'], 403);
            }

            foreach ($organization->getAccounts() as $acc) {
                array_push($accounts, $acc);
            }
        } else {
            if (!$this->getUser()) {
                return $this->json(['msg' => 'bad request'], 400);
            }

            $accounts = $em->getRepository(Account::class)->findBy(['user' => $this->getUser()->getId()]);
        }

        $operationsAll = array();
        $operations = array();
        foreach ($accounts as $acc) {
            foreach ($acc->getOperations() as $opr) {
                array_push($operationsAll, $opr);
            }
        }

        if ($request->query->has('type') || $request->query->has('categoryId') || $request->query->has('fromDate') || $request->query->has('toDate')) {
            $type = $request->query->get('type');
            $categories = $request->query->get('categoryId');
            $fromDate = $request->query->get('fromDate');
            $toDate = $request->query->get('toDate');

            if ($request->query->has('type')) {
                if ($type == 'all') {
                    foreach ($operationsAll as $opr) {
                        array_push($operations, $opr);
                    }
                } else {
                    foreach ($operationsAll as $opr) {
                        if ($opr->getType() == $type) {
                            array_push($operations, $opr);
                        }
                    }
                }
            } else if ($request->query->has('categoryId')) {
                foreach ($operationsAll as $opr) {
                    if (in_array($opr->getCategory()->getId(), $categories)) {
                        array_push($operations, $opr);
                    }
                }
            }

            if ($request->query->has('fromDate') && $request->query->has('toDate')) {
                foreach ($operationsAll as $opr) {
                    if ($opr->getCreatedAt()->getTimestamp() >= $fromDate && $opr->getCreatedAt()->getTimestamp() <= $toDate && !in_array($opr, $operations)) {
                        array_push($operations, $opr);
                    }
                }
            }
        } else {
            $operations = $operationsAll;
        }

        $operationsSumIncome = 0;
        $operationsSumExpense = 0;
        foreach ($operations as $opr) {
            if ($opr->getType() == Operation::TYPE_INCOME) {
                $operationsSumIncome = $operationsSumIncome + $opr->getSum();
            } else {
                $operationsSumExpense = $operationsSumExpense - $opr->getSum();
            }
        }

        return $this->json(['operations' => $operations, 'operationSumIncome' => $operationsSumIncome, 'operationsSumExpense' => $operationsSumExpense], 200);
    }
}