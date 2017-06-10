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
    //TODO date
    /**
     * @Route("/", name="api_report")
     * @Method("GET")
     */
    public function apiReportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $searchType = $request->query->get('searchType');
        if ($searchType) {
//            if ($searchType != 'user' || $searchType != 'organization') {
//                return $this->json('Wrong search type', 400);
//            }

            $accountsIds = array();

            if ($searchType == 'user') {
                foreach ($this->getUser()->getAccounts() as $account) {
                    array_push($accountsIds, $account->getId());
                }
            } else if ($searchType == 'organization') {
                $organizationId = $request->query->get('organizationId');
                if (!$organizationId) {
                    return $this->json('No organizationId provided', 400);
                }

                $organization = $em->getRepository(Organization::class)->find($organizationId);
                if (!$organization) {
                    return $this->json('Organization not found', 404);
                }

                $membersIds = array();
                foreach ($organization->getMembers() as $member) {
                    array_push($membersIds, $member->getId());
                }
                if (!in_array($this->getUser()->getId(), $membersIds)) {
                    return $this->json('Not a member', 403);
                }

                foreach ($organization->getAccounts() as $account) {
                    array_push($accountsIds, $account->getId());
                }
            } else {
                return $this->json('Wrong search type', 400);
            }
        } else {
            $accountsIds = $request->query->get('accountId');
        }

//        $accounts = $this
//            ->getDoctrine()
//            ->getRepository(Account::class)
//            ->loadAccounts($accountsIds);
        $type = $request->query->get('operationType');
        $categoriesIds = $request->query->get('categoryId');

        $categories = array();
        if ($categoriesIds) {
            foreach ($categoriesIds as $categoryId) {
                $category = $em->getRepository(Category::class)->find($categoryId);
                if (!$category) {
                    continue;
                }
                array_push($categories, $category);
            }
        }

        $operations = array();

        foreach ($accountsIds as $accountId) {
            $account = $em->getRepository(Account::class)->find($accountId);
            if (!$account) {
                continue;
            }

            if ($account->getUser()) {
                if ($account->getUser()->getId() != $this->getUser()->getId()) {
                    continue;
                }
            } else if ($account->getOrganization()) {
                $organization = $em->getRepository(Organization::class)->find($account->getOrganization()->getId());
                if (!$organization) {
                    continue;
                }

                $membersIds = array();
                foreach ($organization->getMembers() as $member) {
                    array_push($membersIds, $member->getId());
                }
                if (!in_array($this->getUser()->getId(), $membersIds)) {
                    continue;
                }
            }

            foreach ($account->getOperations() as $operation) {
                if ($type) {
                    if ($operation->getType() == $type) {
                        array_push($operations, $operation);
                    }
                } else if ($categories) {
                    if (in_array($operation->getCategory(), $categories)) {
                        array_push($operations, $operation);
                    }
                } else {
                    array_push($operations, $operation);
                }
            }
        }

        return $this->json(['operations' => $operations], 200);
    }
}