<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Account;
use AppBundle\Entity\Category;
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
    /**
     * @Route("/", name="api_report")
     * @Method("GET")
     */
    public function apiReportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $accountsIds = $request->query->get('accountId');
        $type = $request->query->get('operationType');
        $categoriesIds = $request->query->get('categoryId');

        $logger = $this->get('logger');
        $logger->info('LOGS');
        $logger->info('type' . $type);
        if ($type) {
            $logger->info('LOGS');
        }

        $categories = array();
        if ($categoriesIds) {
            $logger->info('LOGS555');
            foreach ($categoriesIds as $categoryId) {
                $category = $em->getRepository(Category::class)->find($categoryId);
                if (!$category) {
                    continue;
                }
                array_push($categories, $category);
            }
        }

//        $accounts = $this
//            ->getDoctrine()
//            ->getRepository(Account::class)
//            ->loadAccounts($accountsIds);

        $operations = array();

        foreach ($accountsIds as $accountId) {
            $account = $em->getRepository(Account::class)->find($accountId);
            if (!$account) {
                continue;
//                return $this->json('Account not found', 404);
            }

            if ($account->getUser()) {
                if ($account->getUser()->getId() != $this->getUser()->getId()) {
                    continue;
//                    return $this->json('Not owner', 403);
                }
            } else if ($account->getOrganization()) {
                $organization = $em->getRepository(Organization::class)->find($account->getOrganization()->getId());
                if (!$organization) {
                    continue;
//                    return $this->json('Organization not found', 404);
                }

                $membersIds = array();
                foreach ($organization->getMembers() as $member) {
                    array_push($membersIds, $member->getId());
                }
                if (!in_array($this->getUser()->getId(), $membersIds)) {
                    continue;
//                    return $this->json('Not a member', 403);
                }
            }

            foreach ($account->getOperations() as $operation) {
                $logger->info('LOGS0');
                if ($type) {
                    $logger->info('LOGS1');
                    if ($operation->getType() == $type) {
                        array_push($operations, $operation);
                    }
                } else if ($categories) {
                    $logger->info('LOGS2');
                    if (in_array($operation->getCategory(), $categories)) {
                        array_push($operations, $operation);
                    }
                } else {
                    $logger->info('LOGS3');
                    array_push($operations, $operation);
                }
            }
        }

        return $this->json(['operations' => $operations], 200);
    }
}