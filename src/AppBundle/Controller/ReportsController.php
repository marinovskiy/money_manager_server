<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Category;
use AppBundle\Entity\Operation;
use AppBundle\Entity\Organization;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class ReportsController extends Controller
{
    /**
     * @Route("/report/account/{accountId}/report", name="reportAccount")
     * @Method("GET")
     */
    public function reportAccountAction(Request $request, $accountId)
    {
//        $accountsId = $request->query->get('accountId');

        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository(Account::class)->find($accountId);
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('report/reports.html.twig', array(
            'account' => $account,
            'categories' => $categories
        ));
    }

    /**
     * @Route("/report/accounts/report", name="reportAccounts")
     * @Method("GET")
     */
    public function reportAccountsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('report/report_accounts.html.twig', array(
            'categories' => $categories
        ));
    }

    /**
     * @Route("/report/organization/{organizationId}/report", name="reportOrganization")
     * @Method("GET")
     */
    public function reportOrganizationAction(Request $request, $organizationId)
    {
        $em = $this->getDoctrine()->getManager();
        $organization = $em->getRepository(Organization::class)->find($organizationId);
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('report/report_organization.html.twig', array(
            'organization' => $organization,
            'categories' => $categories
        ));
    }

    /**
     * @Route("/report/account/reportResult", name="reportAccountResult")
     * @Method("GET")
     */
    public function reportAccountResultAction(Request $request)
    {
        $accountId = $request->query->get('accountId');
        $type = $request->query->get('type');
        $categories = $request->query->get('categoryId');
        $fromDate = $request->query->get('fromDate');
        $toDate = $request->query->get('toDate');

        $this->get('logger')->info($type);

        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository(Account::class)->find($accountId[0]);
//        $categories = $em->getRepository(Category::class)->findAll();

        $operationsAll = $account->getOperations();
        $operations = array();

        if ($request->query->has('type')) {
            if ($type == 'all') {
                foreach ($operationsAll as $opr) {
                    array_push($operations, $opr);
                }
            } else {
                foreach ($operationsAll as $opr) {
                    $this->get('logger')->info($opr->getCreatedAt()->getTimestamp());
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
//                $this->get('logger')->info($opr->getCreatedAt()->getTimestamp());
                if ($opr->getCreatedAt()->getTimestamp() >= $fromDate && $opr->getCreatedAt()->getTimestamp() <= $toDate
                    && !in_array($opr, $operations)) {
                    array_push($operations, $opr);
                }
            }
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

        return $this->render('report/reports_result.html.twig', array(
            'account' => $account,
            'operations' => $operations,
            'operationsSumIncome' => $operationsSumIncome,
            'operationsSumExpense' => $operationsSumExpense
        ));
    }

    /**
     * @Route("/report/accounts/reportResult", name="reportAccountsResult")
     * @Method("GET")
     */
    public function reportAccountsResultAction(Request $request)
    {
        $type = $request->query->get('type');
        $categories = $request->query->get('categoryId');
        $fromDate = $request->query->get('fromDate');
        $toDate = $request->query->get('toDate');

        $this->get('logger')->info($type);

        $accounts = $this->getUser()->getAccounts();

        $operationsAll = array();
        foreach ($accounts as $acc) {
            foreach ($acc->getOperations() as $opr) {
                array_push($operationsAll, $opr);
            }
        }

        $operations = array();

        if ($request->query->has('type')) {
            if ($type == 'all') {
                foreach ($operationsAll as $opr) {
                    array_push($operations, $opr);
                }
            } else {
                foreach ($operationsAll as $opr) {
                    $this->get('logger')->info($opr->getCreatedAt()->getTimestamp());
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
                if ($opr->getCreatedAt()->getTimestamp() >= $fromDate && $opr->getCreatedAt()->getTimestamp() <= $toDate
                    && !in_array($opr, $operations)) {
                    array_push($operations, $opr);
                }
            }
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

        return $this->render('report/reports_result.html.twig', array(
            'operations' => $operations,
            'operationsSumIncome' => $operationsSumIncome,
            'operationsSumExpense' => $operationsSumExpense
        ));
    }

    /**
     * @Route("/report/organization/reportResult", name="reportOrganizationResult")
     * @Method("GET")
     */
    public function reportOrganizationResultAction(Request $request)
    {
        $organizationId = $request->query->get('organizationId');
        $type = $request->query->get('type');
        $categories = $request->query->get('categoryId');
        $fromDate = $request->query->get('fromDate');
        $toDate = $request->query->get('toDate');

        $this->get('logger')->info($type);

        $em = $this->getDoctrine()->getManager();
        $organization = $em->getRepository(Organization::class)->find($organizationId);
        $accounts = $organization->getAccounts();

        $operationsAll = array();
        foreach ($accounts as $acc) {
            foreach ($acc->getOperations() as $opr) {
                array_push($operationsAll, $opr);
            }
        }

        $operations = array();

        if ($request->query->has('type')) {
            if ($type == 'all') {
                foreach ($operationsAll as $opr) {
                    array_push($operations, $opr);
                }
            } else {
                foreach ($operationsAll as $opr) {
                    $this->get('logger')->info($opr->getCreatedAt()->getTimestamp());
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
                if ($opr->getCreatedAt()->getTimestamp() >= $fromDate && $opr->getCreatedAt()->getTimestamp() <= $toDate
                    && !in_array($opr, $operations)) {
                    array_push($operations, $opr);
                }
            }
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

        return $this->render('report/reports_result.html.twig', array(
            'organization' => $organization,
            'operations' => $operations,
            'operationsSumIncome' => $operationsSumIncome,
            'operationsSumExpense' => $operationsSumExpense
        ));
    }
}